<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\GeneralSheet;
use App\Exports\Sheets\EventsSheet;
use App\Exports\Sheets\UsersSheet;

class ReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $stats;
    protected $topEvents;
    protected $recentUsers;
    protected $section;

    public function __construct($stats, $topEvents, $recentUsers, $section = 'all')
    {
        $this->stats = $stats;
        $this->topEvents = $topEvents;
        $this->recentUsers = $recentUsers;
        $this->section = $section;
    }

    public function sheets(): array
    {
        $sheets = [];

        if ($this->section === 'all' || $this->section === 'general') {
            $sheets[] = new GeneralSheet($this->stats);
        }

        if ($this->section === 'all' || $this->section === 'events') {
            $sheets[] = new EventsSheet($this->stats, $this->topEvents);
        }

        if ($this->section === 'all' || $this->section === 'users') {
            $sheets[] = new UsersSheet($this->stats, $this->recentUsers);
        }

        return $sheets;
    }
}
