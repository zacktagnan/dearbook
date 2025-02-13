<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Group;
use App\Libs\Utilities;
// use Inertia\Inertia;
// use Inertia\Response as InertiaResponse;
// o ...
use App\Models\GroupUser;
use App\Models\Attachment;
use App\Models\GroupAudit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Enums\GroupType;
use App\Http\Enums\GroupUserRole;
use Illuminate\Support\Facades\DB;
use App\Http\Enums\GroupAuditEvent;
use App\Http\Enums\GroupUserStatus;
use App\Services\AttachmentService;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\GroupResource;
use Illuminate\Http\RedirectResponse;
use App\Notifications\MemberRoleChange;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\GroupStoreRequest;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\GroupDeleteRequest;
use App\Http\Requests\GroupUpdateRequest;
use App\Http\Requests\InviteUsersRequest;
use App\Notifications\RequestToJoinGroup;
use App\Notifications\InvitationToJoinGroup;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MemberRemovedFromGroup;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\GroupCoverImageUpdateRequest;
use Inertia\{Inertia, Response as InertiaResponse};
use App\Notifications\InvitationToJoinGroupApproved;
use App\Notifications\RequestToJoinGroupApprovedOrNot;
use App\Http\Requests\GroupThumbnailImageUpdateRequest;
use App\Http\Requests\TransferRequest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class GroupController extends Controller
{
    public $fileDisk = 'public';
    protected $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function profile(Request $request, Group $group, ?string $tabIndex = 'conversation'): InertiaResponse|JsonResource
    {
        $group->load('currentGroupUser', 'user');

        $posts = Post::listedOnTimeLine(auth()->id(), false)
            ->leftJoin('groups AS g', 'g.pinned_post_id', 'posts.id')
            ->where('group_id', $group->id)
            ->orderBy('g.pinned_post_id', 'desc')
            ->orderBy('posts.created_at', 'desc')
            ->paginate(4); //20

        $posts = PostResource::collection($posts);

        if ($request->wantsJson()) {
            return $posts;
        }

        $requestsPending = $group->requestsPending()->orderBy('name')->get();

        $defaultIndex = match ($tabIndex) {
            'conversation' => 0,
            'info' => 1,
            'members' => 2,
            'photos' => 3,
            'requests' => 4,
        };

        $photos = Attachment::forUserOrGroup()->get();
        $photos = $this->attachmentService->filterAndTransform($photos, $group->id);

        $parentPageName = 'group_profile';

        return Inertia::render('Group/Index', [
            // 'status' => session('status'),
            'success' => session('success'),
            'group' => new GroupResource($group),
            'posts' => $posts,
            'after_comment_deleted' => session('after_comment_deleted'),
            'defaultIndex' => $defaultIndex,
            'requestsPending' => !$group->auto_approval
                ? UserResource::collection($requestsPending)
                : null,
            'photos' => $photos,
            'parent_page_name' => $parentPageName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupStoreRequest $request): HttpResponse
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

        GroupAudit::create([
            'group_id' => $group->id,
            'user' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
            ],
            'event' => GroupAuditEvent::CREATED->value,
            'details' => 'Grupo creado por ' . auth()->user()->name . '.',
        ]);

        // return back();
        return response(new GroupResource($group), Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupUpdateRequest $request, Group $group): RedirectResponse
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

    public function restoreAllSelected(Request $request): RedirectResponse
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

    public function restore(int $id, string $from): RedirectResponse
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

    private function updateImage(int $groupId, object $file, string $type): RedirectResponse|HttpResponse
    {
        $group = Group::findOrFail($groupId);

        if (!$group->isAdminOfTheGroup(auth()->id())) {
            return response("You don't have permission to UPDATE images of this group.", Response::HTTP_FORBIDDEN);
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

    public function acceptInvitation(string $token): RedirectResponse
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
            ->with('success', __('dearbook/group/notify.process_to_join.by_invitation.web', [
                'group_name' => $groupUser->group->name,
            ]));
    }

    public function join(Request $request, Group $group): RedirectResponse
    {
        GroupUser::create([
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::USER->value,
            'user_id' => $request->user()->id, // User que se captura dentro de inviteUsersRequest
            'group_id' => $group->id,
            'created_by' => $request->user()->id,
            // En este caso, el propio User es el que realiza el ingreso en el Group
        ]);

        return back()->with('success', __('dearbook/group/notify.process_to_join.by_auto_join.web', [
            'group_name' => $group->name,
        ]));
    }

    public function requestJoin(Request $request, Group $group): RedirectResponse
    {
        GroupUser::create([
            'status' => GroupUserStatus::PENDING->value,
            'role' => GroupUserRole::USER->value,
            'user_id' => $request->user()->id, // User que se captura dentro de inviteUsersRequest
            'group_id' => $group->id,
            'created_by' => $request->user()->id,
            // En este caso, el propio User que realiza el ingreso en el Group, quedando PENDING de ser aprobado
        ]);

        Notification::send($group->adminsGroup, new RequestToJoinGroup($group, $request->user()));

        return back()->with('success', __('dearbook/group/notify.process_to_join.by_request.web'));
    }

    public function requestApproveOrNot(Request $request, Group $group): RedirectResponse|HttpResponse
    {
        if (!$group->isAdminOfTheGroup(auth()->id())) {
            return response("You don't have permission to APPROVE member requests of this group.", Response::HTTP_FORBIDDEN);
        }

        $groupUser = GroupUser::where('user_id', $request->user_id)
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::PENDING->value)
            ->first();

        if ($groupUser) {
            if ($request->action === GroupUserStatus::APPROVED->value) {
                $groupUser->status = GroupUserStatus::APPROVED->value;
            } else if ($request->action === GroupUserStatus::REJECTED->value) {
                $groupUser->status = GroupUserStatus::REJECTED->value;
            }
            $groupUser->save();

            $groupUser->user->notify(new RequestToJoinGroupApprovedOrNot($group, $groupUser->user, $request->action));

            return back()->with('success', __('dearbook/group/notify.process_to_join.request_approved_or_not.web', [
                'user_name' => $groupUser->user->name,
                'status' => __('dearbook/group/notify.process_to_join.request_approved_or_not.decision.' . $request->action),
            ]));
        }

        return back();
    }

    public function changeRole(Request $request, Group $group): RedirectResponse|HttpResponse
    {
        if (!$group->isAdminOfTheGroup(auth()->id())) {
            return response("You don't have permission to CHANGE the Role member inside this group.", Response::HTTP_FORBIDDEN);
        }

        if ($group->isOwnerOfTheGroup($request->user_id)) {
            return response("You don't have permission to CHANGE the Role of the owner of the group.", Response::HTTP_FORBIDDEN);
        }

        /**
         * de querer validar el valor de $request->role según los valores establecidos del ENUM GroupUserRole:
         *      'role' => 'required', Rule::enum(GroupUserRole::class),
         */

        $groupUser = GroupUser::where('user_id', $request->user_id)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $groupUser->role = $request->role;
            $groupUser->save();

            $groupUser->user->notify(new MemberRoleChange($group, $groupUser->user, $request->role));

            return back()->with('success', __('dearbook/group/notify.member_role_change.web', [
                'role' => $request->role,
                'user_name' => $groupUser->user->name,
            ]));
        }

        return back();
    }

    public function removeMember(Request $request, Group $group): RedirectResponse|HttpResponse
    {
        if (!$group->isAdminOfTheGroup(auth()->id())) {
            return response("You don't have permission to DELETE the selected member from this group.", Response::HTTP_FORBIDDEN);
        }

        if ($group->isOwnerOfTheGroup($request->user_id)) {
            return response("You don't have permission to DELETE the owner of the group.", Response::HTTP_FORBIDDEN);
        }

        $groupUser = GroupUser::where('user_id', $request->user_id)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $memberDeleted = $groupUser->user;
            $groupUser->delete();

            $memberDeleted->notify(new MemberRemovedFromGroup($group, $memberDeleted));

            return back()->with('success', __('dearbook/group/notify.member_removed.web', [
                'user_name' => $groupUser->user->name,
            ]));
        }

        return back();
    }

    public function leave(Request $request, Group $group)
    {
        // dump('Usuario:Dejando el grupo...', auth()->user()->name);
        // dump('Dejando de ser miembro del grupo...', $group->name);
        // dd();
        if ($group->isOwnerOfTheGroup(auth()->id())) {
            // return response("The owner member cannot leave the group.", Response::HTTP_FORBIDDEN);
            abort(Response::HTTP_FORBIDDEN, 'The owner member cannot leave the group.');
        }

        $groupUser = GroupUser::where('user_id', auth()->id())
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $groupUser->delete();

            $message = __('dearbook/group/notify.member_leaving.web', [
                'group_name' => $group->name,
            ]);

            if ($request->from === 'user_profile') {
                return back()->with('success', $message);
            } else {
                if ($group->type === GroupType::PUBLIC->value) {
                    // volver a Group-Profile
                    return to_route('group.profile', [
                        'group' => $group->slug
                    ])->with('success', $message);
                } else if ($group->type === GroupType::PRIVATE->value) {
                    // volver a HOME
                    return to_route('home')->with('success', $message);
                }
            }
        }

        return back();
    }

    public function transferOwnership(TransferRequest $request, Group $group): RedirectResponse|HttpResponse
    {
        if (!$group->isOwnerOfTheGroup(auth()->id())) {
            // return response("Only the owner of the group have permission to TRANSFER ownership.", Response::HTTP_FORBIDDEN);
            abort(Response::HTTP_FORBIDDEN, 'Only the owner of the group have permission to TRANSFER ownership.');
        }

        if (!$group->isAdminOfTheGroup($request->user_id)) {
            // return response("The new OWNER have to be ADMIN of the group.", Response::HTTP_FORBIDDEN);
            abort(Response::HTTP_FORBIDDEN, 'The new OWNER have to be ADMIN of the group.');
        }

        DB::beginTransaction();

        try {
            $group->user_id = $request->user_id;
            $groupSaved = $group->save();

            if (!$groupSaved) {
                throw new \Exception('Failed to update group ownership.');
            }

            $groupAudited = GroupAudit::create([
                'group_id' => $group->id,
                'user' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                ],
                'event' => GroupAuditEvent::OWNERSHIP_TRANSFERRED->value,
                'details' => 'Grupo traspasado a ' . $group->user->name . '.',
            ]);

            if (!$groupAudited) {
                throw new \Exception('Failed to create group audit entry.');
            }

            DB::commit();

            return back()->with('success', __('dearbook/group/notify.ownership_transferred.web', [
                'user_name' => $group->user->name,
            ]));
        } catch (\Exception $e) {
            DB::rollBack();

            // Registrar el error para depuración
            Log::error('Error al transferir la propiedad del grupo: ' . $e->getMessage());

            return back()->withErrors('Hubo un problema al transferir la propiedad del grupo.');
        }
    }
}
