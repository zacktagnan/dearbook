<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\ReactionRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public function allReactionsUsers(Comment $comment): array
    {
        $reactions = $comment->reactions()->where('user_id', '<>', auth()->id())->get();

        $usersThatReactToComment = $this->getReactionUsers($reactions);

        return $usersThatReactToComment;
    }

    public function typeReactionsUsers(Comment $comment, string $type): array
    {
        $reactions = $comment->reactions()->where('type', $type)
            ->where('user_id', '<>', auth()->id())->get();

        $usersThatReactToComment = $this->getReactionUsers($reactions);

        return $usersThatReactToComment;
    }

    private function getReactionUsers(Collection $reactions): array
    {
        $usersThatReactToComment = [];
        foreach ($reactions as $reaction) {
            $usersThatReactToComment[] = [
                'name' => $reaction->user->name,
            ];
        }

        return $usersThatReactToComment;
    }
}
