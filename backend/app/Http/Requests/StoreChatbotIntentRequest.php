<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatbotIntentRequest extends FormRequest
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
            'intent_key' => 'required|string|max:50|unique:chatbot_intents,intent_key',
            'name' => 'required|string|max:100',
            'keywords' => 'required|array',
            'keywords.*' => 'string',
            'response_template' => 'required|string',
            'data_source' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'intent_key.required' => 'Vui lòng nhập intent_key.',
            'intent_key.unique' => 'Intent key đã tồn tại.',
            'name.required' => 'Vui lòng nhập name.',
            'keywords.required' => 'Vui lòng nhập keywords.',
            'keywords.array' => 'Keywords phải là danh sách.',
            'response_template.required' => 'Vui lòng nhập response_template.',
        ];
    }
}
