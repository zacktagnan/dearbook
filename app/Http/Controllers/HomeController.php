<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
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
