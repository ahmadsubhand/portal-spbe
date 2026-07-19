<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileDocument extends Model
{
    public const TYPES = [
        'competency_certificate' => 'Sertifikat Kompetensi',
        'training_certificate' => 'Sertifikat Pelatihan',
        'assignment_letter' => 'Surat Tugas',
        'other_supporting' => 'Dokumen Pendukung Lain',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
