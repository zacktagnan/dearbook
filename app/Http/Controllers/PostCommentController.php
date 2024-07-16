<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CommentResource;
use App\Http\Requests\CommentStoreRequest;
use App\Traits\StorageManagement;
use Symfony\Component\HttpFoundation\Response;

class PostCommentController extends Controller
{
    use StorageManagement;

    public function store(CommentStoreRequest $request, Post $post)
    {
        DB::beginTransaction();

        $allFilePaths = [];
        $destinationFolder = '';

        try {
            $comment = Comment::create([
                'post_id' => $post->id,
                'comment' => $request->comment,
                'user_id' => auth()->id(),
            ]);
            $hasComment = true;

            $comments = $post->comments()->count();
            $destinationFolder = 'attachments/comment-' . $comment->id;

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $request->attachments ?? [];

            foreach ($files as $file) {
                $path = $file->store($destinationFolder, 'public');
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
            $this->deleteAlreadyUploadedFiles($allFilePaths);
            $this->deleteFolderIfEmpty($destinationFolder);

            DB::rollBack();

            return back();
        }
    }

    public function destroy(Comment $comment)
    {
        // dump('Borrando el COMMENT con ID', $comment->id, 'cuyo autor es el USER con ID', $comment->user_id, 'y siendo el AUTH->ID', auth()->id());
        // dd('TOT_Attachments', $comment->attachments()->count(), 'TOT_Reactions', $comment->reactions()->count());

        if ($comment->user_id !== auth()->id()) {
            return response("You don't have permission to DELETE this comment", Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();

        try {
            $this->deleteResources($comment);

            $comment->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back();
        }
    }

    private function deleteResources(Comment $comment)
    {
        if ($comment->attachments()->count() > 0) {
            $comment->attachments()->delete();
        }
        if ($comment->reactions()->count() > 0) {
            $comment->reactions()->delete();
        }
    }
}
