<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    protected $fillable = [
        'nidn',
        'name',
        'expertise',
        'academic_rank',
        'is_active'
    ];
}
