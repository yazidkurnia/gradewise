<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecture extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nidn',
        'name',
        'expertise',
        'academic_rank',
        'is_active'
    ];
}
