<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\PostReactionRequest;
use App\Models\PostReaction;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostReactionController extends Controller
{
    public function reaction(PostReactionRequest $request, Post $post): JsonResponse
    {
        // dd('REQUEST', $request->all());

        // if (!$request->current_reaction_type) {
        //     dd('CURRENT_TYPE es NULL');
        // }
        // dd('CURRENT_TYPE', $request->current_reaction_type);

        // if (!$this->reactionExists($post->id)) {

        if (!$request->current_reaction_type) {
            $hasReaction = true;
            $this->createReaction($post->id, $request->reaction_type);
            // PostReaction::create([
            //     'post_id' => $post->id,
            //     'user_id' => auth()->id(),
            //     'type' => $request->reaction_type,
            // ]);
            $type = $request->reaction_type;
        } else {
            if ($request->from_main_reaction_button) {
                if ($this->reactionExists($post->id)) {
                    $hasReaction = false;
                    $this->deleteReaction($post->id);
                    $type = '';
                } else {
                    $hasReaction = true;
                    $this->createReaction($post->id, $request->reaction_type);
                    $type = $request->reaction_type;
                }
            } else {
                $hasReaction = true;
                $this->updateReaction($post->id, $request->reaction_type);
                $type = $request->reaction_type;
            }
        }

        // if ($this->reactionExists($post->id)) {
        //     $hasReaction = false;
        //     $this->deleteReaction($post->id);
        //     $type = '';
        // } else {
        //     $hasReaction = true;
        //     PostReaction::create([
        //         'post_id' => $post->id,
        //         'user_id' => auth()->id(),
        //         'type' => $request->reaction,
        //     ]);
        //     $type = $request->reaction;
        // }

        $reactions = PostReaction::where('post_id', $post->id)->count();

        return response()->json([
            'total_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
            'current_user_type_reaction' => $type,
        ], Response::HTTP_OK);
    }

    private function reactionExists(int $postId): bool
    {
        $reaction = PostReaction::where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->first();
        if ($reaction) {
            return true;
        } else {
            return false;
        }
    }

    private function createReaction(int $postId, string $type): void
    {
        PostReaction::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'type' => $type,
        ]);
    }

    private function updateReaction(int $postId, string $type): void
    {
        $reaction = PostReaction::where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->first();

        $reaction->update([
            'type' => $type,
        ]);
    }

    private function deleteReaction(int $postId): void
    {
        $reaction = PostReaction::where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->first();

        $reaction->delete();
    }

    public function allReactionsUsers(Post $post): array
    {
        $reactions = PostReaction::where('post_id', $post->id)
            ->where('user_id', '<>', auth()->id())->get();

        $usersThatReactToPost = $this->getReactionUsers($reactions);

        return $usersThatReactToPost;
    }

    public function typeReactionsUsers(Post $post, string $type): array
    {
        $reactions = PostReaction::where('post_id', $post->id)
            ->where('type', $type)
            ->where('user_id', '<>', auth()->id())->get();

        $usersThatReactToPost = $this->getReactionUsers($reactions);

        return $usersThatReactToPost;
    }

    private function getReactionUsers(Collection $reactions): array
    {
        $usersThatReactToPost = [];
        foreach ($reactions as $reaction) {
            // $usersThatReactToPost[] = $reaction->user->name;
            $usersThatReactToPost[] = [
                'name' => $reaction->user->name,
            ];
        }

        return $usersThatReactToPost;
    }
}
