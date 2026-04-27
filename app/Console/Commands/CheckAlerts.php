<?php

namespace App\Console\Commands;

use App\Jobs\CheckStockAlerts;
use App\Models\Company;
use Illuminate\Console\Command;

class CheckAlerts extends Command
{
    protected $signature = 'wms:check-alerts {--company= : Slug of specific company}';
    protected $description = 'Check stock alerts for all active companies and send notifications';

    public function handle(): void
    {
        $query = Company::where('is_active', true);

        if ($this->option('company')) {
            $query->where('slug', $this->option('company'));
        }

        $companies = $query->get();

        if ($companies->isEmpty()) {
            $this->info('No active companies found.');
            return;
        }

        $this->info("Checking alerts for {$companies->count()} company/companies...");

        foreach ($companies as $company) {
            $this->info("  → {$company->name}");
            CheckStockAlerts::dispatch($company);
        }

        $this->info('Alert check dispatched successfully.');
    }
}
