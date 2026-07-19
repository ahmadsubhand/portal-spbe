<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'category_id',
        'judul',
        'konten',
        'status',
        'is_draft',
        'review_note',
        'submitted_at',
        'approved_at',
        'published_at',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'artikel_tag');
    }

    public function scopePublished($query)
    {
        return $query
            ->where('is_draft', false)
            ->where('status', self::STATUS_APPROVED)
            ->whereNotNull('published_at');
    }

    public function scopePendingReview($query)
    {
        return $query
            ->where('is_draft', false)
            ->where('status', self::STATUS_PENDING);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPublished(): bool
    {
        return $this->isApproved() && $this->published_at !== null;
    }
}
