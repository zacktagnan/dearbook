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
            // 'comment' => $this->comment,
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
        ];
    }
}
