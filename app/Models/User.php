<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'linked_id',
        'linked_type',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    /**
     * Get the linked entity (Student or Lecture) - Polymorphic Relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function linkable()
    {
        return $this->morphTo('linked');
    }

    /**
     * Get linked Student if user is a student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'linked_id');
    }

    /**
     * Get linked Lecture if user is a lecturer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lecture()
    {
        return $this->belongsTo(Lecture::class, 'linked_id');
    }

    /**
     * Check if user is a student
     *
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->linked_type === 'App\\Models\\Student';
    }

    /**
     * Check if user is a lecturer
     *
     * @return bool
     */
    public function isLecturer(): bool
    {
        return $this->linked_type === 'App\\Models\\Lecture';
    }

    /**
     * Get the linked profile (Student or Lecture) dynamically
     *
     * @return Student|Lecture|null
     */
    public function getLinkedProfile()
    {
        return $this->linkable;
    }

}
