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

    /**
     * Get the user account associated with this lecturer
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function user()
    {
        return $this->morphOne(User::class, 'linked');
    }

    /**
     * Alternative: Get user by checking linked_id and linked_type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userAccount()
    {
        return $this->hasOne(User::class, 'linked_id')
                    ->where('linked_type', self::class);
    }
}
