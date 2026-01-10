<?php

namespace App\Contracts;

use App\Models\Lecture;
use Illuminate\Database\Eloquent\Collection;

interface LectureRepositoryInterface
{
    /**
     * Find user by ID.
     */
    public function findById(int $id): ?Lecture;

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?Lecture;

    /**
     * Get all lecture.
     */
    public function all(): Collection;

    /**
     * Create a new lecture.
     */
    public function create(array $data): Lecture;

    /**
     * Update lecture.
     */
    public function update(Lecture $lecture, array $data): Lecture;

    /**
     * Delete lecture.
     */
    public function delete(Lecture $lecture): bool;
}
