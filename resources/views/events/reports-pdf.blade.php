<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Evento - {{ $event->name }}</title>
    <style>
        @page {
            margin: 100px 25px;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 12px;
        }
        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 60px;
            border-bottom: 2px solid #0C2340;
            padding-bottom: 10px;
        }
        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 40px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .brand-title {
            color: #0C2340;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .brand-subtitle {
            color: #00B5E2;
            font-size: 14px;
            margin: 0;
        }
        .report-info {
            text-align: right;
            position: absolute;
            right: 0;
            top: 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #0C2340;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .stats-grid {
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 10px;
        }
        .stat-card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0C2340;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f1f3f5;
            color: #0C2340;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #0C2340;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .text-right { text-align: right; }
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            background-color: #6c757d;
        }
        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>
<body>
    <header>
        <div class="brand-title">TEQUIO</div>
        <div class="brand-subtitle">Reporte de Evento</div>
        <div class="report-info">
            <strong>{{ $event->name }}</strong><br>
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </header>

    <footer>
        Sistema TEQUIO - Generado automáticamente | <span class="page-number"></span>
    </footer>

    <main>
        <div class="section">
            <div class="section-title">RESUMEN GENERAL</div>
            <table class="stats-grid">
                <tr>
                    <td width="25%" style="padding: 0;">
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($stats['totalRegistrations']) }}</div>
                            <div class="stat-label">Inscripciones</div>
                        </div>
                    </td>
                    <td width="25%" style="padding: 0;">
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($stats['totalComponents']) }}</div>
                            <div class="stat-label">Componentes</div>
                        </div>
                    </td>
                    <td width="25%" style="padding: 0;">
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($stats['componentsByType']['talk']) }}</div>
                            <div class="stat-label">Charlas</div>
                        </div>
                    </td>
                    <td width="25%" style="padding: 0;">
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($stats['componentsByType']['workshop']) }}</div>
                            <div class="stat-label">Talleres</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">DETALLE DE COMPONENTES</div>
            <table>
                <thead>
                    <tr>
                        <th>Componente</th>
                        <th>Tipo</th>
                        <th>Modalidad</th>
                        <th class="text-right">Inscripciones</th>
                        <th class="text-right">Capacidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->components as $component)
                    <tr>
                        <td>{{ $component->name }}</td>
                        <td>{{ ucfirst($component->type) }}</td>
                        <td>{{ ucfirst($component->modality) }}</td>
                        <td class="text-right">{{ $component->registrations->count() }}</td>
                        <td class="text-right">{{ $component->capacity > 0 ? $component->capacity : 'Ilimitada' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">INSCRIPCIONES RECIENTES</div>
            <table>
                <thead>
                    <tr>
                        <th>Participante</th>
                        <th>Email</th>
                        <th>Componente</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allRegistrations->take(50) as $registration)
                    <tr>
                        <td>{{ $registration->user->name }}</td>
                        <td>{{ $registration->user->email }}</td>
                        <td>{{ $registration->component->name }}</td>
                        <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($allRegistrations->count() > 50)
                <p style="text-align: center; font-style: italic; color: #666;">Mostrando las últimas 50 inscripciones de {{ $allRegistrations->count() }} totales.</p>
            @endif
        </div>
    </main>
</body>
</html>
