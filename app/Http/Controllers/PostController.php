<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Traits\StorageManagement;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    use StorageManagement;

    protected string $rootFolderBaseName = 'attachments/post-';

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request): RedirectResponse
    {
        // dd($request);
        // dd($request->attachments);

        DB::beginTransaction();

        $allFilePaths = [];
        $destinationFolder = '';

        try {
            $post = Post::create($request->all());
            $destinationFolder = $this->rootFolderBaseName . $post->id;

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $request->attachments ?? [];

            foreach ($files as $file) {
                $path = $file->store($destinationFolder, 'public');
                $allFilePaths[] = $path;
                $post->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $request->user()->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            $this->deleteAlreadyUploadedFiles($allFilePaths);
            $this->deleteFolderIfEmpty($destinationFolder);

            DB::rollBack();
        }

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post): void
    {
        DB::beginTransaction();

        $allFilePaths = [];
        $destinationFolder = $this->rootFolderBaseName . $post->id;

        try {
            // dd($request->all());
            $post->update($request->all());

            // dd($request->deleted_file_ids);
            if ($request->deleted_file_ids) {
                $attachmentsToDelete = $post->attachments()
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
                $post->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $request->user()->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            // dd($e->getMessage());
            $this->deleteAlreadyUploadedFiles($allFilePaths);
            $this->deleteFolderIfEmpty($destinationFolder);

            DB::rollBack();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post) //: ResponseFactory|RedirectResponse
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to DELETE this post", Response::HTTP_FORBIDDEN);
        }
        $post->delete();
        return back();
        // Si no se establece el BACK(), no vale el RedirectResponse
        // y, entonces, mejor no establecer ningún tipo de RETURN
    }
}
