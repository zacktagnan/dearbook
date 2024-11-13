<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchGroupOrFollowingRequest extends FormRequest
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
            'search_term' => 'nullable|string|max:100',
        ];
    }

    public function prepareForValidation()
    {
        $searchTerm = $this->query('search_term', '');

        // Sanitizar el término de búsqueda
        // Eliminar etiquetas HTML
        $searchTerm = strip_tags($searchTerm);
        // Escapar caracteres especiales
        $searchTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');

        // Asignar el término saneado nuevamente al objeto request
        $this->merge([
            'search_term' => $searchTerm,
        ]);
    }

    public function getProcessedSearchTerm()
    {
        $searchTerm = $this->query('search_term', '');

        if (empty($searchTerm)) {
            return ['type' => null, 'term' => ''];
        }

        if (strpos($searchTerm, 'search_group_') === 0) {
            $type = 'group';
            $term = substr($searchTerm, strlen('search_group_'));
        } elseif (strpos($searchTerm, 'search_following_') === 0) {
            $type = 'following';
            $term = substr($searchTerm, strlen('search_following_'));
        } else {
            $type = null;
            $term = '';
        }

        return ['type' => $type, 'term' => $term];
    }
}
