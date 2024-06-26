<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\CommentStoreRequest;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function store(CommentStoreRequest $request, Post $post)
    {
        Comment::create([
            'post_id' => $post->id,
            'comment' => $request->comment,
            'user_id' => auth()->id(),
        ]);
        $hasComment = true;

        $comments = $post->comments()->count();

        return response()->json([
            'total_of_comments' => $comments,
            'current_user_has_comment' => $hasComment,
            'current_user_total_of_comments' => $post->currentUserComments()->count(),
        ], Response::HTTP_CREATED);
    }

    public function allCommentsUsers(Post $post): array
    {
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
