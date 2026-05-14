<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table      = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'username',
        'password_hash',
        'nama_lengkap',
        'email',
        'telepon',
        'role',
        'api_key',
    ];

    protected $hidden = [
        'password_hash',
        'api_key',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims(): array
    {
        return [
            'role'         => $this->role,
            'username'     => $this->username,
            'nama_lengkap' => $this->nama_lengkap,
        ];
    }
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isKasir(): bool    { return $this->role === 'kasir'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'customer_id', 'id_user');
    }
}