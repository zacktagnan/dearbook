<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'body' => $this->body
                ?: '',
            'user' => new UserResource($this->user),

            // Cargando a través del nombre de la relación con Post,
            'group' => $this->group,
            'attachments' => $this->attachments,

            // 'created_at' => $this->created_at->format(config('app.format.' . app()->getLocale() . '.datetime')),
            // 'updated_at' => $this->updated_at->format(config('app.format.' . app()->getLocale() . '.datetime.basic')),
            // ------------------------------------------------------------------------------------------------
            // 'created_at' => $this->createdAtWithFormat(),
            // 'created_at' => $this->created_at->translatedFormat('j F Y'),
            // ------------------------------------------------------------------------------------------------
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->createdAtWithFormat(),
            'created_at_large_format' => $this->createdAtWithLargeFormat(),
            'updated_at_large_format' => $this->updatedAtWithLargeFormat(),
        ];
    }
}
