<?php

namespace App\Traits;

use App\Http\Resources\CommentResource;

trait CommentsTree
{
    /**
     * Undocumented function
     *
     * @param \App\Models\Comment[] $comments
     * @param integer|null $parentId
     * @return array
     */
    public static function convertCommentsIntoTree($comments, int $parentId = null): array
    {
        $commentTree = [];

        foreach ($comments as $comment) {
            if ($comment->parent_id === $parentId) {
                $children = self::convertCommentsIntoTree($comments, $comment->id);

                // if ($children) {
                //     $comment->setAttribute('children', $children);
                // }
                //o, habiendo establecido esta propiedad en el modelo de Comment...
                $comment->childCommentsArr = $children;

                $comment->totalOfComments = collect($children)->sum('totalOfComments') + count($children);
                $comment->totalOfDirectChildComments = count($children);

                $commentTree[] = new CommentResource($comment);
            }
        }

        return $commentTree;
    }

    /**
     * Undocumented function
     *
     * @param \App\Models\Comment[] $comments
     * @param integer|null $parentId
     * @return array
     */
    public static function convertLatestCommentsIntoTree($comments, int $parentId = null): array
    {
        $commentTree = [];

        $commentTree = self::convertCommentsIntoTree($comments, $parentId);

        if (count($commentTree) > 0) {
            $commentTree = array_reverse($commentTree);
            $latestCommentTree = [];
            $latestCommentTree[] = $commentTree[0];

            return $latestCommentTree;
        }

        return $commentTree;
    }
}
