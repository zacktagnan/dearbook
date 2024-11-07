<?php

namespace App\Services;

use App\Http\Resources\AttachmentResource;

class AttachmentService
{

    public function filterAndTransform($attachments, $groupId = null)
    {
        return $attachments->filter(function ($attachment) use ($groupId) {
            if ($attachment->attachmentable_type === 'App\Models\Post') {
                $post = $attachment->attachmentable;
                return $groupId
                    ? $post && is_null($post->deleted_at) && $post->group_id === $groupId
                    : $post && is_null($post->deleted_at);
            }

            if ($attachment->attachmentable_type === 'App\Models\Comment') {
                $comment = $attachment->attachmentable;
                $post = $comment->post;
                return $groupId
                    ? $post && is_null($post->deleted_at) && $post->group_id === $groupId
                    : $post && is_null($post->deleted_at);
            }

            // Si no es ni un Post ni un Comment, lo mantenemos
            return true;
        })->map(function ($attachment) {
            return new AttachmentResource($attachment, true);
        });
    }
}
