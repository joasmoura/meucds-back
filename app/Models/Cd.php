<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cd extends Model
{
    use HasFactory;

    public function musicas(){
        return $this->hasMany(Musica::class, 'cd_id','id');
    }
}
