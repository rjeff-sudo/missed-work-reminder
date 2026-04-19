<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\MissedWorkReminder;
use Illuminate\Support\Facades\Mail;

class SendMissedWorkEminders extends Command
{
    protected $signature = 'remind:missed-work';
    protected $description = 'Send email reminders to users who missed work today';

    public function handle()
    {
        $this->info("Checking attendance for " . User::count() . " users...");

        $absentUsers = User::whereDoesntHave('attendance', function ($query) {
            $query->whereDate('date', now()->today());
        })->get();

        if ($absentUsers->isEmpty()) {
            $this->info("All users attended today. No reminders needed.");
            return;
        }

        $this->info("Found " . $absentUsers->count() . " absent users. Sending emails...");

        foreach ($absentUsers as $user) {
            Mail::to($user->email)->send(new MissedWorkReminder($user));
            $this->info("Reminder sent to: " . $user->name);
           
        }

        $this->info("Done. Total reminders sent: " . $absentUsers->count());
    }
}
