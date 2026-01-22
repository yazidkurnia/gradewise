<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface 
{
    /**
     * Find student by ID.
     */
    public function findById(int $id): ?Student
    {
        return Student::find($id);
    }

    /**
     * Get all students.
     */
    public function all(): Collection
    {
        return Student::all();
    }

    /**
     * Create a new student.
     */
    public function create(array $data): Student
    {
        return Student::create($data);
    }

    /**
     * Update student.
     */
    public function update(Student $student, array $data): Student
    {
        $student->update($data);
        return $student->fresh();
    }

    /**
     * Delete student.
     */
    public function delete(Student $student): bool
    {
        return $student->delete();
    }
}