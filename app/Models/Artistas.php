<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Artistas extends Model
{
    use HasFactory;

    public function foto($tamanho){
        return (!empty($this->img) ? (Storage::disk('public')->exists("cantores/{$tamanho}/".$this->img) ? Storage::url("cantores/{$tamanho}/".$this->img) : '') : '');
    }

    public function cds(){
        return $this->hasMany(Cd::class,'artista_id','id');
    }
}
