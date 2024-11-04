<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use Inertia\Inertia;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\PostResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\FollowResource;

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

        $user = $request->user();
        $followings = $user->followings;
        $followings->transform(function ($following) {
            // $following->created_at_human = Carbon::parse($following->created_at)->diffForHumans();
            $following->since_date = __('dearbook/following/list.inside_profile.since_date_text', [
                'since_date' => Carbon::parse($following->pivot->created_at)->diffForHumans(),
            ]);
            return $following;
        });

        return Inertia::render('Home', [
            'after_comment_deleted' => session('after_comment_deleted'),
            'posts' => $posts,
            'groups' => GroupResource::collection($groups),
            'followings' => FollowResource::collection($followings),
        ]);
    }
}
