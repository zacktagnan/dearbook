<?php

namespace App\Traits;

use App\Http\Resources\ReactionResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

trait UserReactions
{
    public function authUserReactions($reactions)
    {
        return $reactions->where('user_id', auth()->id())->get();
    }

    public function allUserReactions(Model $model, int $id): JsonResource
    {
        $model = ($model instanceof Post)
            ? $model::withArchived()->withTrashed()->find($id)
            : $model::find($id);
        // dump('MODEL', $model);
        // dd();
        $reactions = $model->reactions()->where('user_id', '<>', auth()->id())
            ->orderBy('created_at', 'DESC')->get();

        return ReactionResource::collection($reactions);
    }

    public function typeUserReactions(Model $model, int $id, string $type): JsonResource
    {
        $model = ($model instanceof Post)
            ? $model::withArchived()->withTrashed()->find($id)
            : $model::find($id);
        $reactions = $model->reactions()->where('type', $type)
            ->where('user_id', '<>', auth()->id())
            ->orderBy('created_at', 'DESC')->get();

        return ReactionResource::collection($reactions);
    }
}
