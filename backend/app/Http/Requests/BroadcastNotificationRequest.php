<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BroadcastNotificationRequest extends FormRequest
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
            'target' => ['required', Rule::in(['all', 'students', 'teachers'])],
            'title' => 'required|string|max:200',
            'content' => 'required|string|min:10',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.max' => 'Tiêu đề tối đa 200 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung.',
            'content.min' => 'Nội dung tối thiểu 10 ký tự.',
        ];
    }
}
