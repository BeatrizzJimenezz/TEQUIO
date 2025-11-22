<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreated extends Notification
{
    use Queueable;

    protected $tempPassword;

    public function __construct(string $tempPassword)
    {
        $this->tempPassword = $tempPassword;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu cuenta en TEQUIO ha sido creada')
            ->greeting('¡Bienvenido/a ' . $notifiable->name . '!')
            ->line('Se ha creado una cuenta para ti en la plataforma **TEQUIO**.')
            ->line('A continuación encontrarás tus datos de acceso:')
            ->line('---')
            ->line('**Correo electrónico:** ' . $notifiable->email)
            ->line('**Contraseña temporal:** ' . $this->tempPassword)
            ->line('---')
            ->line('Por seguridad, deberás cambiar tu contraseña en tu primer inicio de sesión.')
            ->action('Iniciar Sesión', url('/login'))
            ->line('Si no solicitaste esta cuenta, puedes ignorar este mensaje.')
            ->salutation('Saludos, el equipo de TEQUIO');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
