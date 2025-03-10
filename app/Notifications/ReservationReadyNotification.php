<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Votre réservation de {$this->reservation->item->name} est disponible")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Bonne nouvelle ! L'objet que vous avez réservé est maintenant disponible.")
            ->line("Item: {$this->reservation->item->name}")
            ->line("Date de réservation: {$this->reservation->reservation_date->format('d/m/Y')}")
            ->line("Date d'expiration: {$this->reservation->expiry_date->format('d/m/Y')}")
            ->action('Voir les détails', url('/reservations/' . $this->reservation->id))
            ->line("Vous avez jusqu'au {$this->reservation->expiry_date->format('d/m/Y')} pour venir chercher l'objet, après quoi la réservation sera annulée.");
    }
}
