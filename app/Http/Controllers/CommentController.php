<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\CommentStoreRequest;
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

        $comments = Comment::count();

        return response()->json([
            'total_of_comments' => $comments,
        ], Response::HTTP_CREATED);
    }
}
