<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte General - Sistema TEQUIO</title>
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
        <div class="brand-subtitle">Sistema de Gestión de Eventos</div>
        <div class="report-info">
            <strong>Reporte General</strong><br>
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </header>

    <footer>
        Sistema TEQUIO - Generado automáticamente | <span class="page-number"></span>
    </footer>

    <main>
        @if($section == 'all' || $section == 'general')
        <div class="section">
            <div class="section-title">RESUMEN GENERAL</div>
            <table class="stats-grid">
                <tr>
                    <td width="25%" style="padding: 0;">
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($stats['users']['total']) }}</div>
                            <div class="stat-label">Usuarios</div>
                        </div>
                    </td>
                    <td width="25%" style="padding: 0;">
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($stats['events']['total']) }}</div>
                            <div class="stat-label">Eventos</div>
                        </div>
                    </td>
                    <td width="25%" style="padding: 0;">
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($stats['registrations']['total']) }}</div>
                            <div class="stat-label">Inscripciones</div>
                        </div>
                    </td>
                    <td width="25%" style="padding: 0;">
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($stats['components']['total']) }}</div>
                            <div class="stat-label">Componentes</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        @endif

        @if($section == 'all' || $section == 'events')
        <div class="section">
            <div class="section-title">ESTADO DE EVENTOS</div>
            <table>
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalEvents = $stats['events']['total'] > 0 ? $stats['events']['total'] : 1; @endphp
                    <tr>
                        <td>Borrador</td>
                        <td class="text-right">{{ $stats['events']['byStatus']['draft'] }}</td>
                        <td class="text-right">{{ round(($stats['events']['byStatus']['draft'] / $totalEvents) * 100, 1) }}%</td>
                    </tr>
                    <tr>
                        <td>Publicado</td>
                        <td class="text-right">{{ $stats['events']['byStatus']['published'] }}</td>
                        <td class="text-right">{{ round(($stats['events']['byStatus']['published'] / $totalEvents) * 100, 1) }}%</td>
                    </tr>
                    <tr>
                        <td>Activo</td>
                        <td class="text-right">{{ $stats['events']['byStatus']['active'] }}</td>
                        <td class="text-right">{{ round(($stats['events']['byStatus']['active'] / $totalEvents) * 100, 1) }}%</td>
                    </tr>
                    <tr>
                        <td>Finalizado</td>
                        <td class="text-right">{{ $stats['events']['byStatus']['finished'] }}</td>
                        <td class="text-right">{{ round(($stats['events']['byStatus']['finished'] / $totalEvents) * 100, 1) }}%</td>
                    </tr>
                </tbody>
            </table>

            <div class="section-title" style="margin-top: 30px;">TOP EVENTOS POR INSCRIPCIONES</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Evento</th>
                        <th class="text-right">Inscripciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topEvents as $index => $event)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $event->name }}</td>
                        <td class="text-right"><strong>{{ $event->registrations_count }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($section == 'all' || $section == 'users')
        <div class="section">
            <div class="section-title">USUARIOS RECIENTES</div>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Fecha Registro</th>
                        <th>Roles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge">{{ $role }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="section-title" style="margin-top: 30px;">DISTRIBUCIÓN POR ROL</div>
            <table>
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalUsers = $stats['users']['total'] > 0 ? $stats['users']['total'] : 1; @endphp
                    <tr>
                        <td>Administradores</td>
                        <td class="text-right">{{ $stats['users']['byRole']['administradores'] }}</td>
                        <td class="text-right">{{ round(($stats['users']['byRole']['administradores'] / $totalUsers) * 100, 1) }}%</td>
                    </tr>
                    <tr>
                        <td>Organizadores</td>
                        <td class="text-right">{{ $stats['users']['byRole']['organizadores'] }}</td>
                        <td class="text-right">{{ round(($stats['users']['byRole']['organizadores'] / $totalUsers) * 100, 1) }}%</td>
                    </tr>
                    <tr>
                        <td>Participantes</td>
                        <td class="text-right">{{ $stats['users']['byRole']['participantes'] }}</td>
                        <td class="text-right">{{ round(($stats['users']['byRole']['participantes'] / $totalUsers) * 100, 1) }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
    </main>
</body>
</html>
