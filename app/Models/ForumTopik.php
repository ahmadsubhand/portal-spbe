<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumTopik extends Model
{
    use HasFactory;

    // Pastikan kode ini berada DI DALAM kurung kurawal class ForumTopik
    protected $fillable = ['user_id', 'judul_topik', 'deskripsi'];

    public function komentars() {
        return $this->hasMany(ForumKomentar::class, 'forum_topik_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}