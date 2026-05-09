<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'uploaded_by',
        'proposed_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'status',
        'title',
        'slug',
        'description',
        'author',
        'publisher',
        'published_year',
        'isbn',
        'language',
        'pages',
        'file_url',
        'cover_image',
        'view_count',
        'download_count',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'published_year' => 'integer',
            'is_featured' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'document_tag');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(DocumentView::class);
    }
}
