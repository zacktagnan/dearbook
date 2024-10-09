<?php

namespace App\Http\Requests;

use App\Http\Enums\GroupUserStatus;
use Closure;
use App\Libs\Utilities;
use App\Models\GroupUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    public static int $maximumAmount = 15; // 25;

    // public static array $allowedMimeTypes = [
    //     'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
    //     'wav', 'mp3', 'mp4',
    //     'doc', 'docx', 'xls', 'xlsx', 'txt',
    //     'pdf', 'csv', 'zip', 'rar',
    // ];
    // Pasado a ... Utilities.php

    // private $maximumBytes = pow(1024, 3);
    // // 1GB ~= 1 * 1024 * 1024 * 1024 (1GB * 1024MB * 1024KBytes * 1024bytes)
    // // private $maximumTotalBytes = 1 * 1024 * 1024 * 1024;
    // private $maximumTotalBytes = 1 * $this->maximumBytes;
    private $maximumBytes;
    // 1GB ~= 1 * 1024 * 1024 * 1024 (1GB * 1024MB * 1024KBytes * 1024bytes)
    // private $maximumTotalBytes = 1 * 1024 * 1024 * 1024;
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
            'body' => 'required_without:attachments|nullable|string',
            'attachments' => [
                'required_without:body',
                'array',
                'max:' . self::$maximumAmount,
                function ($attribute, $value, $fail) {
                    // $totalSize = collect($value)->sum(function (UploadedFile $file) {
                    //     return $file->getSize();
                    // });
                    $totalSize = collect($value)->sum(fn(UploadedFile $file) => $file->getSize());

                    // dd('maximumBytes', $this->maximumBytes, 'maximumTotalBytes', $this->maximumTotalBytes, 'totalSize', $totalSize, $totalSize / $this->maximumBytes);

                    // Total de tamaño en KB del conjunto de archivos subidos
                    if ($totalSize > $this->maximumTotalBytes) {
                        // $fail('The total size of all files must not exceed 1GB.');
                        $fail('El tamaño total de todos los archivos no debe exceder de ' . ($this->maximumTotalBytes / $this->maximumBytes) . 'GB. Subido: ' . number_format($totalSize / $this->maximumBytes, 2) . 'GB.');
                    }
                },
            ],
            'attachments.*' => [
                'file',
                // File::types(self::$allowedMimeTypes),
                File::types(Utilities::$allowedMimeTypes),
            ],

            'group_id' => [
                'nullable',
                'exists:groups,id',
                function (string $attribute, mixed $value, Closure $fail) {
                    $groupUser = GroupUser::where('user_id', auth()->id())
                        ->where('group_id', $value)
                        ->where('status', GroupUserStatus::APPROVED->value)
                        // ->first();
                        ->exists();

                    if (!$groupUser) {
                        $fail('Solo un MIEMBRO aprobado puede publicar.');
                    }
                },
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
            'body.required_without' => 'Se requiere que el comentario esté constituido o por un texto o por un archivo adjunto.',
            'attachments.required_without' => 'Se requiere que el comentario esté constituido o por un texto o por un archivo adjunto.',
            // 'attachments.*' => 'El adjunto elegido debe disponer de una de las siguientes extensiones: ' . implode(', ', self::$allowedMimeTypes),
            // mensaje demasiado largo
            'attachments.max' => 'Demasiados archivos adjuntos. Máximo ' . self::$maximumAmount . '. Agregados: ' . count($this->attachments) . '.',
            'attachments.*.file' => 'Cada adjunto debe ser un archivo.',
            'attachments.*.mimes' => 'Extensión no válida.',
        ];
    }
}
