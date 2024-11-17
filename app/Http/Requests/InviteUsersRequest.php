<?php

namespace App\Http\Requests;

use App\Http\Enums\GroupUserStatus;
use Closure;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Foundation\Http\FormRequest;

class InviteUsersRequest extends FormRequest
{
    public Group $group;
    public ?GroupUser $groupUser = null;
    public ?User $user = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\Group $group */
        $this->group = $this->route('group');

        // return true;
        return $this->group->isAdminOfTheGroup(auth()->id());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username_or_email' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $this->user = User::where('username', $value)
                        ->orWhere('email', $value)
                        ->first();

                    if (!$this->user) {
                        $fail('No hay ningún usuario con ese USERNAME/EMAIL.');
                    } else {
                        $this->groupUser = GroupUser::where('user_id', $this->user->id)
                            ->where('group_id', $this->group->id)
                            ->first();

                        if ($this->groupUser && $this->groupUser->status === GroupUserStatus::APPROVED->value) {
                            $fail('Este usuario ya es un miembro aprobado del grupo.');
                        }
                    }
                },
            ],
        ];
    }
}
