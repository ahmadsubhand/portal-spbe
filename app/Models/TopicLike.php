<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicLike extends Model
{
    protected $fillable = ['topic_id', 'user_id'];
}
