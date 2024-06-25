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
        ], Response::HTTP_CREATED);
    }

    public function allCommentsUsers(Post $post): array
    {
        $comments = $post->comments()->where('user_id', '<>', auth()->id())
            ->distinct()
            ->get(['user_id']);

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
            ];
        }

        return $usersThatCommentedOnPost;
    }
}
