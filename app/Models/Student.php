<?php

namespace App\Models;

use App\Models\Thesis;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nim',
        'name',
        'faculty',
        'program',
        'entry_year',
        'status'
    ];

    /**
     * Get the thesis associated with the student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thesis()
    {
        return $this->hasOne(Thesis::class);
    }

    /**
     * Get the user account associated with this student
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
