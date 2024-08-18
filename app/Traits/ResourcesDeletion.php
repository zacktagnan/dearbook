<?php

namespace App\Traits;

use App\Models\Post;
use App\Libs\Utilities;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;

trait ResourcesDeletion
{
    use StorageManagement;

    public function processingDeleteResources(Model $model, int $id): void
    {
        if ($model instanceof Post) {
            $model = $model::withTrashed()->find($id);
            $this->applyDeleteResourcesForPost($model);
        } else if ($model instanceof Comment) {
            $model = $model::find($id);
            $this->applyDeleteResourcesForComment($model);
        }
    }

    private function applyDeleteResourcesForPost(Post $post): void
    {
        if ($post->attachments()->count() > 0) {
            foreach ($post->attachments()->get() as $attachmentToDelete) {
                $attachmentToDelete->delete();
            }
            $this->deleteFolderIfEmpty(Utilities::$postRootFolderBaseName . $post->id);

            $post->attachments()->delete();
        }
        if ($post->comments()->count() > 0) {
            foreach ($post->comments()->get() as $commentToDelete) {
                $this->applyDeleteResourcesForComment($commentToDelete);
                $commentToDelete->delete();
            }
        }
        if ($post->reactions()->count() > 0) {
            $post->reactions()->delete();
        }
    }

    private function applyDeleteResourcesForComment(Comment $comment): void
    {
        if ($comment->attachments()->count() > 0) {
            foreach ($comment->attachments()->get() as $attachmentToDelete) {
                $attachmentToDelete->delete();
            }
            $this->deleteFolderIfEmpty(Utilities::$commentRootFolderBaseName . $comment->id);

            $comment->attachments()->delete();
        }
        if ($comment->reactions()->count() > 0) {
            $comment->reactions()->delete();
        }
    }
}
