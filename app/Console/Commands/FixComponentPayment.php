<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EventComponent;
use Illuminate\Support\Facades\DB;

class FixComponentPayment extends Command
{
    protected $signature = 'fix:component-payment';
    protected $description = 'Fix payment_required field for all components based on price';

    public function handle()
    {
        $this->info('Updating components payment_required field...');

        $updated = EventComponent::query()->update([
            'payment_required' => DB::raw('CASE WHEN price > 0 THEN 1 ELSE 0 END')
        ]);

        $this->info("Updated {$updated} components.");
        $this->info('Done!');

        return 0;
    }
}
