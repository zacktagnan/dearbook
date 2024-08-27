<?php

namespace App\Http\Resources;

// use App\Models\PostReaction;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Traits\UserReactions;
use App\Traits\UsersThatCommented;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    use UserReactions;
    use UsersThatCommented;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // [ Pasado a nivel de PostReactionController, llamado desde onMounted del Post/Item ]
        // -> Consulta desde el Resource - Sacar datos
        // $postReactions = $post->reactions()->where('user_id', '<>', $this->user_id)->get();
        // $usersThatReactToPost = [];
        // foreach ($postReactions as $postReaction) {
        //     // $usersThatReactToPost[] = $postReaction->user->name;
        //     $usersThatReactToPost[] = [
        //         'name' => $postReaction->user->name,
        //     ];
        // }
        // -------------------------------------------
        // dd($usersThatReactToPost);

        // dd('authUser-reaction-type', $this->reactions[0]->type);

        return [
            'id' => $this->id,
            'body' => $this->body
                ?: '',
            'user' => new UserResource($this->user),

            'deleted_at' => $this->deleted_at
                ?: '',
            'archived_at' => $this->archived_at
                ?: '',

            // Cargando a través del nombre de la relación con Post,
            'group' => $this->group,
            'attachments' => AttachmentResource::collection($this->attachments),
            // -> Consulta desde el Resource - Enviar datos
            // 'users_that_react_to_post' => $usersThatReactToPost,
            'total_of_reactions' => $this->reactions_count,
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'current_user_type_reaction' => $this->reactions->count() > 0
                ? $this->reactions[0]->type
                : '',

            'all_user_reactions' => $this->allUserReactions(new Post, $this->id),
            'like_user_reactions' => $this->typeUserReactions(new Post, $this->id, 'like'),
            'love_user_reactions' => $this->typeUserReactions(new Post, $this->id, 'love'),
            'care_user_reactions' => $this->typeUserReactions(new Post, $this->id, 'care'),
            'haha_user_reactions' => $this->typeUserReactions(new Post, $this->id, 'haha'),
            'wow_user_reactions' => $this->typeUserReactions(new Post, $this->id, 'wow'),
            'sad_user_reactions' => $this->typeUserReactions(new Post, $this->id, 'sad'),
            'angry_user_reactions' => $this->typeUserReactions(new Post, $this->id, 'angry'),

            'total_of_comments' => $this->comments_count,
            'current_user_has_comment' => $this->currentUserComments->count() > 0,
            'current_user_total_of_comments' => $this->currentUserComments->count(),
            'latest_comments' => CommentResource::collection($this->latestComments),
            'all_comments' => CommentResource::collection($this->comments),
            'all_users_that_commented' => $this->allUsersThatCommented($this->id),

            // 'created_at' => $this->created_at->format(config('app.format.' . app()->getLocale() . '.datetime')),
            // 'updated_at' => $this->updated_at->format(config('app.format.' . app()->getLocale() . '.datetime.basic')),
            // ------------------------------------------------------------------------------------------------
            // 'created_at' => $this->createdAtWithFormat(),
            // 'created_at' => $this->created_at->translatedFormat('j F Y'),
            // ------------------------------------------------------------------------------------------------
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->createdAtWithFormat(),
            'created_at_large_format' => $this->createdAtWithLargeFormat(),
            'updated_at_large_format' => $this->updatedAtWithLargeFormat(),
        ];
    }
}
