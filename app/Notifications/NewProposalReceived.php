<?php

namespace App\Notifications;

use App\Models\EventComponent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProposalReceived extends Notification
{
    use Queueable;

    protected $component;
    protected $proposer;

    public function __construct(EventComponent $component, $proposer)
    {
        $this->component = $component;
        $this->proposer = $proposer;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
        $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];

        $mail = (new MailMessage)
            ->subject('Nueva propuesta recibida para ' . $this->component->event->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Has recibido una nueva propuesta para tu evento **' . $this->component->event->name . '**.')
            ->line('---')
            ->line('**Detalles de la propuesta:**')
            ->line('**Nombre:** ' . $this->component->name)
            ->line('**Tipo:** ' . ($typeLabels[$this->component->type] ?? $this->component->type))
            ->line('**Modalidad:** ' . ($modalityLabels[$this->component->modality] ?? $this->component->modality))
            ->line('**Descripción:** ' . \Str::limit($this->component->description, 200))
            ->line('---')
            ->line('**Propuesto por:**')
            ->line('**Nombre:** ' . $this->proposer->name)
            ->line('**Email:** ' . $this->proposer->email);

        // Agregar horarios propuestos
        if ($this->component->schedules->count() > 0) {
            $schedules = $this->component->schedules->map(function ($schedule) {
                return $schedule->date->format('d/m/Y') . ' de ' .
                       \Carbon\Carbon::parse($schedule->start_time)->format('H:i') . ' a ' .
                       \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
            })->implode(', ');

            $mail->line('**Horarios propuestos:** ' . $schedules);
        }

        return $mail
            ->line('---')
            ->action('Revisar Propuesta', url('/my-events/' . $this->component->event_id . '/evaluation'))
            ->line('Por favor, revisa esta propuesta y apruébala o recházala desde el panel de evaluación.')
            ->salutation('Saludos, el equipo de TEQUIO');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'component_id' => $this->component->id,
            'event_id' => $this->component->event_id,
            'proposer_id' => $this->proposer->id,
        ];
    }
}
