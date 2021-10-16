<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nome','icone', 'texto','url','ordem','bloqueio'];

    public function artistas(){
        return $this->hasMany(Artistas::class,'categoria_id','id');
    }

    public function banners(){
        return $this->hasMany(Banner::class, 'categoria_id','id');
    }

    public function getImgAttribute(){
        return (!empty($this->icone) ? (Storage::disk('public')->exists('categorias/'.$this->icone) ? Storage::url('categorias/'.$this->icone) : '') : '');
    }
}
