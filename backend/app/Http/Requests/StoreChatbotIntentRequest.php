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
}
