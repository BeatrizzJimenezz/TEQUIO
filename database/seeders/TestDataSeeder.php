<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProfessionalProfile;
use App\Models\Event;
use App\Models\EventComponent;
use App\Models\ComponentSchedule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get organizer user
        $organizador = User::where('email', 'organizador@tequio.com')->first();

        if (!$organizador) {
            $this->command->error('Organizador user not found. Run UsersRolesSeeder first.');
            return;
        }

        // Create or get professional profile for organizer
        $profile = ProfessionalProfile::firstOrCreate(
            ['user_id' => $organizador->id],
            [
                'about_me' => 'Organizador de eventos tecnológicos y académicos',
                'current_workplace' => 'TEQUIO Events',
                'skills' => 'Gestión de eventos, Coordinación, Planificación',
            ]
        );

        // Create additional users as speakers/presenters
        $speaker1 = User::firstOrCreate(
            ['email' => 'ponente1@tequio.com'],
            [
                'name' => 'Dr. Carlos García',
                'password' => Hash::make('1234'),
            ]
        );
        $speaker1->assignRole('Participante');

        $profileSpeaker1 = ProfessionalProfile::firstOrCreate(
            ['user_id' => $speaker1->id],
            [
                'about_me' => 'Experto en Laravel y desarrollo backend con 10 años de experiencia',
                'current_workplace' => 'Tech Solutions S.A.',
                'skills' => 'PHP, Laravel, MySQL, Docker, AWS',
            ]
        );

        $speaker2 = User::firstOrCreate(
            ['email' => 'ponente2@tequio.com'],
            [
                'name' => 'Ing. María López',
                'password' => Hash::make('1234'),
            ]
        );
        $speaker2->assignRole('Participante');

        $profileSpeaker2 = ProfessionalProfile::firstOrCreate(
            ['user_id' => $speaker2->id],
            [
                'about_me' => 'Especialista en Frontend y experiencia de usuario',
                'current_workplace' => 'UX Design Studio',
                'skills' => 'Vue.js, React, TypeScript, Figma, UX/UI',
            ]
        );

        // Create Event 1: Tech Conference
        $event1 = Event::create([
            'professional_profile_id' => $profile->id,
            'name' => 'Conferencia de Tecnología 2024',
            'description' => 'Una conferencia sobre las últimas tendencias en tecnología, desarrollo de software y metodologías ágiles.',
            'start_date' => Carbon::now()->addDays(10),
            'end_date' => Carbon::now()->addDays(12),
            'start_time' => '09:00:00',
            'modality' => 'hybrid',
            'location' => 'Centro de Convenciones TEQUIO',
            'visibility' => 'public',
            'status' => 'active',
        ]);

        // Components for Event 1
        $component1_1 = EventComponent::create([
            'event_id' => $event1->id,
            'speaker_id' => $profileSpeaker1->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Introducción a Laravel 11',
            'description' => 'Workshop práctico sobre las nuevas características de Laravel 11 y mejores prácticas de desarrollo.',
            'type' => 'workshop',
            'proposal_status' => 'approved',
            'modality' => 'in_person',
            'location' => 'Sala A - Planta Baja',
            'level' => 'intermediate',
            'capacity' => 30,
            'attendee_price' => 0,
            'participant_requirements' => 'Laptop con PHP 8.2+ instalado',
        ]);

        // Schedules for Component 1_1 (Day 1 morning)
        ComponentSchedule::create([
            'component_id' => $component1_1->id,
            'date' => Carbon::now()->addDays(10),
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
        ]);

        $component1_2 = EventComponent::create([
            'event_id' => $event1->id,
            'speaker_id' => $profileSpeaker2->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Vue.js Avanzado',
            'description' => 'Aprende patrones avanzados de Vue.js, composables y state management con Pinia.',
            'type' => 'workshop',
            'proposal_status' => 'approved',
            'modality' => 'in_person',
            'location' => 'Sala B - Planta Baja',
            'level' => 'advanced',
            'capacity' => 25,
            'attendee_price' => 50.00,
            'participant_requirements' => 'Conocimientos previos de Vue.js básico',
        ]);

        // Schedules for Component 1_2 (Day 1 morning - SAME TIME as 1_1 for conflict testing)
        ComponentSchedule::create([
            'component_id' => $component1_2->id,
            'date' => Carbon::now()->addDays(10),
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
        ]);

        $component1_3 = EventComponent::create([
            'event_id' => $event1->id,
            'speaker_id' => $profileSpeaker1->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Arquitectura de Microservicios',
            'description' => 'Conferencia sobre diseño y implementación de arquitecturas basadas en microservicios.',
            'type' => 'talk',
            'proposal_status' => 'approved',
            'modality' => 'virtual',
            'level' => 'advanced',
            'capacity' => 100,
            'attendee_price' => 0,
        ]);

        // Schedules for Component 1_3 (Day 1 afternoon)
        ComponentSchedule::create([
            'component_id' => $component1_3->id,
            'date' => Carbon::now()->addDays(10),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
        ]);

        $component1_4 = EventComponent::create([
            'event_id' => $event1->id,
            'speaker_id' => $profileSpeaker1->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'DevOps con Docker y Kubernetes',
            'description' => 'Workshop hands-on sobre contenedores y orquestación con Docker y Kubernetes.',
            'type' => 'workshop',
            'proposal_status' => 'approved',
            'modality' => 'in_person',
            'location' => 'Sala C - Primer Piso',
            'level' => 'intermediate',
            'capacity' => 20,
            'attendee_price' => 75.00,
            'participant_requirements' => 'Docker Desktop instalado',
        ]);

        // Schedules for Component 1_4 (Day 2 full day)
        ComponentSchedule::create([
            'component_id' => $component1_4->id,
            'date' => Carbon::now()->addDays(11),
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
        ]);
        ComponentSchedule::create([
            'component_id' => $component1_4->id,
            'date' => Carbon::now()->addDays(11),
            'start_time' => '14:00:00',
            'end_time' => '17:00:00',
        ]);

        // Create Event 2: Academic Symposium
        $event2 = Event::create([
            'professional_profile_id' => $profile->id,
            'name' => 'Simposio de Innovación Educativa',
            'description' => 'Encuentro académico para discutir nuevas metodologías de enseñanza y tecnologías educativas.',
            'start_date' => Carbon::now()->addDays(20),
            'end_date' => Carbon::now()->addDays(21),
            'start_time' => '08:30:00',
            'modality' => 'in_person',
            'location' => 'Auditorio Principal Universidad',
            'visibility' => 'public',
            'status' => 'active',
        ]);

        // Components for Event 2
        $component2_1 = EventComponent::create([
            'event_id' => $event2->id,
            'speaker_id' => $profileSpeaker2->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Gamificación en el Aula',
            'description' => 'Técnicas y herramientas para implementar gamificación en procesos educativos.',
            'type' => 'workshop',
            'proposal_status' => 'approved',
            'modality' => 'in_person',
            'location' => 'Aula Magna',
            'level' => 'beginner',
            'capacity' => 50,
            'attendee_price' => 0,
        ]);

        ComponentSchedule::create([
            'component_id' => $component2_1->id,
            'date' => Carbon::now()->addDays(20),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        $component2_2 = EventComponent::create([
            'event_id' => $event2->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Inteligencia Artificial en Educación',
            'description' => 'Panel sobre el uso ético y efectivo de IA en contextos educativos.',
            'type' => 'talk',
            'proposal_status' => 'approved',
            'modality' => 'hybrid',
            'location' => 'Sala de Conferencias',
            'level' => 'intermediate',
            'capacity' => 80,
            'attendee_price' => 25.00,
        ]);

        ComponentSchedule::create([
            'component_id' => $component2_2->id,
            'date' => Carbon::now()->addDays(20),
            'start_time' => '11:30:00',
            'end_time' => '13:00:00',
        ]);

        $component2_3 = EventComponent::create([
            'event_id' => $event2->id,
            'speaker_id' => $profileSpeaker1->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Diseño Universal de Aprendizaje',
            'description' => 'Principios y prácticas del DUA para crear experiencias educativas inclusivas.',
            'type' => 'talk',
            'proposal_status' => 'approved',
            'modality' => 'in_person',
            'location' => 'Auditorio B',
            'level' => 'beginner',
            'capacity' => 60,
            'attendee_price' => 0,
        ]);

        ComponentSchedule::create([
            'component_id' => $component2_3->id,
            'date' => Carbon::now()->addDays(21),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
        ]);

        // Component with very limited capacity for testing
        $component2_4 = EventComponent::create([
            'event_id' => $event2->id,
            'speaker_id' => $profileSpeaker2->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Taller Exclusivo: Metodologías Activas',
            'description' => 'Taller práctico con cupo muy limitado sobre metodologías activas de aprendizaje.',
            'type' => 'workshop',
            'proposal_status' => 'approved',
            'modality' => 'in_person',
            'location' => 'Laboratorio 101',
            'level' => 'intermediate',
            'capacity' => 5, // Very limited for testing capacity validation
            'attendee_price' => 100.00,
            'participant_requirements' => 'Ser docente activo',
        ]);

        ComponentSchedule::create([
            'component_id' => $component2_4->id,
            'date' => Carbon::now()->addDays(21),
            'start_time' => '14:00:00',
            'end_time' => '17:00:00',
        ]);

        // === ADDITIONAL TEST SCENARIOS ===

        // Open offer waiting for applications
        $openOffer = EventComponent::create([
            'event_id' => $event1->id,
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Seguridad en Aplicaciones Web',
            'description' => 'Buscamos ponente experto en OWASP Top 10 y mejores prácticas de seguridad web.',
            'type' => 'talk',
            'proposal_status' => 'offer_open',
            'modality' => 'in_person',
            'location' => 'Sala D - Segundo Piso',
            'level' => 'advanced',
            'capacity' => 40,
            'organizer_cost' => 500.00,
            'instructor_requirements' => 'Certificación en seguridad informática, mínimo 5 años de experiencia',
        ]);

        ComponentSchedule::create([
            'component_id' => $openOffer->id,
            'date' => Carbon::now()->addDays(12),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
        ]);

        // Pending proposal from participant
        $participante = User::where('email', 'participante@tequio.com')->first();
        $profileParticipante = ProfessionalProfile::firstOrCreate(
            ['user_id' => $participante->id],
            [
                'about_me' => 'Desarrollador fullstack apasionado por las nuevas tecnologías',
                'current_workplace' => 'Freelancer',
                'skills' => 'JavaScript, Python, Node.js, PostgreSQL',
            ]
        );

        $proposal = EventComponent::create([
            'event_id' => $event1->id,
            'speaker_id' => $profileParticipante->id,
            'proposed_by_user_id' => $participante->id,
            'name' => 'APIs RESTful con Node.js',
            'description' => 'Diseño e implementación de APIs RESTful escalables utilizando Node.js y Express.',
            'type' => 'workshop',
            'proposal_status' => 'proposed',
            'modality' => 'in_person',
            'location' => 'Sala E - Tercer Piso',
            'level' => 'intermediate',
            'capacity' => 25,
            'attendee_price' => 0,
            'participant_requirements' => 'Conocimientos básicos de JavaScript',
        ]);

        ComponentSchedule::create([
            'component_id' => $proposal->id,
            'date' => Carbon::now()->addDays(11),
            'start_time' => '15:00:00',
            'end_time' => '18:00:00',
        ]);

        // Component for testing SPEAKER conflict (same speaker at same time)
        $speakerConflictTest = EventComponent::create([
            'event_id' => $event1->id,
            'speaker_id' => $profileSpeaker1->id, // Same speaker as component1_1
            'proposed_by_user_id' => $organizador->id,
            'name' => 'Testing con PHPUnit',
            'description' => 'Aprende a escribir tests unitarios y de integración con PHPUnit.',
            'type' => 'workshop',
            'proposal_status' => 'approved',
            'modality' => 'in_person',
            'location' => 'Sala F - Tercer Piso', // Different location
            'level' => 'beginner',
            'capacity' => 20,
            'attendee_price' => 0,
        ]);

        // Schedule that conflicts with speaker (Dr. Carlos García already has Laravel 11 at 09:00-12:00)
        ComponentSchedule::create([
            'component_id' => $speakerConflictTest->id,
            'date' => Carbon::now()->addDays(10),
            'start_time' => '10:00:00', // Overlaps with Laravel 11 (09:00-12:00)
            'end_time' => '12:00:00',
        ]);

        $this->command->info('');
        $this->command->info('=========================================');
        $this->command->info('  TEST DATA CREATED SUCCESSFULLY!');
        $this->command->info('=========================================');
        $this->command->info('');
        $this->command->info('EVENTS:');
        $this->command->info('  • ' . $event1->name . ' (ID: ' . $event1->id . ')');
        $this->command->info('  • ' . $event2->name . ' (ID: ' . $event2->id . ')');
        $this->command->info('');
        $this->command->info('TEST SCENARIOS:');
        $this->command->info('  1. Location conflict: "Laravel 11" and "Vue.js" - same time, different rooms (OK)');
        $this->command->info('  2. Speaker conflict: "Laravel 11" and "Testing con PHPUnit" - same speaker overlapping times');
        $this->command->info('  3. Limited capacity: "Taller Exclusivo" has only 5 spots');
        $this->command->info('  4. Multiple schedules: "DevOps" has morning and afternoon sessions');
        $this->command->info('  5. Open offer: "Seguridad en Aplicaciones Web" waiting for applications');
        $this->command->info('  6. Pending proposal: "APIs RESTful con Node.js" needs evaluation');
        $this->command->info('');
        $this->command->info('USER CREDENTIALS:');
        $this->command->info('  Admin:        admin@tequio.com / 1234');
        $this->command->info('  Organizador:  organizador@tequio.com / 1234');
        $this->command->info('  Participante: participante@tequio.com / 1234');
        $this->command->info('  Ponente 1:    ponente1@tequio.com / 1234');
        $this->command->info('  Ponente 2:    ponente2@tequio.com / 1234');
        $this->command->info('');
    }
}
