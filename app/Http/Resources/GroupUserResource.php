<?php

namespace App\Http\Resources;

use App\Libs\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupUserResource extends JsonResource
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
            'username' => $this->username,
            // -> Si existe y no es NULL, se trata, sino, se devuelve NULL o, directamente, la imagen predeterminada
            'avatar_url' => $this->avatar_path
                ? Storage::url($this->avatar_path)
                : Utilities::$defaultAvatarImage,
            'role' => $this->role,
            'status' => $this->status,
            'group_id' => $this->group_id,
        ];
    }
}
