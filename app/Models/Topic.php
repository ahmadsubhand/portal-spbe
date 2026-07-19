<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Topic extends Model
{
    protected $fillable = ['user_id', 'title', 'slug', 'content', 'category', 'visibility', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isOpen(): bool
    {
        return $this->visibility === 'open';
    }

    public function isActive(): bool
    {
        return (bool) ($this->is_active ?? true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Otomatis membuat slug sebelum disimpan ke DB
    protected static function boot() {
        parent::boot();
        static::creating(function ($topic) {
            $topic->slug = Str::slug($topic->title) . '-' . rand(1000, 9999);
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function replies() { return $this->hasMany(Reply::class); }
    public function likes() { return $this->hasMany(TopicLike::class); }
}
