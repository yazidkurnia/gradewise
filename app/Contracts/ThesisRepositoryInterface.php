<?php

namespace App\Contracts;

use App\Models\Thesis;
use Illuminate\Database\Eloquent\Collection;

/**
 * @interface ThesisRepositoryInterface
 */
interface ThesisRepositoryInterface
{
    /**
     * Fetch all thesis data
     *
     * @return Collection
     */
    public function fetch_all(): Collection;

    /**
     * Find thesis by ID
     *
     * @param int $id
     * @return Thesis|null
     */
    public function findById(int $id): ?Thesis;

    public function fetch_all_with_student(): Collection;

    public function fetch_with_student(int $studentId): ?Thesis;
}