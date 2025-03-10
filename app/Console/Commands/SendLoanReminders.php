<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Notifications\LoanDueReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLoanReminders extends Command
{
    protected $signature = 'loans:send-reminders';
    protected $description = 'Send reminders for loans that are due soon or overdue';

    public function handle()
    {
        $today = Carbon::today();

        // Rappels pour les emprunts qui doivent être rendus dans 3 jours
        $this->sendReminders($today->copy()->addDays(3), 3);

        // Rappels pour les emprunts qui doivent être rendus aujourd'hui
        $this->sendReminders($today, 0);

        // Rappels pour les emprunts en retard de 2 jours
        $this->sendReminders($today->copy()->subDays(2), -2);

        $this->info('Loan reminders have been sent successfully.');

        return 0;
    }

    private function sendReminders($date, $daysRemaining)
    {
        $loans = Loan::whereNull('return_date')
            ->whereDate('due_date', $date)
            ->with(['user', 'item'])
            ->get();

        $this->info("Found {$loans->count()} loans due " . ($daysRemaining > 0 ? "in $daysRemaining days" : ($daysRemaining == 0 ? "today" : abs($daysRemaining) . " days ago")) . ".");

        foreach ($loans as $loan) {
            if ($loan->user) {
                $loan->user->notify(new LoanDueReminder($loan, $daysRemaining));
                $this->info("Reminder sent to {$loan->user->email} for {$loan->item->name}");
            }
        }
    }
}
