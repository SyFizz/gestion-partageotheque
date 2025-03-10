<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $loan;
    protected $daysRemaining;

    public function __construct(Loan $loan, $daysRemaining)
    {
        $this->loan = $loan;
        $this->daysRemaining = $daysRemaining;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->daysRemaining > 0) {
            $subject = "Rappel : Retour de {$this->loan->item->name} dans {$this->daysRemaining} jour(s)";
            $line1 = "Nous vous rappelons que votre emprunt de {$this->loan->item->name} arrive à échéance dans {$this->daysRemaining} jour(s).";
        } else if ($this->daysRemaining == 0) {
            $subject = "Rappel : Retour de {$this->loan->item->name} aujourd'hui";
            $line1 = "Nous vous rappelons que votre emprunt de {$this->loan->item->name} arrive à échéance aujourd'hui.";
        } else {
            $subject = "Rappel : Retour de {$this->loan->item->name} en retard";
            $line1 = "Nous vous rappelons que votre emprunt de {$this->loan->item->name} est en retard de " . abs($this->daysRemaining) . " jour(s).";
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Bonjour {$notifiable->name},")
            ->line($line1)
            ->line("Date d'emprunt : {$this->loan->loan_date->format('d/m/Y')}")
            ->line("Date de retour prévue : {$this->loan->due_date->format('d/m/Y')}")
            ->action('Voir les détails', url('/loans/' . $this->loan->id))
            ->line('Merci de votre attention.');
    }
}
