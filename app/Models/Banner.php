<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    public function getImagemAttribute(){
        return (!empty($this->img) ? (Storage::disk('public')->exists('banners/img1000/'.$this->img) ? ($this->ordem == 1 ? Storage::url('banners/img1000/'.$this->img) : Storage::url('banners/img1000/'.$this->img) ) : '') : '');
    }
}
