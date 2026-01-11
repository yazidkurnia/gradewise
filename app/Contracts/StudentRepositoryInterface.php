<?php

namespace App\Contracts;

use App\Models\Lecture;
use Illuminate\Database\Eloquent\Collection;

interface StudentRepositoryInterface {
    public function fetch_all(): ?Collection;

}