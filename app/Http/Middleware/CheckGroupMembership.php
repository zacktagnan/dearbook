<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenAreaException;
use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $post = Post::detail(auth()->id())
            ->findOrFail($request->route('id'));

        if ($post->group) {
            if ($post->group->type === 'private') {
                if (!$post->group->currentGroupUserApproved) {
                    throw new ForbiddenAreaException(__('dearbook/group.exceptions.forbidden_area.message', [
                        'group_name' => $post->group->name,
                    ]), 403);
                }
            }
        }

        return $next($request);
    }
}
