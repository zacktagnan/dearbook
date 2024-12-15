<?php

namespace App\Http\Controllers;

use App\Models\Post;
// use Inertia\Inertia;
use Inertia\{Inertia, Response as InertiaResponse};
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\PostDataFormatOnArchiveManagement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ArchiveManagementController extends Controller
{
    use PostDataFormatOnArchiveManagement;

    public function index(): InertiaResponse
    {
        return Inertia::render('ArchiveManagement/Index', [
            'success' => session('success'),
        ]);
    }

    public function activityLogPostsCollection(): Collection
    {
        $posts = Post::with(['user', 'group.currentGroupUser'])
            ->select('id', 'body', 'user_id', 'deleted_at', 'archived_at', 'created_at', 'group_id')
            ->selectRaw('DATE_FORMAT(created_at, "%l:%i %p") AS created_at_time')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $postsWithGroupBy = $this->processCollection($posts, 'only_attachment');

        return new Collection($postsWithGroupBy);
    }

    public function activityLogPosts(): JsonResponse
    {
        return response()->json([
            'current_activity_log_posts' => $this->activityLogPostsCollection(),
        ], Response::HTTP_OK);
    }

    public function archivedPostsCollection(): Collection
    {
        $posts = Post::onlyArchived()
            ->with(['user', 'group.currentGroupUser'])
            ->select('id', 'body', 'user_id', 'deleted_at', 'archived_at', 'created_at', 'group_id')
            ->selectRaw('DATE_FORMAT(created_at, "%l:%i %p") AS created_at_time')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $postsWithGroupBy = $this->processCollection($posts, 'only_attachment');

        return new Collection($postsWithGroupBy);
    }

    public function archivedPosts(): JsonResponse
    {
        return response()->json([
            'current_archived_posts' => $this->archivedPostsCollection(),
        ], Response::HTTP_OK);
    }

    public function trashedPostsCollection(): Collection|JsonResponse
    {
        $authId = auth()->id();

        try {
            $posts = Post::onlyTrashed()
                ->with(['user', 'group'])
                ->selectRaw('
                    id,
                    body,
                    user_id,
                    deleted_by,
                    deleted_at,
                    archived_at,
                    group_id,
                    created_at,
                    DATE_FORMAT(created_at, "%l:%i %p") AS created_at_time,
                    DATE_FORMAT(deleted_at, "%Y-%m-%d %H:%i:%s") AS deleted_at_time
                ')
                ->where(function ($query) use ($authId) {
                    // Mostrar posts eliminados por el usuario (sus propios posts)
                    $query->where('user_id', $authId)
                        ->where('deleted_by', $authId)
                        ->orWhere(function ($subQuery) use ($authId) {
                            // Permitir que el autor vea sus posts eliminados por otros (ADMIN)
                            $subQuery->where('user_id', $authId);
                        });

                    // También mostrar posts eliminados por otros administradores
                    $query->orWhere(function ($subQuery) use ($authId) {
                        // Posts eliminados por un ADMIN
                        $subQuery->whereHas('group.currentGroupUser', function ($query) use ($authId) {
                            $query->where('role', 'admin');
                        })
                            // Excluir posts eliminados que son de autoría del ADMIN que está consultando
                            ->where('user_id', '!=', $authId)
                            // Permitir que el ADMIN vea los posts eliminados por otros ADMINs
                            ->where('deleted_by', '!=', DB::raw('user_id'));
                    });
                })
                ->latest()
                ->get();

            $postsWithGroupBy = $this->processCollection($posts, 'attachment_and_group');

            return new Collection($postsWithGroupBy);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Error en la consulta'], 500);
        }
    }

    public function trashedPosts(): JsonResponse
    {
        // Ejecutando método de otro controlador...
        // return response()->json([
        //     'current_trashed_posts' => (new ArchiveManagementController)->trashedPosts(),
        // ], Response::HTTP_OK);

        return response()->json([
            'current_trashed_posts' => $this->trashedPostsCollection(),
        ], Response::HTTP_OK);
    }

    public function trashedGroupsCollection(): Collection
    {
        // , 'archived_at'
        return Group::with(['user'])->select('id', 'name', 'about', 'cover_path', 'thumbnail_path', 'user_id', 'deleted_by', 'deleted_at', 'created_at')
            ->selectRaw('DATE_FORMAT(created_at, "%l:%i %p") AS created_at_time')
            ->onlyTrashed()->latest()
            ->where('user_id', auth()->id())
            ->get()
            ->groupBy(fn($item) => $item->createdAtWithoutTimeAndWeekDay());
    }

    public function trashedGroups(): JsonResponse
    {
        return response()->json([
            'current_trashed_groups' => $this->trashedGroupsCollection(),
        ], Response::HTTP_OK);
    }

    public function notifyProcessEnding(string $processType): RedirectResponse
    {
        // $from = match ($processType) {
        //     'archive_all_selected_from_trash' => 'trash',
        // };
        $from = explode("from_", $processType)[1];

        return back()->with([
            'success' => [
                'from' => $from,
                'message' => 'Conjunto de publicaciones archivado satisfactoriamente.',
            ],
        ]);
    }
}
