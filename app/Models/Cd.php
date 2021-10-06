<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cd extends Model
{
    use HasFactory;

    public function musicas(){
        return $this->hasMany(Musica::class, 'cd_id','id');
    }

    public function getCapaMiniAttribute(){
        return (!empty($this->img) ? (Storage::disk('public')->exists("cds/img240/".$this->img) ? Storage::url("cds/img240/".$this->img) : '') : '');
    }
}
