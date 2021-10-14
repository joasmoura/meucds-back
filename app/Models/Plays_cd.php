<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plays_cd extends Model
{
    use HasFactory;

    protected $fillable = ['num_play', 'cd_id'];
}
