<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Libs\Utilities;
use App\Models\Comment;
use App\Traits\ResourcesDeletion;
use App\Traits\StorageManagement;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CommentResource;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Traits\CommentsTree;
use Symfony\Component\HttpFoundation\Response;

class PostCommentController extends Controller
{
    use CommentsTree;
    use ResourcesDeletion;
    use StorageManagement;

    public function store(CommentStoreRequest $request, Post $post)
    {
        DB::beginTransaction();

        $allFilePaths = [];
        $destinationFolder = '';

        try {
            $comment = Comment::create([
                'parent_id' => $request->parent_id ?: null,
                'post_id' => $post->id,
                'comment' => $request->comment,
                'user_id' => auth()->id(),
            ]);
            $hasComment = true;

            $comments = $post->comments()->count();
            $destinationFolder = Utilities::$commentRootFolderBaseName . $comment->id;

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

            if (!is_null($request->parent_id)) {
                $commentParent = Comment::find($request->parent_id);
            }

            return response()->json([
                'total_of_comments' => $comments,
                'current_user_has_comment' => $hasComment,
                'current_user_total_of_comments' => $post->currentUserComments()->where('user_id', auth()->id())->count(),
                // -> Sin el commentTree
                // 'latest_comments' => CommentResource::collection(
                //     $post->latestComments()->root()->latest()->limit(1)->get()
                // ),
                // 'all_comments' => CommentResource::collection($post->comments()->root()->get()),
                // -> Con el commentTree
                'latest_comments' => $this->convertLatestCommentsIntoTree($post->latestComments()->get()),
                'all_comments' => $this->convertCommentsIntoTree($post->comments()->get()),

                'all_child_comments' => !is_null($request->parent_id)
                    ? CommentResource::collection($commentParent->childComments()->get())
                    : [],
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->deleteAlreadyUploadedFiles($allFilePaths);
            $this->deleteFolderIfEmpty($destinationFolder);

            DB::rollBack();

            return back();
        }
    }

    public function update(CommentUpdateRequest $request, Comment $comment) //: void
    {
        DB::beginTransaction();

        $allFilePaths = [];
        $destinationFolder = Utilities::$commentRootFolderBaseName . $comment->id;

        try {
            $comment->update($request->all());

            if ($request->deleted_file_ids) {
                $attachmentsToDelete = $comment->attachments()
                    ->whereIn('id', $request->deleted_file_ids)
                    ->get();

                foreach ($attachmentsToDelete as $attachmentToDelete) {
                    $attachmentToDelete->delete();
                }

                $this->deleteFolderIfEmpty($destinationFolder);
            }

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

            // return response(new CommentResource($comment), Response::HTTP_OK);
            // o
            // return new CommentResource($comment);
            // ---------------------------------------------------------------------------
            // o, para poder devolver más datos...
            // $post = Post::where('id', $comment->post_id)->get(); // Devuelve un ARRAY de resultados, uno en este caso
            // // Por ello, para tener el primer objeto del ARRAY con el que poder sacar datos de relaciones que pasar al CommentResource
            // $post = $post[0];
            // Sino, en vez del GET, emplear el FIRST
            $post = Post::where('id', $comment->post_id)->first();

            return response()->json([
                'commentUpdated' => new CommentResource($comment),

                // -> Sin el commentTree
                // 'latest_comments' => CommentResource::collection(
                //     $post->latestComments()->root()->latest()->limit(1)->get()
                // ),
                // 'all_comments' => CommentResource::collection($post->comments()->root()->get()),
                // -> Con el commentTree
                'latest_comments' => $this->convertLatestCommentsIntoTree($post->latestComments()->get()),
                'all_comments' => $this->convertCommentsIntoTree($post->comments()->get()),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->deleteAlreadyUploadedFiles($allFilePaths);
            $this->deleteFolderIfEmpty($destinationFolder);

            DB::rollBack();
        }
    }

    public function destroy(int $id, string $to)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== auth()->id()) {
            return response("You don't have permission to DELETE this comment", Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();

        try {
            $this->changeParentOrOrphan($comment);
            $this->deleteResources($comment);

            $comment->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back();
        }
    }

    private function changeParentOrOrphan(Comment $comment): void
    {
        $childrenComment = Comment::where('parent_id', $comment->id)->get();
        if ($childrenComment) {
            foreach ($childrenComment as $childComment) {
                $childComment->update([
                    'parent_id' => !is_null($comment->parent_id) ? $comment->parent_id : null,
                ]);
            }
        }
    }

    private function deleteResources(Comment $comment): void
    {
        $this->processingDeleteResources(new Comment, $comment->id);
    }
}
