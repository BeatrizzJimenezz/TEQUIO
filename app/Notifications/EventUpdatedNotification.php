<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventUpdatedNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $changes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, array $changes)
    {
        $this->event = $event;
        $this->changes = $changes;
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
        // Traducciones de campos
        $fieldNames = [
            'name'        => 'Nombre',
            'start_date'  => 'Fecha de inicio',
            'end_date'    => 'Fecha de finalización',
            'start_time'  => 'Hora de inicio',
            'description' => 'Descripción',
            'modality'    => 'Modalidad',
            'location'    => 'Ubicación',
            'visibility'  => 'Visibilidad',
            'status'      => 'Estado',
            'cover_image' => 'Imagen de portada',
            'logo'        => 'Logo',
        ];

        // Traducciones de valores
        $valueTranslate = [
            'virtual'    => 'Virtual',
            'in_person'  => 'Presencial',
            'hybrid'     => 'Híbrido',
            'public'     => 'Público',
            'private'    => 'Privado',
            'planning'   => 'Planificación',
            'active'     => 'Activo',
            'finished'   => 'Finalizado',
        ];

        $mail = (new MailMessage)
            ->subject('Actualización del evento: ' . $this->event->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El evento en el que participas ha sido actualizado.')
            ->line('Evento: **' . $this->event->name . '**')
            ->line('Cambios realizados:');

        // Procesar cada cambio detectado
        foreach ($this->changes as $field => $change) {

            $old = $change['old'];
            $new = $change['new'];

            /* -------------------------
             * Normalización de valores
             * ------------------------- */

            // Convertir fechas Carbon → texto
            if ($old instanceof \Carbon\Carbon) {
                $old = $old->format('Y-m-d H:i');
            }
            if ($new instanceof \Carbon\Carbon) {
                $new = $new->format('Y-m-d H:i');
            }

            // Si vienen en formato ISO (2025-11-21T00:00:00Z)
            if (is_string($old) && str_contains($old, 'T')) {
                $old = date('Y-m-d', strtotime($old));
            }
            if (is_string($new) && str_contains($new, 'T')) {
                $new = date('Y-m-d', strtotime($new));
            }

            // Arrays → JSON
            if (is_array($old)) {
                $old = json_encode($old, JSON_UNESCAPED_UNICODE);
            }
            if (is_array($new)) {
                $new = json_encode($new, JSON_UNESCAPED_UNICODE);
            }

            // Objetos raros → string
            if (is_object($old)) {
                $old = (string) $old;
            }
            if (is_object($new)) {
                $new = (string) $new;
            }

            // Aplicar traducciones si existen
            $old = $valueTranslate[$old] ?? $old;
            $new = $valueTranslate[$new] ?? $new;

            // Nombre amigable del campo
            $label = $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));

            $mail->line("• **{$label}** cambió de **{$old}** a **{$new}**");
        }

        return $mail
            ->action('Ver evento', url('/events/' . $this->event->id))
            ->line('Revisa los detalles actualizados del evento.');
    }

    /**
     * Notification array.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'changes' => $this->changes,
        ];
    }
}
