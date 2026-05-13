<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'author' => 'nullable|string|max:150',
            'publisher' => 'nullable|string|max:150',
            'published_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'isbn' => ['nullable', 'string', 'max:20', 'regex:/^(?:97[89][-\s]?)?\d{1,5}[-\s]?\d{1,7}[-\s]?\d{1,7}[-\s]?[\dX]$/i'],
            'language' => 'nullable|string|max:10',
            'pages' => 'nullable|integer|min:1',
            'file_url' => 'required|url|max:500',
            'cover_image' => 'nullable|url|max:500',
            'tags' => 'array',
            'tags.*' => 'integer|exists:tags,id',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'published_year.min' => 'Năm xuất bản phải từ 1900.',
            'published_year.max' => 'Năm xuất bản không được lớn hơn năm hiện tại.',
            'isbn.regex' => 'ISBN không hợp lệ.',
            'pages.min' => 'Số trang phải lớn hơn 0.',
            'file_url.required' => 'Cần upload file PDF trước khi gửi.',
            'file_url.url' => 'File PDF không hợp lệ.',
            'cover_image.url' => 'Ảnh bìa không hợp lệ.',
        ];
    }
}
