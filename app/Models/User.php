<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar_path',
        'nip',
        'phone_number',
        'unit_kerja',
        'jabatan',
        'bidang_keahlian',
        'bio',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function artikels()
    {
        return $this->hasMany(Artikel::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function deactivationRequests()
    {
        return $this->hasMany(AccountDeactivationRequest::class);
    }

    public function profileDocuments()
    {
        return $this->hasMany(ProfileDocument::class);
    }

    public function pendingDeactivationRequest()
    {
        return $this->hasOne(AccountDeactivationRequest::class)
            ->where('status', AccountDeactivationRequest::STATUS_PENDING)
            ->latestOfMany();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAsn(): bool
    {
        return $this->role === 'asn';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
