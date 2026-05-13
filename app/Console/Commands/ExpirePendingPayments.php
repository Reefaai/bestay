<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Console\Command;

class ExpirePendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transition pending payments past their expiry window to expired.';

    /**
     * Execute the console command.
     */
    public function handle(PaymentService $paymentService): int
    {
        Payment::query()
            ->where('status', Payment::STATUS_PENDING)
            ->where('expires_at', '<', now())
            ->chunkById(100, function ($payments) use ($paymentService) {
                foreach ($payments as $payment) {
                    $paymentService->expireIfOverdue($payment, actorType: 'system');
                }
            });

        return self::SUCCESS;
    }
}
