<?php

namespace App\Contracts;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface StudentRepositoryInterface 
{
    /**
     * Find user by ID
     */
    public function findById(int $id): ?Student;

    /**
     * Get all student
     */
    public function all(): Collection;

    /**
     * Create a new student
     */
    public function create(array $data): Student;

    /**
     * Update student
     */
    public function update(Student $student, array $data): Student;

    /**
     * Delete student
     */
    public function delete(Student $student): bool;

}