<?php

namespace App\Http\Resources;

use App\Libs\Utilities;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function __construct($resource, public bool $includeMembers = true)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataResource = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,

            // 'status' => $this->status,
            // 'role' => $this->role,
            'status' => $this->currentGroupUser?->status,
            'role' => $this->currentGroupUser?->role,

            // 'cover_path' => $this->cover_path,
            // 'thumbnail_path' => $this->thumbnail_path,

            // 'thumbnail_url' => 'https://picsum.photos/100',
            // -> Si existe y no es NULL, se trata, sino, se devuelve NULL o, directamente, la imagen predeterminada
            'cover_url' => $this->cover_path
                ? Storage::url($this->cover_path)
                : Utilities::$defaultGroupCoverImage,
            'thumbnail_url' => $this->thumbnail_path
                ? Storage::url($this->thumbnail_path)
                : Utilities::$defaultGroupThumbnailImage,
            'auto_approval' => $this->auto_approval,
            // 'about' => $this->about,
            'short_description' => Str::words($this->about, 11, '...'),
            'full_description' => $this->about,

            // 'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                'avatar_url' => $this->user->avatar_path
                    ? Storage::url($this->user->avatar_path)
                    : Utilities::$defaultAvatarImage,
            ],

            'pinned_post_id' => $this->pinned_post_id,

            // 'deleted_by' => $this->deleted_by,
            // 'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->createdAtWithFormat(),
        ];

        if ($this->includeMembers) {
            $creatorFromAudit = $this->creatorFromAudit;

            $creatorUserExists = User::find($creatorFromAudit->user->id);

            if ($creatorUserExists) {
                $creatorUserIsMember = $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.user_id', 'g_u.group_id', 'g_u.created_at'])
                    ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
                    ->where('g_u.group_id', $this->id)
                    ->where('g_u.user_id', '=', $creatorUserExists->id)
                    ->first();

                if ($creatorUserIsMember) {
                    $dataResource['creator'] = new GroupUserResource($creatorUserIsMember);
                    $dataResource['creator_is_member'] = true;
                } else {
                    $dataResource['creator'] = new UserCreatorNotMemberButAccountResource($creatorUserExists, $creatorFromAudit->created_at);
                    $dataResource['creator_is_member'] = false;
                }
            } else {
                // Si el creador no existe (cuenta eliminada), cargar los datos desde 'group_audits'
                $dataResource['creator'] = [
                    'id' => $creatorFromAudit->user->id,
                    'name' => $creatorFromAudit->user->name,
                    'avatar_url' => Utilities::$defaultAvatarImage,
                    'joining_date' => __('dearbook/group/list.members.creating_date_text', [
                        'creating_date' => $creatorFromAudit->created_at->diffForHumans(),
                    ]),
                ];
            }

            $ownerUser = $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.user_id', 'g_u.group_id', 'g_u.created_at'])
                ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
                ->where('g_u.group_id', $this->id)
                ->where('g_u.user_id', '=', $this->user_id)
                ->first();
            $dataResource['owner'] = new GroupUserResource($ownerUser);

            $dataResource['total_group_user'] = count($this->members);

            $members = $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.user_id', 'g_u.group_id', 'g_u.created_at'])
                ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
                ->where('g_u.group_id', $this->id);

            if ($creatorFromAudit->user->id !== $this->user_id) {
                $members = $members
                    ->where('g_u.user_id', '!=', $creatorUserExists ? $creatorUserExists->id : $creatorFromAudit->user->id) // Excluimos al CREATOR
                    ->where('g_u.user_id', '!=', $this->user_id); // Excluimos al OWNER
            }

            $members = $members
                ->orderByRaw('CASE WHEN users.id = ? THEN 0 ELSE 1 END', [$this->user_id])
                ->orderBy('g_u.role')
                ->orderBy('users.name')
                ->get();

            $dataResource['all_group_users'] = GroupUserResource::collection($members);
        }

        return $dataResource;
    }
}
