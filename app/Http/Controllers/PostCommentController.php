<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use Symfony\Component\HttpFoundation\Response;

class PostCommentController extends Controller
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
            'current_user_total_of_comments' => $post->currentUserComments()->where('user_id', auth()->id())->count(),
            'latest_comments' => CommentResource::collection(
                $post->latestComments()->latest()->limit(1)->get()
            ),
        ], Response::HTTP_CREATED);
    }
}
