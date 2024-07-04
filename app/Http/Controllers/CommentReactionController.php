<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ReactionRequest;
use App\Http\Resources\ReactionResource;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentReactionController extends Controller
{
    public function reaction(ReactionRequest $request, Comment $comment): JsonResponse
    {
        if (!$request->current_reaction_type) {
            $hasReaction = true;
            $this->createReaction($comment, $request->reaction_type);
            $type = $request->reaction_type;
        } else {
            if ($request->from_main_reaction_button) {
                if ($this->reactionExists($comment)) {
                    $hasReaction = false;
                    $this->deleteReaction($comment);
                    $type = '';
                } else {
                    $hasReaction = true;
                    $this->createReaction($comment, $request->reaction_type);
                    $type = $request->reaction_type;
                }
            } else {
                $hasReaction = true;
                $this->updateReaction($comment, $request->reaction_type);
                $type = $request->reaction_type;
            }
        }

        $reactions = $comment->reactions()->count();

        return response()->json([
            'total_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
            'current_user_type_reaction' => $type,
        ], Response::HTTP_OK);
    }

    private function reactionExists(Comment $comment): bool
    {
        $reaction = $comment->reactions()->where('user_id', auth()->id())
            ->first();
        if ($reaction) {
            return true;
        } else {
            return false;
        }
    }

    private function createReaction(Comment $comment, string $type): void
    {
        $comment->reactions()->create([
            'user_id' => auth()->id(),
            'type' => $type,
        ]);
    }

    private function updateReaction(Comment $comment, string $type): void
    {
        $reaction = $comment->reactions()->where('user_id', auth()->id())
            ->first();

        $reaction->update([
            'type' => $type,
        ]);
    }

    private function deleteReaction(Comment $comment): void
    {
        $reaction = $comment->reactions()->where('user_id', auth()->id())
            ->first();

        $reaction->delete();
    }

    public function allReactionsUsers(Comment $comment): JsonResource
    {
        $reactions = $comment->reactions()->where('user_id', '<>', auth()->id())
            ->orderBy('created_at', 'DESC')->get();

        return ReactionResource::collection($reactions);
    }

    public function typeReactionsUsers(Comment $comment, string $type): JsonResource
    {
        $reactions = $comment->reactions()->where('type', $type)
            ->where('user_id', '<>', auth()->id())
            ->orderBy('created_at', 'DESC')->get();

        return ReactionResource::collection($reactions);
    }
}
