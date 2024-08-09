<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;

class ArchiveManagementController extends Controller
{
    public function index()
    {
        $trashedPosts = Post::with(['user', 'attachments' => function ($query) {
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
        return Inertia::render('ArchiveManagement/Index', [
            'activityLogPosts' => 'Publicaciones del Registro de Actividad',
            'archivedPosts' => 'Publicaciones Archivadas',
            // -----------------------------------------------------------------------------
            'trashedPosts' => $trashedPosts,
        ]);
    }
}
