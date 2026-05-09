<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'intent_key' => 'sometimes|string|max:100',
            'name' => 'sometimes|string|max:255',
            'keywords' => 'sometimes|array',
            'keywords.*' => 'string|max:120',
            'response_template' => 'sometimes|string',
            'data_source' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
