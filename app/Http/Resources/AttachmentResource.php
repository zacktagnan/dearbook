<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function __construct($resource, public bool $includeAttachmentableData = false)
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
            'mime' => $this->mime,
            'size' => $this->size,
            'url' => $this->path
                ? Storage::url($this->path)
                : null,
            'created_at' => $this->created_at,
        ];

        if ($this->includeAttachmentableData) {
            $dataResource['attachmentable'] = $this->attachmentable;
            $dataResource['post_user_username'] = $this->attachmentable_type === 'App\Models\Post'
                ? $this->attachmentable->user->username
                : (
                    $this->attachmentable_type === 'App\Models\Comment'
                    ? $this->attachmentable->post->user->username
                    : null
                );
        }

        return $dataResource;
    }
}
