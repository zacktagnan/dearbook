<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isNull;

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
            // -> Si existe y no es NULL, se trata, sino, se devuelve NULL
            'cover_url' => $this->cover_path
                ? Storage::url($this->cover_path)
                : null,
            'avatar_url' => $this->avatar_path
                ? Storage::url($this->avatar_path)
                : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
