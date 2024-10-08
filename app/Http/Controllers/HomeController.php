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
        $posts = Post::listedOnTimeLine(auth()->id())
            ->paginate(20);

        $posts = PostResource::collection($posts);

        if ($request->wantsJson()) {
            return $posts;
        }

        $groups = Group::query()
            // ->select(['groups.*', 'g_u.status', 'g_u.role'])
            ->with('currentGroupUser')
            ->select(['groups.*'])
            ->join('group_users AS g_u', 'g_u.group_id', 'groups.id')
            ->where('g_u.user_id', auth()->id())
            // ->where('status', GroupUserStatus::APPROVED->value)
            // ->latest()
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
