<?php

namespace App\Repositories;

use App\Models\Lecture;
use App\Contracts\LectureRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LectureRepository implements LectureRepositoryInterface
{
    /**
     * Find lecture by ID.
     */
    public function findById(int $id): ?Lecture
    {
        return Lecture::find($id);
    }

    /**
     * Find lecture by email.
     */
    public function findByEmail(string $email): ?Lecture
    {
        return Lecture::where('email', $email)->first();
    }

    /**
     * Get all lectures.
     */
    public function all(): Collection
    {
        return Lecture::all();
    }

    /**
     * Create a new lecture.
     */
    public function create(array $data): Lecture
    {
        return Lecture::create($data);
    }

    /**
     * Update lecture.
     */
    public function update(Lecture $lecture, array $data): Lecture
    {
        $lecture->update($data);
        return $lecture->fresh();
    }

    /**
     * Delete lecture.
     */
    public function delete(Lecture $lecture): bool
    {
        return $lecture->delete();
    }
}