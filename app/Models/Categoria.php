<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    public function artistas(){
        return $this->hasMany(Artistas::class,'categoria_id','id');
    }

    public function banners(){
        return $this->hasMany(Banner::class, 'categoria_id','id');
    }
}
