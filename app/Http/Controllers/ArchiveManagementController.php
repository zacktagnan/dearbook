<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Inertia\Inertia;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class ArchiveManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('ArchiveManagement/Index', [
            'success' => session('success'),
        ]);
    }

    public function activityLogPostsCollection(): Collection
    {
        return Post::with(['user', 'attachments', 'group' => function ($query) {
            $query->limit(1)->orderBy('id')->get();
        }])->select('id', 'body', 'user_id', 'deleted_at', 'archived_at', 'created_at', 'group_id')
            ->selectRaw('DATE_FORMAT(created_at, "%l:%i %p") AS created_at_time')
            ->latest()
            ->where('user_id', auth()->id())
            ->get()
            ->groupBy(fn($item) => $item->createdAtWithoutTimeAndWeekDay());
    }

    public function activityLogPosts()
    {
        return response()->json([
            'current_activity_log_posts' => $this->activityLogPostsCollection(),
        ], Response::HTTP_OK);
    }

    public function archivedPostsCollection(): Collection
    {
        return Post::with(['user', 'attachments', 'group' => function ($query) {
            $query->limit(1)->orderBy('id')->get();
        }])->select('id', 'body', 'user_id', 'deleted_at', 'archived_at', 'created_at', 'group_id')
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

    public function trashedPostsCollection(): Collection
    {
        return Post::with(['user', 'attachments', 'group' => function ($query) {
            $query->limit(1)->orderBy('id')->get();
        }])->select('id', 'body', 'user_id', 'deleted_at', 'archived_at', 'created_at', 'group_id')
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

    public function trashedGroups()
    {
        return response()->json([
            'current_trashed_groups' => $this->trashedGroupsCollection(),
        ], Response::HTTP_OK);
    }

    public function notifyProcessEnding(string $processType)
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
