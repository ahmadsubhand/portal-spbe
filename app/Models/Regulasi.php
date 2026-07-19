<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulasi extends Model
{
    use HasFactory;

    // Kode protected $fillable HARUS berada di dalam kurung kurawal class ini
    protected $fillable = ['nomor', 'tahun', 'tentang', 'file_path'];
}