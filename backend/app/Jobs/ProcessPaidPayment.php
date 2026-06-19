<?php

namespace App\Jobs;

use App\Models\Affiliate;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscribe;
use App\Models\User;
use App\PaymentStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaidPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public int $paymentId)
    {
    }

    public function handle(): void
    {
        $payment = Payment::find($this->paymentId);

        if (!$payment || $payment->payment_status !== PaymentStatusEnum::PAID->value) {
            return;
        }

        if (!$payment->payment_paid_at) {
            $payment->update(['payment_paid_at' => now(), 'payment_updated_at' => now()]);
        }

        $alreadyProcessed = Subscribe::where('subscribe_id_user', $payment->payment_id_user)
            ->where('subscribe_created_at', '>=', $payment->payment_paid_at ?? $payment->payment_updated_at)
            ->exists();

        if ($alreadyProcessed) {
            return;
        }

        DB::transaction(function () use ($payment) {
            $plan = Plan::findOrFail($payment->payment_id_plan);
            $user = User::findOrFail($payment->payment_id_user);

            if ($user->subscribe) {
                Subscribe::where('subscribe_id', $user->subscribe)
                    ->where('subscribe_canceled_at', null)
                    ->update(['subscribe_canceled_at' => now(), 'subscribe_updated_at' => now()]);
            }

            $periodDays = match ($plan->plan_periode) {
                '1y' => 365,
                '6m' => 180,
                '3m' => 90,
                '1m' => 30,
                default => (int) config('langkahkecil.trial_days', 10),
            };

            $subscription = Subscribe::create([
                'subscribe_id_user' => $user->id,
                'subscribe_harga' => $plan->plan_harga,
                'subscribe_discount' => $payment->payment_diskon ?? 0,
                'subscribe_total' => $payment->payment_total ?? $plan->plan_harga,
                'subscribe_id_plan' => $plan->plan_id,
                'subsribe_value' => $plan->plan_value ?? 1,
                'subscribe_start_at' => now(),
                'subscribe_end_at' => now()->addDays($periodDays),
                'subscribe_created_at' => now(),
            ]);

            $user->update([
                'subscribe' => $subscription->subscribe_id,
                'role' => $plan->plan_harga > 0 ? 'premium' : 'user',
            ]);

            $this->processAffiliateUpgrade($user, $plan, $payment);
        });

        Log::info("PaymentProcessed: payment_id={$payment->payment_id} user_id={$payment->payment_id_user}");
    }

    private function processAffiliateUpgrade(User $user, Plan $plan, Payment $payment): void
    {
        if ($plan->plan_harga <= 0) {
            return;
        }

        $alreadyCommissioned = Affiliate::where('affiliate_id_payment', $payment->payment_id)->exists();
        if ($alreadyCommissioned) {
            return;
        }

        $referrer = null;
        $usedCustomCode = false;
        $discountValue = 0;

        if ($payment->payment_diskon_code) {
            $discount = \App\Models\Discount::where('discount_code', $payment->payment_diskon_code)->first();
            if ($discount && $discount->discount_created_by) {
                $discountCreator = User::find($discount->discount_created_by);
                if ($discountCreator && $discountCreator->affiliate_code) {
                    $usedCustomCode = true;
                    $discountValue = $discount->discount_type === 'percentage'
                        ? (int) $discount->discount_value
                        : (int) round($discount->discount_value / $plan->plan_harga * 100);

                    $oldReff = $user->affiliate_reff;
                    if ($oldReff !== $discountCreator->affiliate_code) {
                        $user->update(['affiliate_reff' => $discountCreator->affiliate_code]);
                        Log::info("AffiliateSwitch: user_id={$user->id} from={$oldReff} to={$discountCreator->affiliate_code} reason=discount_code={$payment->payment_diskon_code}");
                    }

                    $referrer = $discountCreator;
                }
            }
        }

        if (!$referrer && $user->affiliate_reff) {
            $referrer = User::where('affiliate_code', $user->affiliate_reff)->first();
        }

        if (!$referrer) {
            return;
        }

        $commissionRate = (int) config('langkahkecil.affiliate.upgrade_commission_rate', 15);

        if ($usedCustomCode) {
            $commissionRate = max(0, $commissionRate - $discountValue);
        }

        $commission = (int) round($plan->plan_harga * $commissionRate / 100);

        $note = $usedCustomCode
            ? "Komisi {$commissionRate}% (diskon {$discountValue}% untuk customer) dari " . $user->name . " upgrade " . $plan->plan_nama
            : "Komisi {$commissionRate}% dari " . $user->name . " upgrade " . $plan->plan_nama;

        Affiliate::create([
            'affiliate_id_user' => $referrer->id,
            'affiliate_id_from_user' => $user->id,
            'affiliate_id_payment' => $payment->payment_id,
            'affiliate_tipe' => 'upgrade',
            'affiliate_jumlah' => $commission,
            'affiliate_payment_jumlah' => $plan->plan_harga,
            'affiliate_commission_rate' => $commissionRate,
            'affiliate_catatan' => $note,
            'affiliate_status' => 'pending',
            'affiliate_created_at' => now(),
            'affiliate_updated_at' => now(),
        ]);

        Log::info("AffiliateUpgrade: referrer_id={$referrer->id} from_user_id={$user->id} amount={$commission} custom_code=" . ($usedCustomCode ? $payment->payment_diskon_code : 'none'));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessPaidPayment FAILED: payment_id={$this->paymentId} error={$exception->getMessage()}");
    }
}
