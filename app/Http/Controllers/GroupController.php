<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\GroupStoreRequest;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\GroupDeleteRequest;
use App\Http\Requests\GroupUpdateRequest;
use App\Http\Requests\InviteUsersRequest;
use App\Notifications\InvitationToJoinGroup;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\GroupCoverImageUpdateRequest;
use App\Notifications\InvitationToJoinGroupApproved;
use App\Http\Requests\GroupThumbnailImageUpdateRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class GroupController extends Controller
{
    public $fileDisk = 'public';

    /**
     * Display a listing of the resource.
     */
    public function profile(Group $group, ?string $tabIndex = 'conversation')
    {
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

    public function restoreAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            $groupsToRestore = match ($request->from) {
                // 'archive' => Group::onlyArchived()
                //     ->whereIn('id', $request->checked_ids)
                //     ->get(),
                'trash' => Group::onlyTrashed()
                    ->whereIn('id', $request->checked_ids)
                    ->get(),
            };

            foreach ($groupsToRestore as $group) {
                $this->applyRestoration($group, $request->from);
            }
        }

        return back();
    }

    public function restore(int $id, string $from)
    {
        $group = match ($from) {
            // 'archive' => Group::onlyArchived()->findOrFail($id),
            'trash' => Group::onlyTrashed()->findOrFail($id),
        };
        $this->applyRestoration($group, $from);

        return back();
    }

    public function applyRestoration(Group $group, string $from)
    {
        if ($group->user_id !== auth()->id()) {
            return response("You don't have permission to PROCESS the Restoration of this group", Response::HTTP_FORBIDDEN);
        }
        match ($from) {
            // 'archive' => $group->unArchive(),
            'trash' => $group->restore(),
        };

        if ($from === 'trash') {
            $group->update([
                'deleted_by' => null,
            ]);
        }
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

    public function inviteUsers(InviteUsersRequest $request, Group $group): RedirectResponse
    {
        // Siempre que sea un groupUser con STATUS a PENDING...
        if ($request->groupUser) {
            $request->groupUser->delete();
        }

        $expirationTimeInHours = 24;
        $token = Str::random(256);

        GroupUser::create([
            'status' => GroupUserStatus::PENDING->value,
            'role' => GroupUserRole::USER->value,
            'token' => $token,
            'token_expires_at' => Carbon::now()->addHours($expirationTimeInHours),
            'user_id' => $request->user->id, // User que se captura dentro de inviteUsersRequest
            'group_id' => $group->id,
            'created_by' => $request->user()->id, // User que ejecuta la petición, es decir, el ADMIN autenticado
        ]);

        $request->user->notify(new InvitationToJoinGroup($group, $request->user, $expirationTimeInHours, $token));

        return back()->with('success', 'El usuario ' . $request->user->username . ' ha sido invitado a unirse al grupo "' . $group->name . '".');
    }

    public function acceptInvitation(string $token)
    {
        // $groupUser = GroupUser::where('token', '43210')
        $groupUser = GroupUser::where('token', $token)
            ->first();

        $status = [];
        $status['code'] = 400;
        $status['title'] = '';

        if (!$groupUser) {

            // throw new BadRequestException('Token not valid');

            // return Inertia::render('Errors/other', [
            //     'posts' => $posts,
            //     'groups' => GroupResource::collection($groups),
            // ]);
            $status['title'] = __('error.other.title_base', [
                'code' => $status['code'],
                'text' => __('error.other.type.token_not_valid.title'),
            ]);
            $status['message'] = __('error.other.type.token_not_valid.message');
        } else if ($groupUser->token_uses_at || $groupUser->status === GroupUserStatus::APPROVED->value) {
            $status['title'] = __('error.other.title_base', [
                'code' => $status['code'],
                'text' => __('error.other.type.token_used.title'),
            ]);
            $status['message'] = __('error.other.type.token_used.message');
        } else if ($groupUser->token_expires_at < Carbon::now()) {
            $status['title'] = __('error.other.title_base', [
                'code' => $status['code'],
                'text' => __('error.other.type.token_expired.title'),
            ]);
            $status['message'] = __('error.other.type.token_expired.message');
        }

        // return Inertia::render('Errors/other', [
        //     'status' => $status,
        // ]);
        if ($status['title'] !== '') {
            return Inertia::render('Errors/OtherError', compact('status'));
        }

        $groupUser->update([
            'status' => GroupUserStatus::APPROVED->value,
            'token_uses_at' => Carbon::now(),
        ]);

        $groupUser->userAdmin->notify(new InvitationToJoinGroupApproved($groupUser->group, $groupUser->user));

        return redirect(route('group.profile', $groupUser->group))
            ->with('success', __('dearbook/group.process_to_join.by_invitation.notification', [
                'group_name' => $groupUser->group->name,
            ]));
    }

    public function join(Request $request, Group $group)
    {
        // $request->user();

        GroupUser::create([
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::USER->value,
            'user_id' => $request->user()->id, // User que se captura dentro de inviteUsersRequest
            'group_id' => $group->id,
            'created_by' => $request->user()->id,
            // En este caso, el propio User es el que realiza el ingreso en el Group
        ]);

        return back()->with('success', __('dearbook/group.process_to_join.by_auto_join.notification', [
            'group_name' => $group->name,
        ]));
    }

    public function requestJoin(Request $request, Group $group)
    {
        // $request->user();

        GroupUser::create([
            'status' => GroupUserStatus::PENDING->value,
            'role' => GroupUserRole::USER->value,
            'user_id' => $request->user()->id, // User que se captura dentro de inviteUsersRequest
            'group_id' => $group->id,
            'created_by' => $request->user()->id,
            // En este caso, el propio User que realiza el ingreso en el Group, quedando PENDING de ser aprobado
        ]);
    }
}
