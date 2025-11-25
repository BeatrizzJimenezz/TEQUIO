<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UsersSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $stats;
    protected $recentUsers;

    public function __construct($stats, $recentUsers)
    {
        $this->stats = $stats;
        $this->recentUsers = $recentUsers;
    }

    public function array(): array
    {
        $data = [
            ['DISTRIBUCIÓN POR ROL', ''],
            ['Administradores', $this->stats['users']['byRole']['administradores']],
            ['Organizadores', $this->stats['users']['byRole']['organizadores']],
            ['Participantes', $this->stats['users']['byRole']['participantes']],
            ['', ''],
            ['USUARIOS RECIENTES', '', '', ''],
            ['Nombre', 'Email', 'Fecha Registro', 'Roles'],
        ];

        foreach ($this->recentUsers as $user) {
            $data[] = [
                $user->name,
                $user->email,
                $user->created_at->format('d/m/Y H:i'),
                $user->getRoleNames()->implode(', '),
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['SISTEMA TEQUIO - REPORTE DE USUARIOS'],
            ['Generado: ' . now()->format('d/m/Y H:i')],
            [''],
        ];
    }

    public function title(): string
    {
        return 'Usuarios';
    }

    public function styles(Worksheet $sheet)
    {
        // Branding Header
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2340']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Section Headers
        $sheet->getStyle('A4:B4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B5E2']],
        ]);

        $sheet->getStyle('A9:D9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B5E2']],
        ]);

        // Borders
        $sheet->getStyle('A4:B7')->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
        
        $lastRow = 9 + count($this->recentUsers) + 1;
        $sheet->getStyle('A9:D' . $lastRow)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

        return [];
    }
}
