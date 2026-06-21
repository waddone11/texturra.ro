<?php

namespace App\Models;

use App\Enums\UserType;
use App\Notifications\CustomVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type', // Ensure 'type' is mass assignable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'type' => UserType::class, // Correct cast for enums in Laravel
    ];

    public function isRole(string $role): bool
    {
        return $this->type->value === $role;
    }

    /**
     * Filament panel access — mirrors the existing /admin guard
     * (CheckRole:admin,employee). Managers and clients are excluded.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->type, [UserType::ADMIN, UserType::EMPLOYEE], true);
    }

    /**
     * Override the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->type === UserType::ADMIN;
    }
    /**
     * Check if the user is an employee.
     */


    /**
     * Check if the user is a client.
     */
    public function isClient(): bool
    {
        return $this->type === UserType::CLIENT;
    }


    public function isGuest(): bool
    {
        return $this->type === UserType::GUEST;
    }



    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Relationship with Invoice Addresses.
     * A user can have many invoice addresses.
     */
    public function invoiceAddresses()
    {
        return $this->hasMany(InvoiceAddress::class);
    }

    /**
     * Relationship with Orders.
     * A user can have many orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
