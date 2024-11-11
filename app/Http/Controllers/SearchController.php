<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;

class SearchController extends Controller
{
    public function search(Request $request, string $keywords = null)
    {
        $users = User::where('name', 'like', "%$keywords%")
            ->orWhere('username', 'like', "%$keywords%")
            ->get();

        $groups = Group::where('name', 'like', "%$keywords%")
            ->orWhere('about', 'like', "%$keywords%")
            ->get();

        $posts = Post::listedOnTimeLine(auth()->id())
            ->where('body', 'like', "%$keywords%")
            ->paginate(5); //20

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }

        return Inertia::render('Search', [
            'after_comment_deleted' => session('after_comment_deleted'),
            'users' => UserResource::collection($users),
            'groups' => GroupResource::collection($groups),
            'posts' => $posts,
            'keywords' => $keywords,
        ]);
    }
}