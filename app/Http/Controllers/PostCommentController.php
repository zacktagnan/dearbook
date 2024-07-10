<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CommentStoreRequest;
use Symfony\Component\HttpFoundation\Response;

class PostCommentController extends Controller
{
    public function store(CommentStoreRequest $request, Post $post)
    {
        DB::beginTransaction();

        $allFilePaths = [];

        try {
            $comment = Comment::create([
                'post_id' => $post->id,
                'comment' => $request->comment,
                'user_id' => auth()->id(),
            ]);
            $hasComment = true;

            $comments = $post->comments()->count();

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $request->attachments ?? [];

            foreach ($files as $file) {
                $path = $file->store('attachments/comment-' . $comment->id, 'public');
                $allFilePaths[] = $path;
                $comment->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $request->user()->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'total_of_comments' => $comments,
                'current_user_has_comment' => $hasComment,
                'current_user_total_of_comments' => $post->currentUserComments()->where('user_id', auth()->id())->count(),
                'latest_comments' => CommentResource::collection(
                    $post->latestComments()->latest()->limit(1)->get()
                ),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            DB::rollBack();

            return back();
        }
    }
}
