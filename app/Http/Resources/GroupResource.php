<?php

namespace App\Http\Resources;

use App\Libs\Utilities;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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

            // 'deleted_by' => $this->deleted_by,
            // 'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->createdAtWithFormat(),
        ];

        if ($this->includeMembers) {
            // 'total_group_user' => count($this->allGroupUser), //OK
            // 'all_group_users' => UserResource::collection($this->allGroupUser),
            // Esta relación da todos los miembros sea cuál sea su STATUS (APPROVED, PENDING, REJECTED o el que sea)
            // 'all_group_users' => $this->allGroupUser,
            // ---------------------------------------------------------------------------------------------------------
            // Pero, con el uso de MEMBERS, solamente, se obtienen los que ya tienen el STATUS de APPROVED
            // ---------------------------------------------------------------------------------------------------------
            $dataResource['total_group_user'] = count($this->members);
            $dataResource['all_group_users'] = GroupUserResource::collection(
                $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.group_id'])
                    ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
                    ->where('g_u.group_id', $this->id)
                    ->orderBy('g_u.role')
                    ->orderBy('users.name')
                    ->get()
            );
        }

        return $dataResource;
    }
}
