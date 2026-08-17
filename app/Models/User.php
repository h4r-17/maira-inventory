<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function hasRole(string | array $roles): bool
    {
        // ambil role user dari relasi role
        $userRole = $this->role?->nama_role;

        // super admin dapat memiliki semua akses
        if ($userRole === 'Super Admin') {
            return true;
        }

        // mengecek lebih dari 1 role untuk halaman yang bisa diakses oleh beberapa role
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }

        // jika parameter hanya 1 string role
        return $userRole === $roles;
    }
}
