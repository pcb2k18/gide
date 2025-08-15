<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// ⬇️ add these
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password'];
    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ⬇️ authorize who can access the Filament panel
    public function canAccessPanel(Panel $panel): bool
    {
        // QUICK fix: allow any authenticated user
        // return true;

        // Better: restrict by email(s) or a flag/role
        return in_array($this->email, [
            'info@ghanainsider.com', // <-- change to your admin email(s)
        ]);
    }
}
