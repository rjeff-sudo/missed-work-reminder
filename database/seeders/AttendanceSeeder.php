<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Mark Jeff as present today (user_id = 1)
        DB::table('attendance')->insert([
            'user_id' => 1,
            'date' => Carbon::today()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Alice (user_id = 2) and Brian (user_id = 3)
        // are left with NO attendance record — they missed work
        $this->command->info('Jeff marked as present. Alice and Brian are absent.');
    }
}
