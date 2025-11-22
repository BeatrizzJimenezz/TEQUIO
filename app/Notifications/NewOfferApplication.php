<?php

namespace App\Notifications;

use App\Models\EventComponent;
use App\Models\OfferApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOfferApplication extends Notification
{
    use Queueable;

    protected $offer;
    protected $application;
    protected $applicant;

    public function __construct(EventComponent $offer, OfferApplication $application, $applicant)
    {
        $this->offer = $offer;
        $this->application = $application;
        $this->applicant = $applicant;
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
            ->subject('Nueva postulación recibida para: ' . $this->offer->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Has recibido una nueva postulación para tu oferta **' . $this->offer->name . '** en el evento **' . $this->offer->event->name . '**.')
            ->line('---')
            ->line('**Detalles de la oferta:**')
            ->line('**Nombre:** ' . $this->offer->name)
            ->line('**Tipo:** ' . ($typeLabels[$this->offer->type] ?? $this->offer->type))
            ->line('**Modalidad:** ' . ($modalityLabels[$this->offer->modality] ?? $this->offer->modality))
            ->line('---')
            ->line('**Postulante:**')
            ->line('**Nombre:** ' . $this->applicant->name)
            ->line('**Email:** ' . $this->applicant->email);

        // Agregar mensaje del postulante si existe
        if ($this->application->message) {
            $mail->line('---')
                 ->line('**Mensaje del postulante:**')
                 ->line($this->application->message);
        }

        // Información del perfil profesional
        $profile = $this->applicant->professionalProfile;
        if ($profile) {
            $mail->line('---')
                 ->line('**Perfil profesional:**');

            if ($profile->headline) {
                $mail->line('**Titular:** ' . $profile->headline);
            }

            if ($profile->bio) {
                $mail->line('**Bio:** ' . \Str::limit($profile->bio, 200));
            }
        }

        return $mail
            ->line('---')
            ->action('Revisar Postulaciones', url('/my-events/' . $this->offer->event_id . '/evaluation'))
            ->line('Por favor, revisa esta postulación y acepta o rechaza al candidato desde el panel de evaluación.')
            ->salutation('Saludos, el equipo de TEQUIO');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'offer_id' => $this->offer->id,
            'application_id' => $this->application->id,
            'applicant_id' => $this->applicant->id,
        ];
    }
}
