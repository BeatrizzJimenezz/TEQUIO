<?php

namespace App\Notifications;

use App\Models\RoleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleRequestProcessed extends Notification
{
    use Queueable;

    protected $roleRequest;
    protected $status;

    public function __construct(RoleRequest $roleRequest, string $status)
    {
        $this->roleRequest = $roleRequest;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->greeting('Hola ' . $notifiable->name . ',');

        if ($this->status === 'approved') {
            $mail->subject('¡Tu solicitud de Organizador ha sido aprobada!')
                 ->line('¡Felicidades! Tu solicitud para convertirte en **Organizador** ha sido **aprobada**.')
                 ->line('Ahora puedes crear y gestionar tus propios eventos en la plataforma.')
                 ->action('Crear mi primer evento', url('/events/create'));

            if ($this->roleRequest->admin_notes) {
                $mail->line('**Nota del administrador:** ' . $this->roleRequest->admin_notes);
            }

        } else {
            $mail->subject('Tu solicitud de Organizador ha sido rechazada')
                 ->line('Lamentamos informarte que tu solicitud para convertirte en **Organizador** ha sido **rechazada**.');

            if ($this->roleRequest->admin_notes) {
                $mail->line('**Motivo:** ' . $this->roleRequest->admin_notes);
            }

            $mail->line('Puedes enviar una nueva solicitud en el futuro.')
                 ->action('Enviar nueva solicitud', url('/role-requests/create'));
        }

        return $mail->line('Gracias por tu interés en contribuir a TEQUIO.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'role_request_id' => $this->roleRequest->id,
            'status' => $this->status,
        ];
    }
}
