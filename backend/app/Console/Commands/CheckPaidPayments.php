<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPaidPayment;
use App\Models\Payment;
use App\PaymentStatusEnum;
use Illuminate\Console\Command;

class CheckPaidPayments extends Command
{
    protected $signature = 'payment:process';
    protected $description = 'Find paid payments that need subscription/affiliate processing and process them';

    public function handle(): int
    {
        $payments = Payment::where('payment_status', PaymentStatusEnum::PAID->value)
            ->where('payment_created_at', '>=', now()->addMinutes(-10))
            ->get();

        $processed = 0;

        foreach ($payments as $payment) {
            try {
                ProcessPaidPayment::dispatchSync($payment->payment_id);
                $processed++;
            } catch (\Throwable $e) {
                $this->error("Failed payment_id={$payment->payment_id}: {$e->getMessage()}");
            }
        }

        if ($processed > 0) {
            $this->info("Processed {$processed} paid payments.");
        } else {
            $this->line('No pending paid payments to process.');
        }

        return self::SUCCESS;
    }
}
