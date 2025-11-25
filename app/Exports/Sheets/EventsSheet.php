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

class EventsSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $stats;
    protected $topEvents;

    public function __construct($stats, $topEvents)
    {
        $this->stats = $stats;
        $this->topEvents = $topEvents;
    }

    public function array(): array
    {
        $data = [
            ['ESTADO DE EVENTOS', ''],
            ['Borrador', $this->stats['events']['byStatus']['draft']],
            ['Publicado', $this->stats['events']['byStatus']['published']],
            ['Activo', $this->stats['events']['byStatus']['active']],
            ['Finalizado', $this->stats['events']['byStatus']['finished']],
            ['', ''],
            ['VISIBILIDAD', ''],
            ['Públicos', $this->stats['events']['byVisibility']['public']],
            ['Privados', $this->stats['events']['byVisibility']['private']],
            ['', ''],
            ['TOP EVENTOS POR INSCRIPCIONES', '', ''],
            ['Nombre', 'Inscripciones', 'Fecha Inicio'],
        ];

        foreach ($this->topEvents as $event) {
            $data[] = [
                $event->name,
                $event->registrations_count,
                $event->start_date ? $event->start_date->format('d/m/Y') : 'N/A',
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['SISTEMA TEQUIO - REPORTE DE EVENTOS'],
            ['Generado: ' . now()->format('d/m/Y H:i')],
            [''],
        ];
    }

    public function title(): string
    {
        return 'Eventos';
    }

    public function styles(Worksheet $sheet)
    {
        // Branding Header
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        
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
        $sectionHeaders = ['A4:B4', 'A10:B10', 'A14:C14'];
        foreach ($sectionHeaders as $range) {
            $sheet->getStyle($range)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B5E2']],
            ]);
        }

        // Borders for tables
        $sheet->getStyle('A4:B8')->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
        $sheet->getStyle('A10:B12')->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
        
        $lastRow = 14 + count($this->topEvents) + 1;
        $sheet->getStyle('A14:C' . $lastRow)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

        return [];
    }
}
