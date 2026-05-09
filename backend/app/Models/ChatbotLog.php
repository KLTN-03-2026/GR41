<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'matched_intent_id', 'question', 'answer', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function intent(): BelongsTo
    {
        return $this->belongsTo(ChatbotIntent::class, 'matched_intent_id');
    }
}
