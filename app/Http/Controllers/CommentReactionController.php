<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Traits\UserReactions;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ReactionRequest;
use App\Http\Resources\CommentResource;
use Symfony\Component\HttpFoundation\Response;

class CommentReactionController extends Controller
{
    use UserReactions;

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

        // Devolviendo más datos extra para actualización de los mismos
        // tanto en listado principal, como dentro del ModalForDetail...
        $post = Post::where('id', $comment->post_id)->first();

        return response()->json([
            'total_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
            'current_user_type_reaction' => $type,

            'all_user_reactions' => $this->allUserReactions(new Comment, $comment->id),
            'like_user_reactions' => $this->typeUserReactions(new Comment, $comment->id, 'like'),
            'love_user_reactions' => $this->typeUserReactions(new Comment, $comment->id, 'love'),
            'care_user_reactions' => $this->typeUserReactions(new Comment, $comment->id, 'care'),
            'haha_user_reactions' => $this->typeUserReactions(new Comment, $comment->id, 'haha'),
            'wow_user_reactions' => $this->typeUserReactions(new Comment, $comment->id, 'wow'),
            'sad_user_reactions' => $this->typeUserReactions(new Comment, $comment->id, 'sad'),
            'angry_user_reactions' => $this->typeUserReactions(new Comment, $comment->id, 'angry'),

            'latest_comments' => CommentResource::collection(
                $post->latestComments()->root()->latest()->limit(1)->get()
            ),
            'all_comments' => CommentResource::collection($post->comments()->root()->get()),
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
}
