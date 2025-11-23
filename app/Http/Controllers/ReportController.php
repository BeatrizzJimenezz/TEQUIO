<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComponent;
use App\Models\OfferApplication;
use App\Models\Registration;
use App\Models\RoleRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Verificar que sea administrador
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Solo los administradores pueden acceder a los reportes.');
        }

        // Estadísticas generales
        $stats = [
            'users' => $this->getUserStats(),
            'events' => $this->getEventStats(),
            'registrations' => $this->getRegistrationStats(),
            'components' => $this->getComponentStats(),
            'applications' => $this->getApplicationStats(),
        ];

        // Datos para gráficos
        $charts = [
            'usersByMonth' => $this->getUsersByMonth(),
            'eventsByMonth' => $this->getEventsByMonth(),
            'registrationsByMonth' => $this->getRegistrationsByMonth(),
            'eventsByModality' => $this->getEventsByModality(),
            'componentsByType' => $this->getComponentsByType(),
        ];

        // Eventos recientes
        $recentEvents = Event::with('professionalProfile.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Usuarios recientes
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top eventos por registros (a través de componentes)
        $topEvents = Event::select('events.*')
            ->selectSub(function ($query) {
                $query->from('registrations')
                    ->join('event_components', 'registrations.component_id', '=', 'event_components.id')
                    ->whereColumn('event_components.event_id', 'events.id')
                    ->selectRaw('count(*)');
            }, 'registrations_count')
            ->orderBy('registrations_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact('stats', 'charts', 'recentEvents', 'recentUsers', 'topEvents'));
    }

    public function export(Request $request)
    {
        // Verificar que sea administrador
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Solo los administradores pueden exportar reportes.');
        }

        $format = $request->query('format', 'pdf');
        $section = $request->query('section', 'all');

        // Recopilar estadísticas usando los métodos existentes
        $stats = [
            'users' => $this->getUserStats(),
            'events' => $this->getEventStats(),
            'registrations' => $this->getRegistrationStats(),
            'components' => $this->getComponentStats(),
            'applications' => $this->getApplicationStats(),
        ];

        // Top eventos por registros (reutilizando lógica del index)
        $topEvents = Event::select('events.*')
            ->selectSub(function ($query) {
                $query->from('registrations')
                    ->join('event_components', 'registrations.component_id', '=', 'event_components.id')
                    ->whereColumn('event_components.event_id', 'events.id')
                    ->selectRaw('count(*)');
            }, 'registrations_count')
            ->orderBy('registrations_count', 'desc')
            ->limit(10)
            ->get();

        // Usuarios recientes
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\ReportExport($stats, $topEvents, $recentUsers, $section),
                'reporte-' . $section . '-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        // PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.reports.pdf', compact('stats', 'topEvents', 'recentUsers', 'section'));
        return $pdf->download('reporte-' . $section . '-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getUserStats()
    {
        $total = User::count();
        $thisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $growth = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;

        // Por roles
        $byRole = [
            'administradores' => User::role('Administrador')->count(),
            'organizadores' => User::role('Organizador')->count(),
            'participantes' => User::role('Participante')->count(),
        ];

        return [
            'total' => $total,
            'thisMonth' => $thisMonth,
            'lastMonth' => $lastMonth,
            'growth' => $growth,
            'byRole' => $byRole,
        ];
    }

    private function getEventStats()
    {
        $total = Event::count();
        $thisMonth = Event::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Por estado
        $byStatus = [
            'draft' => Event::where('status', 'draft')->count(),
            'published' => Event::where('status', 'published')->count(),
            'active' => Event::where('status', 'active')->count(),
            'finished' => Event::where('status', 'finished')->count(),
        ];

        // Por visibilidad
        $byVisibility = [
            'public' => Event::where('visibility', 'public')->count(),
            'private' => Event::where('visibility', 'private')->count(),
        ];

        // Próximos eventos
        $upcoming = Event::where('start_date', '>', now())->count();

        return [
            'total' => $total,
            'thisMonth' => $thisMonth,
            'byStatus' => $byStatus,
            'byVisibility' => $byVisibility,
            'upcoming' => $upcoming,
        ];
    }

    private function getRegistrationStats()
    {
        $total = Registration::count();
        $thisMonth = Registration::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonth = Registration::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $growth = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;

        return [
            'total' => $total,
            'thisMonth' => $thisMonth,
            'lastMonth' => $lastMonth,
            'growth' => $growth,
        ];
    }

    private function getComponentStats()
    {
        $total = EventComponent::count();

        // Por tipo
        $byType = [
            'talk' => EventComponent::where('type', 'talk')->count(),
            'workshop' => EventComponent::where('type', 'workshop')->count(),
            'activity' => EventComponent::where('type', 'activity')->count(),
        ];

        // Por estado de propuesta
        $byProposalStatus = [
            'approved' => EventComponent::where('proposal_status', 'approved')->count(),
            'proposed' => EventComponent::where('proposal_status', 'proposed')->count(),
            'rejected' => EventComponent::where('proposal_status', 'rejected')->count(),
            'offer_open' => EventComponent::where('proposal_status', 'offer_open')->count(),
        ];

        return [
            'total' => $total,
            'byType' => $byType,
            'byProposalStatus' => $byProposalStatus,
        ];
    }

    private function getApplicationStats()
    {
        $total = OfferApplication::count();

        $byStatus = [
            'pending' => OfferApplication::where('status', 'pending')->count(),
            'accepted' => OfferApplication::where('status', 'accepted')->count(),
            'rejected' => OfferApplication::where('status', 'rejected')->count(),
        ];

        return [
            'total' => $total,
            'byStatus' => $byStatus,
        ];
    }

    private function getUsersByMonth()
    {
        $data = User::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return $this->fillMonthlyData($data);
    }

    private function getEventsByMonth()
    {
        $data = Event::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return $this->fillMonthlyData($data);
    }

    private function getRegistrationsByMonth()
    {
        $data = Registration::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return $this->fillMonthlyData($data);
    }

    private function fillMonthlyData($data)
    {
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $result = [];

        for ($i = 1; $i <= 12; $i++) {
            $result[] = [
                'month' => $months[$i - 1],
                'count' => $data[$i] ?? 0,
            ];
        }

        return $result;
    }

    private function getEventsByModality()
    {
        return [
            ['label' => 'Virtual', 'count' => Event::where('modality', 'virtual')->count()],
            ['label' => 'Presencial', 'count' => Event::where('modality', 'in_person')->count()],
            ['label' => 'Híbrido', 'count' => Event::where('modality', 'hybrid')->count()],
        ];
    }

    private function getComponentsByType()
    {
        return [
            ['label' => 'Charlas', 'count' => EventComponent::where('type', 'talk')->count()],
            ['label' => 'Talleres', 'count' => EventComponent::where('type', 'workshop')->count()],
            ['label' => 'Actividades', 'count' => EventComponent::where('type', 'activity')->count()],
        ];
    }
}
