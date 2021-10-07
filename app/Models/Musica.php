<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Musica extends Model
{
    use HasFactory;

    public function getLinkMusicaAttribute(){
        return (!empty($this->link) ? (Storage::disk('public')->exists('musicas/'.$this->link) ? Storage::url('musicas/'.$this->link) : '') : '');
    }
}
