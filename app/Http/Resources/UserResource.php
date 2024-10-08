<?php

namespace App\Http\Resources;

use App\Libs\Utilities;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'username' => $this->username,
            // // -> Si existe y no es NULL, se trata, sino, se devuelve NULL
            // 'cover_url' => $this->cover_path
            //     ? Storage::url($this->cover_path)
            //     : null,
            // ----------------------------------------------------------------------------------
            // Ahora, en vez de NULL, se pasa la imagen predeterminada por defecto desde aquí
            'cover_url' => $this->cover_path
                ? Storage::url($this->cover_path)
                : Utilities::$defaultCoverImage,
            'avatar_url' => $this->avatar_path
                ? Storage::url($this->avatar_path)
                : Utilities::$defaultAvatarImage,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
