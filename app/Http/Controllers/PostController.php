<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Traits\StorageManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Libs\Utilities;
use App\Traits\ResourcesDeletion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    use StorageManagement;
    use ResourcesDeletion;

    public function show(User $user, int $id): InertiaResponse
    {
        $userId = auth()->id();
        $post = Post::withCount(['reactions', 'comments',])
            ->with([
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
                'currentUserComments' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
                'latestComments' => function ($query) {
                    $query->root()
                        ->latest()->limit(1)->get();
                },
                'comments' => function ($query) {
                    // $query->whereNull('parent_id');
                    // o
                    $query->root()
                        ->withCount('childComments');
                },
            ])
            ->withTrashed()
            ->findOrFail($id);
        return Inertia::render('Post/Detail', [
            'post' => new PostResource($post),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        $allFilePaths = [];
        $destinationFolder = '';

        try {
            $post = Post::create($request->all());
            $destinationFolder = Utilities::$postRootFolderBaseName . $post->id;

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
        $destinationFolder = Utilities::$postRootFolderBaseName . $post->id;

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

    public function trashedPostsCollection(): Collection
    {
        return Post::with(['user', 'attachments' => function ($query) {
            $query->limit(1)->orderBy('id')->get();
        }])->select('id', 'body', 'user_id', 'created_at')
            // ->select('id', 'body', 'user_id', 'created_at')
            ->selectRaw('DATE_FORMAT(created_at, "%l:%i %p") AS created_at_time')
            ->onlyTrashed()->latest()
            // ->groupBy('user_id', 'created_at', 'id')
            ->where('user_id', auth()->id())
            ->get()
            // ->groupBy([fn ($item) => Carbon::parse($item->created_at)->format('Y-m-d'), 'user_id', 'id']);
            // ->groupBy([fn ($item) => Carbon::parse($item->created_at)->format('Y-m-d'), 'id'])
            // Buen resultado
            // ->groupBy(fn ($item) => Carbon::parse($item->created_at)->format('Y-m-d'));
            ->groupBy(fn($item) => $item->createdAtWithoutTimeAndWeekDay());
        // ->all();
    }

    public function trashedPosts()
    {
        // Ejecutando método de otro controlador...
        // return response()->json([
        //     'current_trashed_posts' => (new ArchiveManagementController)->trashedPosts(),
        // ], Response::HTTP_OK);

        return response()->json([
            'current_trashed_posts' => $this->trashedPostsCollection(),
        ], Response::HTTP_OK);
    }

    public function restoreAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            $postsToRestore = Post::onlyTrashed()
                ->whereIn('id', $request->checked_ids)
                ->get();

            foreach ($postsToRestore as $post) {
                $this->applyRestoration($post);
            }
        }

        return back();
    }

    public function restore(int $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->applyRestoration($post);

        return back();
    }

    public function applyRestoration(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to PROCESS the Restoration of this post", Response::HTTP_FORBIDDEN);
        }
        $post->restore();
    }

    public function forceDestroyAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            $postsToForceDelete = Post::onlyTrashed()
                ->whereIn('id', $request->checked_ids)
                ->get();

            foreach ($postsToForceDelete as $post) {
                $this->applyForceDeletion($post);
            }
        }

        return back()->with('success', 'Conjunto de publicaciones y recursos eliminados satisfactoriamente.');
    }

    public function forceDestroy(int $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->applyForceDeletion($post);

        return back()->with('success', 'Publicación y recursos eliminados satisfactoriamente.');
    }

    public function applyForceDeletion(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to PROCESS the Total Deletion of this post", Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();

        try {
            $this->deleteResources($post);

            $post->forceDelete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    private function deleteResources(Post $post): void
    {
        $this->processingDeleteResources(new Post, $post->id);
    }
}
