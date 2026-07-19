<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public const PUBLIC_CONTACT_EMAIL = 'public_contact_email';
    public const PUBLIC_SUPPORT_LABEL = 'public_support_label';
    public const DASHBOARD_EMBED_URL = 'dashboard_embed_url';
    public const KNOWLEDGE_BASE_STATUS = 'knowledge_base_status';

    protected $fillable = [
        'key',
        'value',
        'updated_by',
    ];

    public static function definitions(): array
    {
        return [
            self::PUBLIC_CONTACT_EMAIL => 'Email kontak publik.',
            self::PUBLIC_SUPPORT_LABEL => 'Label singkat layanan bantuan publik.',
            self::DASHBOARD_EMBED_URL => 'URL embed dashboard untuk integrasi berikutnya.',
            self::KNOWLEDGE_BASE_STATUS => 'Status placeholder basis pengetahuan.',
        ];
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
