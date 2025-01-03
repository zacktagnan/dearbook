<?php

namespace App\Http\Resources;

use App\Libs\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserCreatorNotMemberButAccountResource extends JsonResource
{
    public function __construct($resource, public ?Carbon $groupCreationDate = null)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'username' => $this->username,
            // // -> Si existe y no es NULL, se trata, sino, se devuelve NULL
            // 'cover_url' => $this->cover_path
            //     ? Storage::url($this->cover_path)
            //     : null,
            // ----------------------------------------------------------------------------------
            // Ahora, en vez de NULL, se pasa la imagen predeterminada por defecto desde aquí
            'cover_url' => $this->cover_path
                ? Storage::url($this->cover_path)
                : Utilities::$defaultCoverImage,
            'avatar_url' => $this->avatar_path
                ? Storage::url($this->avatar_path)
                : Utilities::$defaultAvatarImage,

            'pinned_post_id' => $this->pinned_post_id,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'joining_date' => $this->getJoiningDateFormatted($this->groupCreationDate),
        ];
    }

    private function getJoiningDateFormatted(Carbon $date): string
    {
        return __('dearbook/group/list.members.creating_date_text', [
            'creating_date' => $date->diffForHumans(),
        ]);
    }
}
