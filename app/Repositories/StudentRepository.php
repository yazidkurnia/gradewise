<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface {
    public function fetch_all(): Collection {
        return Student::all();
    }
}