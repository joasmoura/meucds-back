<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Musica extends Model
{
    use HasFactory;

    protected $table = 'musicas';
    protected $fillable = ['nome', 'link', 'url', 'ordem', 'cd_id'];

    public function getLinkMusicaAttribute(){
        return (!empty($this->link) ? (Storage::disk('public')->exists('musicas/'.$this->link) ? Storage::url('musicas/'.$this->link) : '') : '');
    }

    public function cd(){
        return $this->hasOne(Cd::class, 'id','cd_id');
    }
}
