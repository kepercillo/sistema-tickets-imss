<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'clues',      
        'department', 
        'password',
        'role',
    ];

    public function hasRole(string $role): bool
    {
        return strtolower($this->role) === strtolower($role);
    }
    
    protected function name(): Attribute
    {
    return Attribute::make(
        set: fn (string $value) => mb_strtoupper($value, 'UTF-8'),
    );
    }
    protected function department(): Attribute
    {   
    return Attribute::make(
        set: fn (?string $value) => $value ? mb_strtoupper($value, 'UTF-8') : null,
    );
    }   

// Mutador para la CLUES en MAYÚSCULAS
protected function clues(): Attribute
{
    return Attribute::make(
        set: fn (?string $value) => $value ? mb_strtoupper($value, 'UTF-8') : null,
    );
}

// Relación: Los tickets que ha creado este usuario
public function tickets()
    {
    return $this->hasMany(Ticket::class, 'user_id');
    }

// Relación: Los tickets que tiene asignados atender (si es soporte)
public function assignedTickets()
    {
    return $this->hasMany(Ticket::class, 'assigned_to');
    }

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
}
