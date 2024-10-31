<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Group;
use App\Libs\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupUserResource extends JsonResource
{
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
            'username' => $this->username,
            // -> Si existe y no es NULL, se trata, sino, se devuelve NULL o, directamente, la imagen predeterminada
            'avatar_url' => $this->avatar_path
                ? Storage::url($this->avatar_path)
                : Utilities::$defaultAvatarImage,
            'role' => $this->role,
            'status' => $this->status,
            'group_id' => $this->group_id,
            'joining_date' => $this->getJoiningDateFormatted($this->created_at, $this->group_id, $this->user_id),
        ];
    }

    private function getJoiningDateFormatted(Carbon $date, int $groupId, int $userId): string
    {
        if ($this->isUserTheOwnerOfTheGroup($groupId, $userId)) {
            return __('dearbook/group/list.members.creating_date_text', [
                'creating_date' => $date->diffForHumans(),
            ]);
        }
        return __('dearbook/group/list.members.joining_date_text', [
            'joining_date' => $date->diffForHumans(),
        ]);
    }

    public function isUserTheOwnerOfTheGroup(int $groupId, int $userId): bool
    {
        return Group::where('user_id', $userId)
            ->where('id', $groupId)
            ->exists();
    }
}
