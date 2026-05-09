<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'description' => 'nullable|string',
            'author' => 'nullable|string|max:150',
            'publisher' => 'nullable|string|max:150',
            'published_year' => 'nullable|integer|min:1900|max:'.date('Y'),
            'isbn' => 'nullable|string|max:20',
            'language' => 'nullable|string|max:10',
            'pages' => 'nullable|integer|min:1',
            'file_url' => 'sometimes|required|url|max:500',
            'cover_image' => 'nullable|url|max:500',
            'tags' => 'array',
            'tags.*' => 'integer|exists:tags,id',
            'is_featured' => 'boolean',
        ];
    }
}
