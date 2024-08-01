<?php

namespace App\Http\Requests;

use Closure;
use App\Libs\Utilities;
use App\Rules\BodyOrAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    public static int $maximumAmount = 15; // 25;
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
        return $this->post->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'body' => array_merge($this->body ? ['string'] : [], [
                new BodyOrAttachment($this->route('post')),
                // function (string $attribute, mixed $value, Closure $fail) {
                //     $post = $this->route('post');
                //     $currentAttachmentsOnBD = $post->attachments()->select('id')->get();

                //     if ($value === null && $this->attachments === [] && (count($this->deleted_file_ids) === $currentAttachmentsOnBD->count())) {
                //         $fail('Se requiere ya sea que haya un texto o un archivo adjunto.');
                //     }
                // },
            ]),
            'attachments' => [
                'array',
                function (string $attribute, mixed $value, Closure $fail) {
                    $post = $this->route('post');
                    $currentAttachmentsOnBD = $post->attachments()->count();
                    $totalOfDeletedFiles = ($this->deleted_file_ids) ? count($this->deleted_file_ids) : 0;
                    $totalOfFiles = ($currentAttachmentsOnBD + count($value)) - $totalOfDeletedFiles;

                    if ($totalOfFiles > self::$maximumAmount) {
                        $fail('El máximo de adjuntos permitido por Post es de ' . self::$maximumAmount . '. Establecidos: ' . $totalOfFiles . '.');
                    }
                },
                function (string $attribute, mixed $value, Closure $fail) {
                    $totalSize = collect($value)->sum(fn (UploadedFile $file) => $file->getSize());

                    $post = $this->route('post');
                    $currentAttachmentsOnBD = $post->attachments()->select('size')->get();
                    foreach ($currentAttachmentsOnBD as $attachmentOnBD) {
                        $totalSize += $attachmentOnBD->size;
                    }

                    if ($this->deleted_file_ids) {
                        $attachmentsToDelete = $post->attachments()
                            ->whereIn('id', $this->deleted_file_ids)
                            ->get();

                        foreach ($attachmentsToDelete as $attachmentToDelete) {
                            $totalSize -= $attachmentToDelete->size;
                        }
                    }

                    // Total de tamaño en KB del conjunto de archivos subidos
                    if ($totalSize > $this->maximumTotalBytes) {
                        $fail('El tamaño total de todos los archivos no debe exceder de ' . ($this->maximumTotalBytes / $this->maximumBytes) . 'GB. Subido: ' . number_format($totalSize / $this->maximumBytes, 2) . 'GB.');
                    }
                },
            ],
            'attachments.*' => [
                'file',
                File::types(Utilities::$allowedMimeTypes),
            ],
            'deleted_file_ids' => 'array',
            'deleted_file_ids.*' => 'numeric',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge(['user_id' => auth()->id()]);
    }

    public function messages()
    {
        return [
            'attachments.*.file' => 'Cada adjunto debe ser un archivo.',
            'attachments.*.mimes' => 'Extensión no válida.',
        ];
    }
}
