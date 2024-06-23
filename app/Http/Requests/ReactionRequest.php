<?php

namespace App\Http\Requests;

use App\Http\Enums\ReactionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reaction_type' => [
                Rule::enum(ReactionEnum::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'reaction_type' => 'El tipo de reacción marcada no está disponible.',
        ];
    }
}
