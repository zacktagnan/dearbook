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

            'status' => $this->status,
            'role' => $this->role,

            // 'cover_path' => $this->cover_path,
            // 'thumbnail_path' => $this->thumbnail_path,

            // 'thumbnail_url' => 'https://picsum.photos/100',
            // -> Si existe y no es NULL, se trata, sino, se devuelve NULL
            'thumbnail_url' => $this->thumbnail_path
                ? Storage::url($this->thumbnail_path)
                : null,
            'auto_approval' => $this->auto_approval,
            // 'about' => $this->about,
            'description' => Str::words($this->about, 11, '...'),
            'user_id' => $this->user_id,
            // 'deleted_by' => $this->deleted_by,
            // 'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
