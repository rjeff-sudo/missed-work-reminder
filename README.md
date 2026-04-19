# Missed Work Reminder

A minimal Laravel application that automatically sends email reminders to employees who missed work. The system checks attendance records and dispatches reminder emails only to users with no attendance entry for the current day, using an efficient database-level anti-join query.

## Features

- Tracks employee attendance via a dedicated database table
- Detects absences using Eloquent's `whereDoesntHave` — filtering is done at the database level, not in PHP
- Sends automated email reminders only to absent employees
- Built with a custom Artisan console command
- Fully containerized with Docker — no local PHP or MySQL setup required

## Tech Stack

- **Framework:** Laravel 13 (PHP 8.3)
- **Database:** MySQL 8.0
- **Email Testing:** Mailtrap (sandbox)
- **Containerization:** Docker + Docker Compose

## Project Structure

```
app/
├── Console/Commands/SendMissedWorkEminders.php  # Core command logic
├── Mail/MissedWorkReminder.php                  # Mail class
└── Models/
    ├── Attendance.php                           # Attendance model
    └── User.php                                 # User model with attendance relationship

database/
├── migrations/
│   └── ..._create_attendance_table.php          # Attendance table schema
└── seeders/
    ├── UserSeeder.php                           # Sample employee data
    └── AttendanceSeeder.php                     # Marks some users as present

resources/views/emails/
└── missed-work-reminder.blade.php               # Email template

Dockerfile                                       # PHP 8.3 container definition
docker-compose.yml                               # Laravel + MySQL service orchestration
```

## How It Works

```
Docker spins up Laravel + MySQL → Migrations run → Users seeded →
Command runs → Anti-join query finds absent users → Emails sent
```

The system uses Eloquent's `whereDoesntHave` method to perform an **anti-join** against the attendance table. This means only users with no attendance record for the current date are returned — the filtering happens entirely at the SQL level, making it efficient even at scale.

```php
$absentUsers = User::whereDoesntHave('attendance', function ($query) {
    $query->whereDate('date', now()->today());
})->get();
```

## Quick Start (Docker)

### Requirements
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/)

### 1. Clone the repository

```bash
git clone https://github.com/rjeff-sudo/missed-work-reminder.git
cd missed-work-reminder
```

### 2. Start the containers

```bash
docker compose up --build
```

This automatically:
- Starts a MySQL 8.0 container
- Creates the `missed_work_reminder` database
- Runs all migrations
- Seeds sample users and attendance records
- Starts the Laravel development server at `http://localhost:8000`

### 3. Run the reminder command

Open a second terminal and run:

```bash
docker compose exec app php artisan remind:missed-work
```

### Expected output

```
Checking attendance for 3 users...
Found 2 absent users. Sending emails...
Reminder sent to: Alice Wanjiru
Reminder sent to: Brian Omondi
Done. Total reminders sent: 2
```

Jeff is skipped because he has an attendance record for today. Alice and Brian have no record so they receive reminder emails.

## Email Testing

Emails are captured by [Mailtrap](https://mailtrap.io) and never sent to real inboxes. To view sent emails:

1. Log in to your Mailtrap account
2. Go to **Email Testing** → **Inboxes** → **My Inbox**
3. You will see the reminder emails sent to absent users

## Manual Setup (Without Docker)

### Requirements
- PHP 8.3+
- Composer
- MySQL
- A Mailtrap account

### Steps

```bash
git clone https://github.com/rjeff-sudo/missed-work-reminder.git
cd missed-work-reminder
composer install
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database and Mailtrap credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=missed_work_reminder
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Then run:

```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=AttendanceSeeder
php artisan remind:missed-work
```

## Scheduling (Optional)

To run the command automatically every day, add this to `app/Console/Kernel.php`:

```php
$schedule->command('remind:missed-work')->dailyAt('09:00');
```

Then set up a cron job on your server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Sample Data

The seeders create the following test scenario:

| Employee | Attendance Today | Action |
|---|---|---|
| Jeff Otieno | ✅ Present | No email sent |
| Alice Wanjiru | ❌ Absent | Reminder email sent |
| Brian Omondi | ❌ Absent | Reminder email sent |

## Author

**Jeff** — [github.com/rjeff-sudo]
