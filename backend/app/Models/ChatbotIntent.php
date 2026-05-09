<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotIntent extends Model
{
    protected $fillable = [
        'intent_key',
        'name',
        'keywords',
        'response_template',
        'data_source',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ChatbotLog::class, 'matched_intent_id');
    }
}
