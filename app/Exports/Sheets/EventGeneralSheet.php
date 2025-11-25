<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EventGeneralSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $event;
    protected $stats;

    public function __construct($event, $stats)
    {
        $this->event = $event;
        $this->stats = $stats;
    }

    public function array(): array
    {
        return [
            ['Nombre del Evento', $this->event->name],
            ['Fecha de Inicio', $this->event->start_date->format('d/m/Y')],
            ['Fecha de Fin', $this->event->end_date->format('d/m/Y')],
            ['Estado', ucfirst($this->event->status)],
            ['Visibilidad', ucfirst($this->event->visibility)],
            [''],
            ['Componentes Totales', $this->stats['totalComponents']],
            ['Charlas', $this->stats['componentsByType']['talk']],
            ['Talleres', $this->stats['componentsByType']['workshop']],
            ['Actividades', $this->stats['componentsByType']['activity']],
            [''],
            ['Inscripciones Totales', $this->stats['totalRegistrations']],
        ];
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE EVENTO - TEQUIO'],
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
            'A' => 30,
            'B' => 40,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Branding Header
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
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
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4499BB']],
        ]);

        // Data Borders
        $sheet->getStyle('A4:B16')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        return [];
    }
}
