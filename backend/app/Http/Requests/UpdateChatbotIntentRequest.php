<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChatbotIntentRequest extends FormRequest
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
        $intentId = $this->route('id');

        return [
            'intent_key' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('chatbot_intents', 'intent_key')->ignore($intentId),
            ],
            'name' => 'sometimes|string|max:100',
            'keywords' => 'sometimes|array',
            'keywords.*' => 'string|max:120',
            'response_template' => 'sometimes|string',
            'data_source' => 'nullable|string|max:50',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'intent_key.unique' => 'Intent key đã tồn tại.',
            'keywords.array' => 'Keywords phải là danh sách.',
        ];
    }
}
