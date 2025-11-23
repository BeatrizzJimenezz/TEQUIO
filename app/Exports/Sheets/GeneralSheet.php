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

use Maatwebsite\Excel\Concerns\WithColumnWidths;

class GeneralSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $stats;

    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    public function array(): array
    {
        return [
            ['Usuarios Totales', $this->stats['users']['total']],
            ['Nuevos Usuarios (Mes)', $this->stats['users']['thisMonth']],
            ['Crecimiento Usuarios', $this->stats['users']['growth'] . '%'],
            [''],
            ['Eventos Totales', $this->stats['events']['total']],
            ['Eventos Próximos', $this->stats['events']['upcoming']],
            [''],
            ['Inscripciones Totales', $this->stats['registrations']['total']],
            ['Inscripciones (Mes)', $this->stats['registrations']['thisMonth']],
            [''],
            ['Componentes Totales', $this->stats['components']['total']],
        ];
    }

    public function headings(): array
    {
        return [
            ['SISTEMA TEQUIO - REPORTE GENERAL'],
            ['Generado: ' . now()->format('d/m/Y H:i')],
            [''],
            ['Métrica', 'Valor'],
        ];
    }

    public function title(): string
    {
        return 'General';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Branding Header
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2340']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Table Header
        $sheet->getStyle('A4:B4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B5E2']],
        ]);

        // Data Borders
        $sheet->getStyle('A4:B15')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        return [];
    }
}
