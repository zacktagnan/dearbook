<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    public static int $maximumLength = 200;

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
            'comment' => 'max:' . self::$maximumLength,
        ];
    }

    public function messages(): array
    {
        return [
            'comment.max' => 'El comentario no debe exceder los ' . self::$maximumLength . ' caracteres. Introducidos ' . Str::length($this->comment) . '.',
        ];
    }
}
