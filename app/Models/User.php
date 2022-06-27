<?php

namespace App\Models;

use App\Traits\HasULID;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasULID, HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'username',
        'password',
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
    ];

    protected $appends = ['role'];

    public function getRoleAttribute() {
        return $this->getRoleNames()[0];
    }

    public function profile() {
        if($this->getRoleNames()[0] == 'patient'){
            return $this->hasOne(Patient::class);
        }
        if($this->getRoleNames()[0] == 'psychologist'){
            return $this->hasOne(Psychologist::class);
        }
    }

    public function patients() {
        return $this->hasMany(Patient::class);
    }

    public function psychologists() {
        return $this->hasMany(Psychologist::class);
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function isAdmin() {
        return ($this->role == 'admin');
    }

    public function isPsychologist() {
        return ($this->role == 'psychologist');
    }

    public function isPatient() {
        return ($this->role == 'patient');
    }
}
