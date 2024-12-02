<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Group;
use Inertia\Response;
use App\Models\Follower;
use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\StorageManagement;
use App\Services\AttachmentService;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\FollowResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\CoverImageUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\AvatarImageUpdateRequest;
use App\Http\Resources\GroupResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProfileController extends Controller
{
    use StorageManagement;

    public $fileDisk = 'public';
    protected $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function index(Request $request, User $user): Response|AnonymousResourceCollection
    {
        $isCurrentUserFollower = false;
        if (!Auth::guest()) {
            $isCurrentUserFollower = Follower::where('followed_id', $user->id)->where('follower_id', auth()->id())->exists();
        }

        $totalOfFollowers = Follower::where('followed_id', $user->id)->count();

        $posts = Post::listedOnTimeLine(auth()->id(), true)
            ->where('user_id', $user->id)
            ->paginate(5); //20

        $posts = PostResource::collection($posts);

        if ($request->wantsJson()) {
            return $posts;
        }

        $groupsOwned = Group::with('currentGroupUser')
            ->where('groups.user_id', $user->id)
            ->orderBy('groups.name')
            ->get();

        $groupsJoined = Group::query()
            ->with('currentGroupUser')
            ->select(['groups.*', 'g_u.role'])
            ->join('group_users AS g_u', 'g_u.group_id', 'groups.id')
            ->where('g_u.user_id', $user->id)
            ->where('g_u.status', GroupUserStatus::APPROVED->value)
            ->where('groups.user_id', '<>', $user->id)
            ->orderBy('groups.name')
            ->get();

        $followers = $user->followers;
        $followings = $user->followings;

        $followers->transform(function ($follower) {
            // $follower->created_at_human = Carbon::parse($follower->created_at)->diffForHumans();
            $follower->since_date = __('dearbook/follower/list.inside_profile.since_date_text', [
                'since_date' => Carbon::parse($follower->pivot->created_at)->diffForHumans(),
            ]);
            return $follower;
        });
        $followings->transform(function ($following) {
            // $following->created_at_human = Carbon::parse($following->created_at)->diffForHumans();
            $following->since_date = __('dearbook/following/list.inside_profile.since_date_text', [
                'since_date' => Carbon::parse($following->pivot->created_at)->diffForHumans(),
            ]);
            return $following;
        });

        $photos = Attachment::forUserOrGroup($user->id)->get();
        $photos = $this->attachmentService->filterAndTransform($photos);

        $tabIndex = 'posts_tab';
        $defaultIndex = match ($tabIndex) {
            'posts_tab' => 0,
            'about' => 1,
            'followers' => 2,
            'followings' => 3,
            'photos' => 4,
        };

        return Inertia::render('Profile/Index', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'success' => session('success'),
            'user' => new UserResource($user),
            'posts' => $posts,
            'groupsOwned' => GroupResource::collection($groupsOwned),
            'groupsJoined' => GroupResource::collection($groupsJoined),
            'after_comment_deleted' => session('after_comment_deleted'),
            'defaultIndex' => $defaultIndex,
            'isCurrentUserFollower' => $isCurrentUserFollower,
            'totalOfFollowers' => $totalOfFollowers,
            'followers' => FollowResource::collection($followers),
            'followings' => FollowResource::collection($followings),
            'photos' => $photos,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.index', [
            'user' => $request->user()->username,
        ])->with('success', 'Your data has been updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // public function updateImages(Request $request): RedirectResponse
    // {
    //     $data = $request->validate([
    //         'cover' => 'nullable|image|mimes:png,jpg',
    //         // 'cover' => 'nullable|image',
    //         'avatar' => 'nullable|image',
    //     ]);

    //     /** @var \App\Models\User $user **/
    //     // $user = auth()->user();
    //     // dd('DATA', $data, 'USER', $user);
    //     // o
    //     $user = $request->user();

    //     $cover = $data['cover'] ?? null;
    //     $avatar = $data['avatar'] ?? null;

    //     // dd('EXTENSION', $cover->extension());
    //     $coverFileName = 'cover-' . time() . '.' . $cover->extension();

    //     $successMsg = '';

    //     if ($cover) {
    //         $previousCoverImage = $user->cover_path;
    //         $path = $cover->storeAs('user-' . $user->id, $coverFileName, $this->fileDisk);
    //         $user->update([
    //             'cover_path' => $path,
    //         ]);

    //         $this->deleteImageFile($previousCoverImage);

    //         $successMsg = 'Cover image has been updated successfully!';
    //     }

    //     return redirect()->route('profile.index', [
    //         'user' => $user->username,
    //     ])->with('success', $successMsg);
    // }

    public function updateCoverImage(CoverImageUpdateRequest $request): void
    {
        // $data = $request->validate([
        //     'cover' => 'image|mimes:png,jpg|dimensions:min_width=1260,min_height=330',
        // ]);
        // $file = $data['cover'];
        // -----------------------------------------------------------------------------------
        // $file = $request->cover;
        // $user = $request->user();

        $this->updateImage($request->user(), $request->cover, 'cover');
    }

    public function updateAvatarImage(AvatarImageUpdateRequest $request): void
    {
        // $data = $request->validate([
        //     'avatar' => 'image|mimes:png,jpg|dimensions:min_width=168,min_height=168',
        // ]);
        // $file = $data['avatar'];
        // $user = $request->user();

        $this->updateImage($request->user(), $request->avatar, 'avatar');
    }

    private function updateImage(User $user, object $file, string $type): RedirectResponse
    {
        if ($type === 'cover') {
            $previousImageFile = $user->cover_path;
        } else if ($type === 'avatar') {
            $previousImageFile = $user->avatar_path;
        }

        $fileName = $type . '-' . time() . '.' . $file->extension();
        $successMsg = Str::ucfirst($type) . ' image has been updated successfully!';

        $path = $file->storeAs('user-' . $user->id, $fileName, $this->fileDisk);
        $user->update([
            $type . '_path' => $path,
        ]);

        if (isset($previousImageFile)) {
            $this->deleteImageFile($previousImageFile);
        }

        return redirect()->route('profile.index', [
            'user' => $user->username,
        ])->with('success', $successMsg);
    }

    public function deleteImageFile(string $imageFile): void
    {
        if (Storage::disk($this->fileDisk)->exists($imageFile)) {
            Storage::disk($this->fileDisk)->delete($imageFile);
        }
    }
}
