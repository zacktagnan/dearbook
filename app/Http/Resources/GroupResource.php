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
            $creator = $this->creatorFromAudit->user;

            $creatorUserExists = User::find($creator->id);

            if ($creatorUserExists) {
                $creatorUser = $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.user_id', 'g_u.group_id', 'g_u.created_at'])
                    ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
                    ->where('g_u.group_id', $this->id)
                    ->where('g_u.user_id', '=', $creatorUserExists->id) // Excluimos al CREATOR
                    ->first();
                $dataResource['creator'] = new GroupUserResource($creatorUser);
                // $dataResource['creator'] = GroupUserResource::collection(
                //     $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.user_id', 'g_u.group_id', 'g_u.created_at'])
                //         ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
                //         ->where('g_u.group_id', $this->id)
                //         ->where('g_u.user_id', '=', $creatorUserExists->id) // Excluimos al CREATOR
                //         ->get()
                // );
                $dataResource['creator_account_deleted'] = false; // El creador aún existe
            } else {
                // Si el creador no existe (cuenta eliminada), cargar los datos desde 'group_audits'
                $dataResource['creator'] = [
                    'id' => $creator->id,
                    'name' => $creator->name,
                    'avatar_url' => Utilities::$defaultAvatarImage,
                ];
                $dataResource['creator_account_deleted'] = true; // Indicar que el creador ha eliminado su cuenta
            }

            // Incluir al propietario actual del grupo (almacenado en 'groups.user_id')
            $ownerUser = $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.user_id', 'g_u.group_id', 'g_u.created_at'])
                ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
                ->where('g_u.group_id', $this->id)
                ->where('g_u.user_id', '=', $this->user_id)
                ->first();
            $dataResource['owner'] = new GroupUserResource($ownerUser);
            // $dataResource['owner'] = GroupUserResource::collection(
            //     $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.user_id', 'g_u.group_id', 'g_u.created_at'])
            //         ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
            //         ->where('g_u.group_id', $this->id)
            //         ->where('g_u.user_id', '=', $this->user_id)
            //         ->get()
            // );

            // 'total_group_user' => count($this->allGroupUser), //OK
            // 'all_group_users' => UserResource::collection($this->allGroupUser),
            // Esta relación da todos los miembros sea cuál sea su STATUS (APPROVED, PENDING, REJECTED o el que sea)
            // 'all_group_users' => $this->allGroupUser,
            // ---------------------------------------------------------------------------------------------------------
            // Pero, con el uso de MEMBERS, solamente, se obtienen los que ya tienen el STATUS de APPROVED
            // ---------------------------------------------------------------------------------------------------------
            $dataResource['total_group_user'] = count($this->members);

            $members = $this->members()->select(['users.*', 'g_u.role', 'g_u.status', 'g_u.user_id', 'g_u.group_id', 'g_u.created_at'])
                ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
                ->where('g_u.group_id', $this->id);

            if ($creator->id !== $this->user_id) {
                $members = $members
                    ->where('g_u.user_id', '!=', $creatorUserExists->id) // Excluimos al CREATOR
                    ->where('g_u.user_id', '!=', $this->user_id); // Excluimos al OWNER
            }

            $members = $members
                ->orderByRaw('CASE WHEN users.id = ? THEN 0 ELSE 1 END', [$this->user_id])
                ->orderBy('g_u.role')
                ->orderBy('users.name')
                ->get();

            $dataResource['all_group_users'] = GroupUserResource::collection($members);

            // $dataResource['all_group_users'] = GroupUserResource::collection(
            //     $this->members()
            //         ->select([
            //             'users.*',
            //             'g_u.role',
            //             'g_u.status',
            //             'g_u.user_id',
            //             'g_u.group_id',
            //             'g_u.created_at',
            //             // Extraer el nombre del creador desde la columna JSON 'user'
            //             DB::raw("JSON_UNQUOTE(JSON_EXTRACT(ga.user, '$.id')) as creator_id"),
            //             DB::raw("JSON_UNQUOTE(JSON_EXTRACT(ga.user, '$.name')) as creator_name"),
            //             'ga.created_at as creator_date'
            //         ])
            //         ->join('group_users AS g_u', 'g_u.user_id', 'users.id')
            //         ->leftJoin('group_audits AS ga', function ($join) {
            //             $join->on('ga.group_id', '=', 'g_u.group_id')
            //                 ->where('ga.event', '=', 'created');
            //         })
            //         ->where('g_u.group_id', $this->id)
            //         // Condición para agregar el LEFT JOIN solo si el creador no es miembro
            //         ->when(
            //             !$this->members()->where('user_id', $this->creator_id)->exists(),
            //             function ($query) {
            //                 return $query->leftJoin('group_audits AS ga', 'ga.group_id', '=', 'g_u.group_id')
            //                     ->where('ga.event', 'created');
            //             }
            //         )
            //         ->orderByRaw('CASE WHEN users.id = ? THEN 0 ELSE 1 END', [$this->user_id])
            //         ->orderBy('g_u.role')
            //         ->orderBy('users.name')
            //         ->get()
            // );
        }

        return $dataResource;
    }
}
