<?php

namespace App\Notifications;

use App\Models\EventComponent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderTomorrowNotification extends Notification
{
    use Queueable;

    protected $component;

    /**
     * Create a new notification instance.
     */
    public function __construct(EventComponent $component)
    {
        $this->component = $component;
    }

    /**
     * Notification channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Mail representation.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recordatorio: Tu actividad inicia mañana')
            ->greeting('Hola' . $notifiable->name . ',')
            ->line('Este es un recordatorio de que estás inscrito(a) en una actividad que se llevará a cabo **mañana**.')
            ->line('Actividad: **'  . $this->component->name . '**')
            ->line('Tipo: ' . ucfirst($this->component->type))
            ->line('Description: ' . $this->component->description)
            ->line('Evento: ' . $this->component->event->name)
            ->line('Fecha de inicio: '  . $this->component->event->start_date->format('Y-m-d'))
            ->action('Ver actividad', url('/events/' . $this->component->event_id))
            ->line('Por favor, prepárate y llega puntual.');
    }

    /**
     * Array representation.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->component->event_id,
            'component_id' => $this->component->id,
        ];
    }
}
