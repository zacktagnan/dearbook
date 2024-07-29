<?php

namespace App\Http\Requests;

use App\Libs\Utilities;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    public static int $maximumLength = 250;

    private $maximumBytes;
    private $maximumTotalBytes;

    public function __construct()
    {
        $this->maximumBytes = pow(1024, 3); // 1GB en Bytes
        $this->maximumTotalBytes = 1 * $this->maximumBytes;
    }

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
            'parent_id' => 'nullable|exists:comments,id',
            'comment' => 'max:' . self::$maximumLength,
            'attachments' => [
                'array',
                function ($attribute, $value, $fail) {
                    $totalSize = collect($value)->sum(fn (UploadedFile $file) => $file->getSize());

                    // Total de tamaño en KB del conjunto de archivos subidos
                    if ($totalSize > $this->maximumTotalBytes) {
                        $fail('El tamaño total de archivo no debe exceder de ' . ($this->maximumTotalBytes / $this->maximumBytes) . 'GB. Subido: ' . number_format($totalSize / $this->maximumBytes, 2) . 'GB.');
                    }
                },
            ],
            'attachments.*' => [
                'file',
                File::types(Utilities::$allowedMimeTypes),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.max' => 'El comentario no debe exceder los ' . self::$maximumLength . ' caracteres. Introducidos ' . Str::length($this->comment) . '.',
            'attachments.*.file' => 'Cada adjunto debe ser un archivo.',
            'attachments.*.mimes' => 'Extensión no válida.',
        ];
    }
}
