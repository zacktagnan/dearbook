<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class BodyOrAttachment implements DataAwareRule, ValidationRule
{
    protected $data = [];
    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $currentAttachmentsOnBD = $this->post->attachments()->select('id')->get();

        if ($value === null && $this->data['attachments'] === [] && (count($this->data['deleted_file_ids']) === $currentAttachmentsOnBD->count())) {
            $fail('Es requerido bien un texto o un archivo adjunto.');
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
