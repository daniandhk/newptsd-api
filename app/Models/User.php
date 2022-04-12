<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
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

    protected $appends = ['is_profile_set', 'role'];

    public function getIsProfileSetAttribute() {
        $is_profile_set = false;
        // if($this->getRoleNames()[0] == 'patient'){
        //     if(Patient::where('user_id', $this->id)->first()){
        //         $this->is_profile_set = true;
        //     }
        // }
        // if($this->getRoleNames()[0] == 'psychologist'){
        //     if(Psychologist::where('user_id', $this->id)->first()){
        //         $this->is_profile_set = true;
        //     }
        // }
        return $is_profile_set;
    }

    public function getRoleAttribute() {
        $role = $this->getRoleNames()[0];
        return $role;
    }
}
