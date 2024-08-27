<?php

namespace App\Traits;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

trait UsersThatCommented
{
    public function allUsersThatCommented(int $id): array
    {
        $post = Post::withArchived()->withTrashed()->find($id);
        // -> Para solo el dato del ID del User
        // $comments = $post->comments()->where('user_id', '<>', auth()->id())
        //     ->distinct()
        //     ->get(['user_id']);

        // -> Para el dato del ID del User y el TOT de Comment
        $comments = $post->comments()->select('user_id')->selectRaw('COUNT(*) AS user_total_comments')
            ->where('user_id', '<>', auth()->id())
            ->groupBy('user_id')->get();

        $usersThatCommentedOnPost = $this->getCommentUsers($comments);

        return $usersThatCommentedOnPost;
    }

    private function getCommentUsers(Collection $comments): array
    {
        $usersThatCommentedOnPost = [];
        foreach ($comments as $comment) {
            // $usersThatCommentedOnPost[] = $comment->user->name;
            $usersThatCommentedOnPost[] = [
                'name' => $comment->user->name,
                'user_total_comments' => $comment->user_total_comments,
            ];
        }

        return $usersThatCommentedOnPost;
    }
}
