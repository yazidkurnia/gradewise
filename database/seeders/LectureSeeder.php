<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lectures')->insert([
            [
                'nidn' => '012345678901234',
                'name' => 'Dr. Andi Wijaya',
                'expertise' => 'Software Engineering',
                'academic_rank' => 3,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nidn' => '098765432109876',
                'name' => 'Prof. Budi Santoso',
                'expertise' => 'Data Science',
                'academic_rank' => 5,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nidn' => null,
                'name' => 'Siti Rahma, M.Kom',
                'expertise' => 'Information Systems',
                'academic_rank' => 2,
                'is_active' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
