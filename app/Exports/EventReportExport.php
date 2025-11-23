<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\EventGeneralSheet;
use App\Exports\Sheets\EventRegistrationsSheet;

class EventReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $event;
    protected $stats;
    protected $registrations;

    public function __construct($event, $stats, $registrations)
    {
        $this->event = $event;
        $this->stats = $stats;
        $this->registrations = $registrations;
    }

    public function sheets(): array
    {
        return [
            new EventGeneralSheet($this->event, $this->stats),
            new EventRegistrationsSheet($this->event, $this->registrations),
        ];
    }
}
