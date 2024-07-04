<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\ReactionRequest;
use App\Http\Resources\ReactionResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class PostReactionController extends Controller
{
    public function reaction(ReactionRequest $request, Post $post): JsonResponse
    {
        if (!$request->current_reaction_type) {
            $hasReaction = true;
            $this->createReaction($post, $request->reaction_type);
            $type = $request->reaction_type;
        } else {
            if ($request->from_main_reaction_button) {
                if ($this->reactionExists($post)) {
                    $hasReaction = false;
                    $this->deleteReaction($post);
                    $type = '';
                } else {
                    $hasReaction = true;
                    $this->createReaction($post, $request->reaction_type);
                    $type = $request->reaction_type;
                }
            } else {
                $hasReaction = true;
                $this->updateReaction($post, $request->reaction_type);
                $type = $request->reaction_type;
            }
        }

        $reactions = $post->reactions()->count();

        return response()->json([
            'total_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
            'current_user_type_reaction' => $type,
        ], Response::HTTP_OK);
    }

    private function reactionExists(Post $post): bool
    {
        $reaction = $post->reactions()->where('user_id', auth()->id())
            ->first();
        if ($reaction) {
            return true;
        } else {
            return false;
        }
    }

    private function createReaction(Post $post, string $type): void
    {
        $post->reactions()->create([
            'user_id' => auth()->id(),
            'type' => $type,
        ]);
    }

    private function updateReaction(Post $post, string $type): void
    {
        $reaction = $post->reactions()->where('user_id', auth()->id())
            ->first();

        $reaction->update([
            'type' => $type,
        ]);
    }

    private function deleteReaction(Post $post): void
    {
        $reaction = $post->reactions()->where('user_id', auth()->id())
            ->first();

        $reaction->delete();
    }

    public function allReactionsUsers(Post $post): JsonResource
    {
        $reactions = $post->reactions()->where('user_id', '<>', auth()->id())
            ->orderBy('created_at', 'DESC')->get();

        return ReactionResource::collection($reactions);
    }

    public function typeReactionsUsers(Post $post, string $type): JsonResource
    {
        $reactions = $post->reactions()->where('type', $type)
            ->where('user_id', '<>', auth()->id())
            ->orderBy('created_at', 'DESC')->get();

        return ReactionResource::collection($reactions);
    }
}
