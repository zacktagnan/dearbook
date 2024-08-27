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
            ->withArchived()
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
    public function destroy(int $id, string $to) //: ResponseFactory|RedirectResponse
    {
        $post = Post::withArchived()->findOrFail($id);
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to DELETE this post", Response::HTTP_FORBIDDEN);
        }

        if (!is_null($post->archived_at)) {
            $post->unArchive();
        }
        $post->delete();

        // return back();
        // // Si no se establece el BACK(), no vale el RedirectResponse
        // // y, entonces, mejor no establecer ningún tipo de RETURN

        if ($to === 'home') {
            return to_route($to);
        } else {
            return to_route('archive-management.index')->with([
                'success' => [
                    'from' => $to,
                    'message' => 'Publicación enviada a la papelera satisfactoriamente.',
                ],
            ]);
        }
    }

    public function destroyFromManagementAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            $postsToDelete = Post::withArchived()
                ->whereIn('id', $request->checked_ids)
                ->get();

            foreach ($postsToDelete as $post) {
                $this->applyDeletionFromManagement($post);
            }
        }

        // return back()->with('success', 'Conjunto de publicaciones enviado a la papelera satisfactoriamente.');
        return back()->with([
            'success' => [
                'from' => $request->from,
                'message' => 'Conjunto de publicaciones enviado a la papelera satisfactoriamente.',
            ],
        ]);
    }

    public function destroyFromManagement(int $id, string $from)
    {
        $post = Post::withArchived()->findOrFail($id);
        $this->applyDeletionFromManagement($post);

        // return back()->with('success', 'Publicación enviada a la papelera satisfactoriamente.');
        return back()->with([
            'success' => [
                'from' => $from,
                'message' => 'Publicación enviada a la papelera satisfactoriamente.',
            ],
        ]);
    }

    public function applyDeletionFromManagement(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to PROCESS the Deletion of this post", Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();

        try {
            if (!is_null($post->archived_at)) {
                $post->unArchive();
            }
            $post->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function trashedPostsCollection(): Collection
    {
        return Post::with(['user', 'attachments' => function ($query) {
            $query->limit(1)->orderBy('id')->get();
        }])->select('id', 'body', 'user_id', 'deleted_at', 'archived_at', 'created_at')
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
            // $postsToRestore = Post::onlyTrashed()
            //     ->whereIn('id', $request->checked_ids)
            //     ->get();
            $postsToRestore = match ($request->from) {
                'archive' => Post::onlyArchived()
                    ->whereIn('id', $request->checked_ids)
                    ->get(),
                'trash' => Post::onlyTrashed()
                    ->whereIn('id', $request->checked_ids)
                    ->get(),
            };

            foreach ($postsToRestore as $post) {
                $this->applyRestoration($post, $request->from);
            }
        }

        return back();
    }

    public function restore(int $id, string $from)
    {
        // $post = Post::onlyTrashed()->findOrFail($id);
        $post = match ($from) {
            'archive' => Post::onlyArchived()->findOrFail($id),
            'trash' => Post::onlyTrashed()->findOrFail($id),
        };
        $this->applyRestoration($post, $from);

        return back();
    }

    public function applyRestoration(Post $post, string $from)
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to PROCESS the Restoration of this post", Response::HTTP_FORBIDDEN);
        }
        match ($from) {
            'archive' => $post->unArchive(),
            'trash' => $post->restore(),
        };
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

        // return back()->with('success', 'Conjunto de publicaciones y recursos eliminados satisfactoriamente.');
        return back()->with([
            'success' => [
                'from' => $request->from,
                'message' => 'Conjunto de publicaciones y recursos eliminados satisfactoriamente.',
            ],
        ]);
    }

    public function forceDestroy(int $id, string $from)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->applyForceDeletion($post);

        // return back()->with('success', 'Publicación y recursos eliminados satisfactoriamente.');
        return back()->with([
            'success' => [
                'from' => $from,
                'message' => 'Publicación y recursos eliminados satisfactoriamente.',
            ],
        ]);
    }

    public function forceDestroyFromDetail(int $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->applyForceDeletion($post);

        // return Redirect::route('home');
        // return redirect()->route('home');
        return to_route('home');
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

    public function archivedPostsCollection(): Collection
    {
        return Post::with(['user', 'attachments' => function ($query) {
            $query->limit(1)->orderBy('id')->get();
        }])->select('id', 'body', 'user_id', 'deleted_at', 'archived_at', 'created_at')
            ->selectRaw('DATE_FORMAT(created_at, "%l:%i %p") AS created_at_time')
            ->onlyArchived()->latest()
            ->where('user_id', auth()->id())
            ->get()
            ->groupBy(fn($item) => $item->createdAtWithoutTimeAndWeekDay());
    }

    public function archivedPosts()
    {
        return response()->json([
            'current_archived_posts' => $this->archivedPostsCollection(),
        ], Response::HTTP_OK);
    }

    public function archiveAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            $postsToArchive = match ($request->from) {
                // 'archive' => Post::onlyArchived()
                //     ->whereIn('id', $request->checked_ids)
                //     ->get(),
                'trash' => Post::onlyTrashed()
                    ->whereIn('id', $request->checked_ids)
                    ->get(),
            };

            foreach ($postsToArchive as $post) {
                $this->applyArchivation($post);
            }
        }

        return back();
        // return back()->with([
        //     'success' => [
        //         'from' => $request->from,
        //         'message' => 'Conjunto de publicaciones archivado satisfactoriamente.',
        //     ],
        // ]);
    }

    public function archive(int $id, string $from)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $this->applyArchivation($post);

        if ($from === 'home') {
            return back();
        } else if ($from === 'detail') {
            return to_route('home');
        } else {
            return back()->with([
                'success' => [
                    'from' => $from,
                    'message' => 'Publicación archivada satisfactoriamente.',
                ],
            ]);
        }
    }

    public function applyArchivation(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to PROCESS the Archivation of this post", Response::HTTP_FORBIDDEN);
        }
        if (!is_null($post->deleted_at)) {
            $post->restore();
        }
        $post->archive();
    }
}
