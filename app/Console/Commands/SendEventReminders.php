<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EventComponent;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send email reminders for event components starting tomorrow';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Buscar componentes que tienen un HORARIO programado para mañana
        $components = EventComponent::whereHas('schedules', function ($q) use ($tomorrow) {
            $q->whereDate('date', $tomorrow);
        })
        ->with(['event', 'schedules', 'registrations.user'])
        ->get();

        if ($components->isEmpty()) {
            $this->info("No hay componentes programados para mañana.");
            return;
        }

        $this->info("Componentes encontrados: " . $components->count());

        $remindersSent = 0;

        foreach ($components as $component) {
            // Obtener los horarios de mañana para este componente
            $tomorrowSchedules = $component->schedules->filter(function ($schedule) use ($tomorrow) {
                return $schedule->date->toDateString() === $tomorrow;
            });

            foreach ($component->registrations as $registration) {
                $user = $registration->user;
                if (!$user) continue;

                $user->notify(
                    new \App\Notifications\EventComponentReminder(
                        $component,
                        $component->event
                    )
                );

                $this->info("Recordatorio enviado a {$user->email} para '{$component->name}'.");
                $remindersSent++;
            }
        }

        $this->info("Total de recordatorios enviados: {$remindersSent}");
    }
}
