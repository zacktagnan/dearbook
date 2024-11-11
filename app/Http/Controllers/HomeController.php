<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use Inertia\Inertia;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\SearchGroupOrFollowingRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\FollowResource;

class HomeController extends Controller
{
    public function index(SearchGroupOrFollowingRequest $request)
    {
        $userId = auth()->id();

        $posts = Post::listedOnTimeLine($userId)
            ->apply(Post::onlyFromFollowers($userId))
            ->paginate(5); //20

        $posts = PostResource::collection($posts);

        if ($request->wantsJson()) {
            return $posts;
        }

        $processedSearch = $request->getProcessedSearchTerm();
        $searchTerm = $processedSearch['term'];
        $filterType = $processedSearch['type'];
        $searchGroupTerm = '';

        $groups = Group::query()
            ->with('currentGroupUser')
            ->select(['groups.*', 'g_u.role'])
            ->join('group_users AS g_u', 'g_u.group_id', 'groups.id')
            ->where('g_u.user_id', $userId)
            ->orderByRaw('CASE WHEN groups.user_id = ? THEN 0 ELSE 1 END', [$userId])
            ->orderBy('g_u.role')
            ->orderBy('groups.name');
        if ($filterType === 'group' && $searchTerm) {
            // Aplicando un filtro WHERE agrupado sobre 'groups' para evitar posibles duplicados
            $groups->where(function ($query) use ($searchTerm) {
                $query->where('groups.name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('groups.about', 'like', "%$searchTerm%");
            });
            $searchGroupTerm = $searchTerm;
        }
        $groups = $groups->get();

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
            'searchGroupTerm' => $searchGroupTerm,
        ]);
    }
}
