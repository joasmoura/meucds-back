<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cd extends Model
{
    use HasFactory;
    protected $table = 'cds';
    protected $fillable = [
        'artista', 'titulo', 'youtube', 'categoria_id', 'texto', 'img', 'status', 'url', 'data_publicacao',
        'data_lancamento', 'lancamento', 'hora_publicacao', 'publicacao', 'tipo_publicacao', 'user_id'
    ];

    public function musicas(){
        return $this->hasMany(Musica::class, 'cd_id','id');
    }

    public function getCapaMiniAttribute(){
        return (!empty($this->img) ? (Storage::disk('public')->exists("cds/".$this->img) ? Storage::url("cds/".$this->img) : '') : '');
    }

    public function publicidade(){
        return $this->hasMany(Publicidade::class, 'cd_id','id');
    }

    public function downloads(){
        return $this->hasMany(Downloads_cd::class, 'cd_id','id');
    }

    public function reproducoes(){
        return $this->hasMany(Plays_cd::class, 'cd_id','id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id','user_id');
    }
}
