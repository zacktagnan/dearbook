<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $posts = Post::listedOnTimeLine($userId)

            ->apply(Post::onlyFromFollowers($userId))

            ->paginate(5); //20

        $posts = PostResource::collection($posts);

        if ($request->wantsJson()) {
            return $posts;
        }

        $groups = Group::query()
            // ->select(['groups.*', 'g_u.status', 'g_u.role'])
            ->with('currentGroupUser')
            ->select(['groups.*'])
            ->join('group_users AS g_u', 'g_u.group_id', 'groups.id')
            ->where('g_u.user_id', $userId)
            // ->where('status', GroupUserStatus::APPROVED->value)
            // ->latest()
            ->orderByRaw('CASE WHEN groups.user_id = ? THEN 0 ELSE 1 END', [$userId])
            ->orderBy('g_u.role')
            ->orderBy('groups.name')
            ->get();

        return Inertia::render('Home', [
            'after_comment_deleted' => session('after_comment_deleted'),
            'posts' => $posts,
            'groups' => GroupResource::collection($groups),
        ]);
    }
}
