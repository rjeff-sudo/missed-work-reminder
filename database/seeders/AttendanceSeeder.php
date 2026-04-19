<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Jeff (user_id = 1) - Present
        DB::table('attendance')->insert([
            'user_id' => 1,
            'date' => Carbon::today()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sarah (user_id = 4) - Present
        DB::table('attendance')->insert([
            'user_id' => 4,
            'date' => Carbon::today()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Alice (user_id = 2), Brian (user_id = 3), David (user_id = 5)
        // left with NO attendance record — they missed work
        $this->command->info('Jeff and Sarah marked as present. Alice, Brian and David are absent.');
    }
}
