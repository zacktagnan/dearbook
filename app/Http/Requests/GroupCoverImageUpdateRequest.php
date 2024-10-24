<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupCoverImageUpdateRequest extends FormRequest
{
    protected int $minWidth = 1260;
    protected int $minHeight = 330;

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
            'cover' => 'image|mimes:png,jpg|dimensions:min_width=' . $this->minWidth . ',min_height=' . $this->minHeight,
        ];
    }

    public function messages(): array
    {
        return [
            'cover.dimensions' => 'El campo cover tiene dimensiones de imagen no válidas. Minimo:' . $this->minWidth . 'x' . $this->minHeight,
        ];
    }
}
