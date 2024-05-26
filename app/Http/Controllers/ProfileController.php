<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvatarImageUpdateRequest;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\CoverImageUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    public $fileDisk = 'public';

    public function index(User $user): Response
    {
        // dd($user);
        return Inertia::render('Profile/Index', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'success' => session('success'),
            // 'user' => $user,
            'user' => new UserResource($user),
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

        return Redirect::route('profile.edit');
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
