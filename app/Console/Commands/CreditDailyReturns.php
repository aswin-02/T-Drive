<?php
/**
 * Artisan Command: credit:daily-returns
 *
 * This command credits the daily_return amount of each active plan to each user who has an active purchase for that plan,
 * but only if today is an active credit day (as per the credit_days table).
 *
 * Usage:
 *   php artisan credit:daily-returns
 *
 * - Checks if today is an active credit day.
 * - Fetches all active purchases for active users and active plans.
 * - Credits the daily_return to each user for each eligible purchase (idempotent per day).
 * - Logs all actions and errors to the console.
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreditDailyReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credit:daily-returns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit daily returns to users for active plans and purchases if today is an active credit day.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Daily credit process started.');

        // Step 1: Check if today is an active credit day
        $today = now()->toDateString();
        $creditDay = \App\Models\CreditDay::where('date', $today)->where('is_active', true)->first();
        if (!$creditDay) {
            $this->warn('Today is not an active credit day. Exiting.');
            return 0;
        }
        $this->info('Today is an active credit day. Proceeding to credit process.');
        // Step 2: Fetch active purchases for active users and active plans
        $purchases = \App\Models\Purchase::with(['user', 'plan'])
            ->whereHas('user', function ($q) { $q->where('is_active', true); })
            ->whereHas('plan', function ($q) { $q->where('is_active', true); })
            ->get();
        $this->info('Found ' . $purchases->count() . ' active purchases for active users and plans.');
        // Step 3: Credit daily returns to users
        $creditedCount = 0;
        $errorCount = 0;
        foreach ($purchases as $purchase) {
            try {
                $user = $purchase->user;
                $plan = $purchase->plan;
                if (!$user || !$plan) {
                    $this->warn("Purchase #{$purchase->id} missing user or plan. Skipped.");
                    continue;
                }

                // Idempotency: check if already credited for this purchase and date
                $alreadyCredited = \App\Models\Transaction::where('user_id', $user->id)
                    ->where('purchase_id', $purchase->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->where('source', 'daily_return')
                    ->exists();
                if ($alreadyCredited) {
                    $this->line("Already credited for purchase #{$purchase->id} today. Skipped.");
                    continue;
                }

                $amount = $plan->daily_return;
                if ($amount <= 0) {
                    $this->line("Plan #{$plan->id} daily_return is zero or less. Skipped.");
                    continue;
                }

                $balanceAfter = $user->wallet_balance + $amount;

                \App\Models\Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'source' => 'daily_return',
                    'amount' => $amount,
                    'balance_after' => $balanceAfter,
                    'description' => 'Daily return for plan #' . $plan->id . ' purchase #' . $purchase->id,
                    'purchase_id' => $purchase->id,
                ]);
                $this->info("Credited user #{$user->id} for purchase #{$purchase->id} amount $amount.");
                $creditedCount++;
            } catch (\Throwable $e) {
                $this->error("Error crediting purchase #{$purchase->id}: " . $e->getMessage());
                $errorCount++;
            }
        }
        $this->info("Credited daily returns for $creditedCount purchases. Errors: $errorCount");
    }
}
