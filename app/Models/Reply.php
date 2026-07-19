<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $fillable = ['topic_id', 'parent_id', 'user_id', 'content', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isActive(): bool
    {
        return (bool) ($this->is_active ?? true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function user() { return $this->belongsTo(User::class); }
    public function topic() { return $this->belongsTo(Topic::class); }
    public function parent() { return $this->belongsTo(Reply::class, 'parent_id'); }
    public function children() { return $this->hasMany(Reply::class, 'parent_id')->oldest(); }
    public function likes() { return $this->hasMany(ReplyLike::class); }
}
