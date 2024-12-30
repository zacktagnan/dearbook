<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class GroupUpdateRequest extends FormRequest
{
    /** @var \App\Models\Group $group */
    private $group;

    public function prepareForValidation(): void
    {
        $this->group = $this->route('group');
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'name' => 'required|max:255|unique:groups,name,' . $this->group->id,
            'auto_approval' => 'required|boolean',
            'about' => 'nullable|max:500',
        ];
    }
}
