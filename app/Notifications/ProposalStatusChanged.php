<?php

namespace App\Notifications;

use App\Models\EventComponent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalStatusChanged extends Notification
{
    use Queueable;

    protected $component;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(EventComponent $component, string $newStatus)
    {
        $this->component = $component;
        $this->newStatus = $newStatus;
    }

    /**
     * Delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Email message.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'proposed' => 'En revisión',
            'offer_open' => 'Oferta abierta',
        ];

        $statusLabel = $statusLabels[$this->newStatus] ?? ucfirst($this->newStatus);
        $eventName = $this->component->event->name ?? 'Evento';

        $mail = (new MailMessage)
            ->subject('Tu propuesta ha sido ' . strtolower($statusLabel))
            ->greeting('Hola ' . $notifiable->name . ',');

        if ($this->newStatus === 'approved') {
            $mail->line('¡Felicidades! Tu propuesta ha sido **aprobada**.')
                 ->line('**Componente:** ' . $this->component->name)
                 ->line('**Evento:** ' . $eventName)
                 ->line('Pronto recibirás más información sobre los próximos pasos.')
                 ->action('Ver mis propuestas', url('/proposals/my-proposals'));
        } elseif ($this->newStatus === 'rejected') {
            $mail->line('Lamentamos informarte que tu propuesta ha sido **rechazada**.')
                 ->line('**Componente:** ' . $this->component->name)
                 ->line('**Evento:** ' . $eventName)
                 ->line('Te animamos a seguir participando en futuros eventos.')
                 ->action('Ver otros eventos', url('/events'));
        } else {
            $mail->line('El estado de tu propuesta ha cambiado.')
                 ->line('**Componente:** ' . $this->component->name)
                 ->line('**Evento:** ' . $eventName)
                 ->line('**Nuevo estado:** ' . $statusLabel)
                 ->action('Ver mis propuestas', url('/proposals/my-proposals'));
        }

        return $mail->line('Gracias por tu participación.');
    }

    /**
     * Notification array.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'component_id' => $this->component->id,
            'event_id' => $this->component->event_id,
            'new_status' => $this->newStatus,
        ];
    }
}
