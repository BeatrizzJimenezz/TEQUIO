<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EventComponentReminder extends Notification
{
    use Queueable;

    protected $component;
    protected $event;

    public function __construct($component, $event)
    {
        $this->component = $component;
        $this->event = $event;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recordatorio: Actividad programada para mañana')
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Recibes este mensaje porque estás inscrito(a) en una actividad próxima.')
            ->line('**Evento:** ' . $this->event->name)
            ->line('**Actividad:** ' . $this->component->name)
            ->line('**Descripción:** ' . $this->component->description)
            ->line('**Fecha:** ' . $this->component->start_date)
            ->line('**Hora de inicio:** ' . $this->component->start_time)
            ->action('Ver evento', url('/events/' . $this->event->id))
            ->line('¡Nos vemos pronto! Gracias por ser parte de nuestra comunidad.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'component_id' => $this->component->id,
        ];
    }
}
