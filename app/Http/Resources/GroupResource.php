<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
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
            // -> Si existe y no es NULL, se trata, sino, se devuelve NULL
            'cover_url' => $this->cover_path
                ? Storage::url($this->cover_path)
                : null,
            'thumbnail_url' => $this->thumbnail_path
                ? Storage::url($this->thumbnail_path)
                : null,
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
                    : null,
            ],

            'total_group_user' => count($this->allGroupUser),

            // 'deleted_by' => $this->deleted_by,
            // 'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->createdAtWithFormat(),
        ];
    }
}
