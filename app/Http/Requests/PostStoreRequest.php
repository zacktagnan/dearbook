<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class PostStoreRequest extends FormRequest
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
            'body' => 'nullable|string',
            'attachments' => 'array|max:10',
            'attachments.*' => [
                'file',
                File::types([
                    'jpg', 'jpeg', 'png', 'gif', 'webp',
                    'wav', 'mp3', 'mp4',
                    'doc', 'docx', 'xls', 'xlsx', 'txt',
                    'pdf', 'csv', 'zip', 'rar',
                ])->max('500mb'),
            ],
        ];
    }

    protected function passedValidation(): void
    {
        // dd('USER-auth-ID', auth()->id());
        $this->merge(['user_id' => auth()->id()]);
    }
}
