<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'nim_nidn', 'email', 'password', 'role', 'is_active', 'foto', 'fakultas', 'program_studi', 'angkatan', 'telepon'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    // Kelas yang diampu (untuk dosen)
    public function kelasDiampu()
    {
        return $this->hasMany(Kelas::class, 'dosen_id');
    }

    // Kelas yang diikuti (untuk mahasiswa)
    public function kelasDiikuti()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mahasiswa', 'mahasiswa_id', 'kelas_id')->withTimestamps();
    }
}
