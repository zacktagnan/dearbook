<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Support\Str;
use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\GroupCoverImageUpdateRequest;
use App\Http\Requests\GroupDeleteRequest;
use App\Http\Requests\GroupStoreRequest;
use App\Http\Requests\GroupThumbnailImageUpdateRequest;
use App\Http\Requests\GroupUpdateRequest;

class GroupController extends Controller
{
    public $fileDisk = 'public';

    /**
     * Display a listing of the resource.
     */
    public function profile(Group $group, ?string $tabIndex = 'conversation')
    {
        // dump('num', $num);
        // dd();
        $group->load('currentGroupUser');

        $defaultIndex = match ($tabIndex) {
            'conversation' => 0,
            'info' => 1,
            'followers' => 2,
            'followed' => 3,
            'photos' => 4,
        };

        return Inertia::render('Group/Index', [
            // 'status' => session('status'),
            'success' => session('success'),
            'group' => new GroupResource($group),
            'defaultIndex' => $defaultIndex,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupStoreRequest $request)
    {
        $request->merge(['user_id' => $request->user()->id]);
        $group = Group::create($request->all());

        $groupUser = [
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::ADMIN->value,
            'user_id' => $request->user()->id,
            'group_id' => $group->id,
            'created_by' => $request->user()->id,
        ];

        GroupUser::create($groupUser);

        $group->status = $groupUser['status'];
        $group->role = $groupUser['role'];

        // return back();
        return response(new GroupResource($group), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupUpdateRequest $request, Group $group)
    {
        $group->update($request->all());

        return Redirect::route('group.profile', [
            'group' => $group->slug,
        ])->with('success', 'Your group data has been updated successfully!');
    }

    /**
     * Delete the user's group.
     */
    public function destroy(GroupDeleteRequest $request, Group $group): RedirectResponse
    {
        $group->update([
            'deleted_by' => $request->user()->id,
        ]);

        $group->delete();

        return Redirect::to('/');
    }

    public function updateCoverImage(GroupCoverImageUpdateRequest $request): void
    {
        $this->updateImage($request->group_id, $request->cover, 'cover');
    }

    public function updateThumbnailImage(GroupThumbnailImageUpdateRequest $request): void
    {
        $this->updateImage($request->group_id, $request->thumbnail, 'thumbnail');
    }

    private function updateImage(int $groupId, object $file, string $type): RedirectResponse
    {
        $group = Group::findOrFail($groupId);

        if (!$group->isAdminOfTheGroup(auth()->id())) {
            return response("You don't have permission to UPDATE images of this group", Response::HTTP_FORBIDDEN);
        }

        if ($type === 'cover') {
            $previousImageFile = $group->cover_path;
        } else if ($type === 'thumbnail') {
            $previousImageFile = $group->thumbnail_path;
        }

        $fileName = $type . '-' . time() . '.' . $file->extension();
        $successMsg = Str::ucfirst($type) . ' image has been updated successfully!';

        $path = $file->storeAs('group-' . $group->id, $fileName, $this->fileDisk);
        $group->update([
            $type . '_path' => $path,
        ]);

        if (isset($previousImageFile)) {
            $this->deleteImageFile($previousImageFile);
        }

        return redirect()->route('group.profile', [
            'group' => $group->slug,
        ])->with('success', $successMsg);
    }

    public function deleteImageFile(string $imageFile): void
    {
        if (Storage::disk($this->fileDisk)->exists($imageFile)) {
            Storage::disk($this->fileDisk)->delete($imageFile);
        }
    }
}
