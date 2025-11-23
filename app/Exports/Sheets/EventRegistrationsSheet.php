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

class EventRegistrationsSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $event;
    protected $registrations;

    public function __construct($event, $registrations)
    {
        $this->event = $event;
        $this->registrations = $registrations;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->registrations as $registration) {
            $data[] = [
                $registration->user->name,
                $registration->user->email,
                $registration->component->name,
                ucfirst($registration->component->type),
                $registration->created_at->format('d/m/Y H:i'),
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'Participante',
            'Email',
            'Componente',
            'Tipo',
            'Fecha Inscripción',
        ];
    }

    public function title(): string
    {
        return 'Inscripciones';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8CC63F']],
        ]);

        return [];
    }
}
