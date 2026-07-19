<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumKomentar extends Model
{
    use HasFactory;

    // Pastikan kode ini berada DI DALAM kurung kurawal class ForumKomentar
    protected $fillable = ['forum_topik_id', 'user_id', 'isi_komentar'];

    public function user() {
        return $this->belongsTo(User::class);
    }

}