<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'email',
        'password',
        'user_name',
        'type',
        'contact_number', 
        'gender',
        'new',
        'profile_picture_path',
        'birth_date'
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
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userRole() {
        return $this->hasOne(UserRole::class, 'user_id', 'id');
    }
    public function userDriver() {
        return $this->hasOne(UserDriverDetails::class, 'user_id', 'id');
    }
    public function userBusiness() {
        return $this->hasOne(UserBusinessDetails::class, 'user_id', 'id');
    }
    public function userBlocked() {
        return $this->hasOne(UserBlock::class, 'user_id', 'id');
    }
}
