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

        // Buscar componentes donde el EVENTO ocurre mañana
        $components = EventComponent::whereHas('event', function ($q) use ($tomorrow) {
            $q->whereDate('start_date', $tomorrow);
        })
        ->with(['event', 'registrations.user'])
        ->get();

        if ($components->isEmpty()) {
            $this->info("No components scheduled for tomorrow.");
            return;
        }

        $this->info("Components found: " . $components->count());

        foreach ($components as $component) {
            foreach ($component->registrations as $registration) {

                $user = $registration->user;
                if (!$user) continue;

                $user->notify(
                    new \App\Notifications\EventComponentReminder(
                        $component,
                        $component->event
                    )
                );

                $this->info("Reminder sent to {$user->email} for component '{$component->name}'.");
            }
        }

        $this->info("All reminders sent successfully.");
    }
}
