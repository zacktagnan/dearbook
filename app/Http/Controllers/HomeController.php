<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $posts = Post::withCount(['reactions', 'comments',])
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
            ->latest()
            ->paginate(20);
        // $posts = PostResource::collection($posts);
        // return Inertia::render('Home', compact('posts'));
        return Inertia::render('Home', [
            // 'posts' => $posts,
            'posts' => PostResource::collection($posts),
        ]);
    }
}
