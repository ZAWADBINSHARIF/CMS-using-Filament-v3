<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasDatabaseNotifications;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
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


    public function canAccessPanel(Panel $panel): bool
    {

        $accessableEmail = ['admin@email.com', 'zawad@email.com', 'tester@email.com'];

        return in_array($this->email, $accessableEmail);
    }

    public function isAdmin()
    {
        return UserRole::Admin === $this->role;
    }

    public function isEditor()
    {
        return UserRole::Editor === $this->role;
    }

    public function isViewer()
    {
        return UserRole::Viewer === $this->role;
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
            'role' => UserRole::class
        ];
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'user_post')->withPivot("order")->withTimestamps();
    }
}
