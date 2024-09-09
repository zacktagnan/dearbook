<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Traits\UserReactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Comment;

class CommentResource extends JsonResource
{
    use UserReactions;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment
                ?: '',
            // 'user' => new UserResource($this->user),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                // -> Si existe y no es NULL, se trata, sino, se devuelve NULL
                'avatar_url' => $this->user->avatar_path
                    ? Storage::url($this->user->avatar_path)
                    : null,
            ],

            'total_of_comments' => $this->totalOfComments,
            'all_child_comments' => $this->childCommentsArr,

            'total_of_direct_child_comments' => $this->totalOfDirectChildComments,

            // 'latest_child_comments' => CommentResource::collection(
            //     $this->latestChildComments()->latest()->limit(1)->get()
            // ),
            // Para que salgan todos los comentarios HIJO del último comentario visible del Post
            // 'latest_child_comments' => CommentResource::collection(
            //     $this->latestChildComments()->get()
            // ),
            // Ahora, a través del árbol generado...
            'latest_child_comments' => $this->childCommentsArr,

            'attachments' => AttachmentResource::collection($this->attachments),
            'total_of_reactions' => $this->reactions->count(),
            'current_user_has_reaction' => $this->authUserReactions($this->reactions())->count() > 0,
            'current_user_type_reaction' => $this->authUserReactions($this->reactions())->count() > 0
                ? $this->authUserReactions($this->reactions())[0]->type
                : '',

            'all_user_reactions' => $this->allUserReactions(new Comment, $this->id),
            'like_user_reactions' => $this->typeUserReactions(new Comment, $this->id, 'like'),
            'love_user_reactions' => $this->typeUserReactions(new Comment, $this->id, 'love'),
            'care_user_reactions' => $this->typeUserReactions(new Comment, $this->id, 'care'),
            'haha_user_reactions' => $this->typeUserReactions(new Comment, $this->id, 'haha'),
            'wow_user_reactions' => $this->typeUserReactions(new Comment, $this->id, 'wow'),
            'sad_user_reactions' => $this->typeUserReactions(new Comment, $this->id, 'sad'),
            'angry_user_reactions' => $this->typeUserReactions(new Comment, $this->id, 'angry'),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->createdAtShortAbsDiffForHumans(),
            'created_at_large_format' => $this->createdAtWithLargeFormat(),
            'updated_at_large_format' => $this->updatedAtWithLargeFormat(),

            'parent_id' => $this->parent_id
                ?: '',
        ];
    }
}
