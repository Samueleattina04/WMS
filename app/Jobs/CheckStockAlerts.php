<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Product;
use App\Models\StockAlert;
use App\Models\StockLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CheckStockAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Company $company) {}

    public function handle(): void
    {
        $this->checkLowStock();
        $this->checkExpiryDates();
    }

    private function checkLowStock(): void
    {
        $products = Product::where('company_id', $this->company->id)
            ->where('is_active', true)
            ->where('min_stock_alert', '>', 0)
            ->withoutGlobalScopes()
            ->get();

        foreach ($products as $product) {
            $totalStock = StockLocation::where('company_id', $this->company->id)
                ->where('product_id', $product->id)
                ->sum('quantity');

            $alertType = $totalStock === 0 ? 'out_of_stock' : ($totalStock <= $product->min_stock_alert ? 'low_stock' : null);

            if ($alertType) {
                $existing = StockAlert::where('company_id', $this->company->id)
                    ->where('product_id', $product->id)
                    ->whereIn('alert_type', ['low_stock', 'out_of_stock'])
                    ->where('is_resolved', false)
                    ->first();

                if (! $existing) {
                    StockAlert::create([
                        'company_id' => $this->company->id,
                        'product_id' => $product->id,
                        'alert_type' => $alertType,
                        'current_quantity' => $totalStock,
                        'threshold_quantity' => $product->min_stock_alert,
                    ]);
                } else {
                    $existing->update([
                        'alert_type' => $alertType,
                        'current_quantity' => $totalStock,
                    ]);
                }
            } else {
                // Resolve existing low stock alerts if stock is now ok
                StockAlert::where('company_id', $this->company->id)
                    ->where('product_id', $product->id)
                    ->whereIn('alert_type', ['low_stock', 'out_of_stock'])
                    ->where('is_resolved', false)
                    ->update(['is_resolved' => true, 'resolved_at' => now()]);
            }
        }
    }

    private function checkExpiryDates(): void
    {
        $daysThreshold = $this->company->alertDaysBeforeExpiry();
        $expiryDate = now()->addDays($daysThreshold)->format('Y-m-d');

        $expiringSoon = StockLocation::where('company_id', $this->company->id)
            ->where('quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', $expiryDate)
            ->whereDate('expiry_date', '>=', now()->format('Y-m-d'))
            ->get();

        foreach ($expiringSoon as $sl) {
            $existing = StockAlert::where('company_id', $this->company->id)
                ->where('product_id', $sl->product_id)
                ->where('alert_type', 'expiry')
                ->where('expiry_date', $sl->expiry_date)
                ->where('is_resolved', false)
                ->first();

            if (! $existing) {
                StockAlert::create([
                    'company_id' => $this->company->id,
                    'product_id' => $sl->product_id,
                    'alert_type' => 'expiry',
                    'current_quantity' => $sl->quantity,
                    'expiry_date' => $sl->expiry_date,
                ]);
            }
        }

        $this->sendAlertEmails();
    }

    private function sendAlertEmails(): void
    {
        $emails = $this->company->alertEmails();
        if (empty($emails)) {
            return;
        }

        $unnotifiedAlerts = StockAlert::where('company_id', $this->company->id)
            ->where('is_resolved', false)
            ->whereNull('notified_at')
            ->with('product')
            ->get();

        if ($unnotifiedAlerts->isEmpty()) {
            return;
        }

        foreach ($emails as $email) {
            Mail::send('emails.stock-alert', [
                'company' => $this->company,
                'alerts' => $unnotifiedAlerts,
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject("[{$this->company->name}] Alert Magazzino - " . now()->format('d/m/Y'));
            });
        }

        StockAlert::where('company_id', $this->company->id)
            ->where('is_resolved', false)
            ->whereNull('notified_at')
            ->update(['notified_at' => now()]);
    }
}
