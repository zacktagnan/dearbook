<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class PostStoreRequest extends FormRequest
{
    public static array $allowedMimeTypes = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'wav', 'mp3', 'mp4',
        'doc', 'docx', 'xls', 'xlsx', 'txt',
        'pdf', 'csv', 'zip', 'rar',
    ];

    private $maximumAmount = 10;

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
            'body' => 'nullable|string',
            'attachments' => 'array|max:' . $this->maximumAmount,
            'attachments.*' => [
                'file',
                File::types(self::$allowedMimeTypes)->max('500mb'),
            ],
        ];
    }

    protected function passedValidation(): void
    {
        // dd('USER-auth-ID', auth()->id());
        $this->merge(['user_id' => auth()->id()]);
    }

    public function messages()
    {
        return [
            // 'attachments.*' => 'El adjunto elegido debe disponer de una de las siguientes extensiones: ' . implode(', ', self::$allowedMimeTypes),
            // mensaje demasiado largo
            'attachments.max' => 'Demasiados archivos adjuntos. Máximo ' . $this->maximumAmount . '.',
            'attachments.*' => 'Extensión no válida.',
        ];
    }
}
