<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo',
        'url'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFotoAttribute () {
        return (!empty($this->foto_usuario) ? (Storage::disk('public')->exists("usuarios/perfil/".$this->foto_usuario) ? Storage::url("usuarios/perfil/".$this->foto_usuario) : '') : '');
    }

    public function getBannerAttribute () {
        return (!empty($this->banner_usuario) ? (Storage::disk('public')->exists("usuarios/banner/".$this->banner_usuario) ? Storage::url("usuarios/banner/".$this->banner_usuario) : '') : '');
    }

    public function cds(){
        return $this->hasMany(Cd::class, 'user_id', 'id');
    }

}
