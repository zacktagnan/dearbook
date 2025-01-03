<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\Group $group */
        $group = $this->route('group');
        return $group->isAdminOfTheGroup(auth()->id());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Es NECESARIO elegir un usuario ADMIN.',
            'user_id.exists' => 'Se precisa que el usuario ADMIN elegido sea válido.',
        ];
    }
}
