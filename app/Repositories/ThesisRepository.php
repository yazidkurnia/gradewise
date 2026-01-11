<?php

namespace App\Repositories;

use App\Models\Thesis;
use App\Contracts\ThesisRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ThesisRepository implements ThesisRepositoryInterface
{
    /**
     * Fetch all thesis data
     *
     * @return Collection
     */
    public function fetch_all(): Collection
    {
        return Thesis::with('student')->whereNull('deleted_at')->get();
    }

    /**
     * Find thesis by ID
     *
     * @param int $id
     * @return Thesis|null
     */
    public function findById(int $id): ?Thesis
    {
        return Thesis::find($id);
    }

    /**
     * Fetch all thesis data with student relationship
     *
     * @return Collection
     */
    public function fetch_all_with_student(): Collection
    {
        return Thesis::with('student')
                     ->whereNull('deleted_at')
                     ->get();
    }

    /**
     * Fetch student data
     * 
     * @return Thesis with param
     */
    public function fetch_with_student(int $id): Thesis{
        return Thesis::where('student_id', $studentId)->first();
    }   
}