<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatbotLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'matched_intent' => $this->intent?->intent_key ?? $this->intent?->name,
            'user' => $this->whenLoaded('user', fn () => ['id' => $this->user_id, 'name' => $this->user?->name]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
