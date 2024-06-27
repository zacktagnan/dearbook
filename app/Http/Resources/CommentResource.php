<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            // 'comment' => $this->comment,
            'comment' => $this->comment
                ?: '',
            // 'user' => new UserResource($this->user),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                // -> Si existe y no es NULL, se trata, sino, se devuelve NULL
                'avatar_url' => $this->user->avatar_path
                    ? Storage::url($this->user->avatar_path)
                    : null,
            ],
            // ------------------------------------------------------------------------------------------------
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->createdAtShortAbsDiffForHumans(),
            'created_at_large_format' => $this->createdAtWithLargeFormat(),
            'updated_at_large_format' => $this->updatedAtWithLargeFormat(),
        ];
    }
}
