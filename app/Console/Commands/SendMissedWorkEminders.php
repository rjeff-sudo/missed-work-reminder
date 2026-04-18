<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\MissedWorkReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendMissedWorkEminders extends Command
{
    protected $signature = 'remind:missed-work';
    protected $description = 'Send email reminders to users who missed work today';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $users = User::all();

        $count = 0;

        foreach ($users as $user) {
            $attended = $user->attendance()
                ->whereDate('date', $today)
                ->exists();

            if (!$attended) {
                Mail::to($user->email)->send(new MissedWorkReminder($user));
                $this->info("Reminder sent to: " . $user->name);
                $count++;
                sleep(15);
            }
        }

        if ($count === 0) {
            $this->info("No missed users found for today.");
        } else {
            $this->info("Total reminders sent: " . $count);
        }
    }
}
