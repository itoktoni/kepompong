<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Jobs\ProcessPaidPayment;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\Plan;
use App\PaymentStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PaymentController extends Controller
{
    use ControllerTrait;

    public function __construct(Payment $model)
    {
        $this->model = $model::getModel();
    }

    public function create(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:plan,plan_id',
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($request->plan_id);

        $todayCount = Payment::where('payment_id_user', $user->id)
            ->whereDate('payment_created_at', today())
            ->count();

        if ($todayCount >= 10) {
            return response()->json(['message' => 'Batas pembayaran hari ini tercapai (maks 10x). Coba lagi besok.'], 429);
        }

        $payment_status = PaymentStatusEnum::PENDING;
        if($plan->plan_harga == 0){
            $payment_status = PaymentStatusEnum::PAID->value;
        }

        Payment::where('payment_id_user', $user->id)
            ->where('payment_status', PaymentStatusEnum::PENDING->value)
            ->update(['payment_status' => PaymentStatusEnum::CANCELLED->value, 'payment_updated_at' => now()]);

        $qrisString = null;
        $amount = $plan->plan_harga;
        $discount = 0;
        $discountCode = null;

        if ($request->discount_code) {
            $dc = Discount::where('discount_code', strtoupper(trim($request->discount_code)))
                ->where('discount_active', true)
                ->first();

            if ($dc) {
                if ($dc->discount_type === 'percentage') {
                    $discount = (int) round($amount * $dc->discount_value / 100);
                    if ($dc->discount_max_amount) $discount = min($discount, $dc->discount_max_amount);
                } else {
                    $discount = min($dc->discount_value, $amount);
                }
                $amount = $amount - $discount;
                $discountCode = $dc->discount_code;
            }
        }

        if ($amount > 0) {
            $qrisString = nominalQRIS(env('QRIS'), $amount);
        }

        $payment = Payment::create([
            'payment_id_user' => $user->id,
            'payment_id_plan' => $plan->plan_id,
            'payment_order_code' => Payment::generateCode(),
            'payment_jumlah' => $plan->plan_harga,
            'payment_diskon' => $discount,
            'payment_diskon_code' => $discountCode,
            'payment_total' => $amount,
            'payment_qris_string' => $qrisString,
            'payment_status' => $payment_status,
            'payment_metode' => 'qris',
            'payment_expired_at' => now()->addMinutes(10),
            'payment_created_at' => now(),
            'payment_updated_at' => now(),
        ]);

        return response()->json([
            'payment' => $this->formatPayment($payment),
        ]);
    }

    public function status(Request $request, $id)
    {
        $payment = Payment::where('payment_id_user', $request->user()->id)->findOrFail($id);

        if ($payment->payment_status === PaymentStatusEnum::PENDING->value && \Carbon\Carbon::parse($payment->payment_expired_at)->isPast()) {
            $payment->update(['payment_status' => PaymentStatusEnum::EXPIRED->value, 'payment_updated_at' => now()]);
        }

        Artisan::call('payment:process');

        return response()->json([
            'payment' => $this->formatPayment($payment),
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $payment = Payment::where('payment_id_user', $request->user()->id)->findOrFail($id);

        if ($payment->payment_status === PaymentStatusEnum::PENDING->value) {
            $payment->update(['payment_status' => PaymentStatusEnum::CANCELLED->value, 'payment_updated_at' => now()]);
        }

        return response()->json([
            'payment' => $this->formatPayment($payment),
        ]);
    }

    public function settle(Request $request, $id)
    {
        $payment = Payment::where('payment_id_user', $request->user()->id)->findOrFail($id);

        if ($payment->payment_status !== PaymentStatusEnum::PENDING->value) {
            return response()->json(['message' => 'Pembayaran sudah diproses'], 422);
        }

        if (\Carbon\Carbon::parse($payment->payment_expired_at)->isPast()) {
            $payment->update(['payment_status' => PaymentStatusEnum::EXPIRED->value, 'payment_updated_at' => now()]);
            return response()->json(['message' => 'Pembayaran sudah kedaluwarsa'], 422);
        }

        $payment->update([
            'payment_status' => PaymentStatusEnum::PAID->value,
            'payment_paid_at' => now(),
            'payment_updated_at' => now(),
        ]);

        ProcessPaidPayment::dispatch($payment->payment_id);

        $payment->load('has_plan');

        return response()->json([
            'message' => 'Pembayaran berhasil!',
            'payment' => $this->formatPayment($payment),
        ]);
    }

    public function history(Request $request)
    {
        $payments = Payment::where('payment_id_user', $request->user()->id)
            ->with('has_plan')
            ->orderByDesc('payment_created_at')
            ->limit(5)
            ->get()
            ->map(fn ($p) => $this->formatPayment($p));

        return response()->json(['payments' => $payments]);
    }

    private function formatPayment(Payment $payment): array
    {
        return [
            'id' => $payment->payment_id,
            'order_code' => $payment->payment_order_code,
            'plan_id' => $payment->payment_id_plan,
            'plan_name' => $payment->plan?->plan_nama,
            'amount' => $payment->payment_jumlah,
            'discount' => $payment->payment_diskon,
            'discount_code' => $payment->payment_diskon_code,
            'total' => $payment->payment_total,
            'qris_string' => $payment->payment_qris_string,
            'status' => $payment->payment_status,
            'method' => $payment->payment_metode,
            'paid_at' => $payment->payment_paid_at ? \Carbon\Carbon::parse($payment->payment_paid_at)->toIso8601String() : null,
            'expired_at' => \Carbon\Carbon::parse($payment->payment_expired_at)->toIso8601String(),
            'created_at' => $payment->payment_created_at ? \Carbon\Carbon::parse($payment->payment_created_at)->toIso8601String() : null,
        ];
    }

    public function validateDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'plan_id' => 'required|integer|exists:plan,plan_id',
        ]);

        $code = strtoupper(trim($request->code));
        $plan = Plan::findOrFail($request->plan_id);
        $subtotal = $plan->plan_harga;

        if ($subtotal <= 0) {
            return response()->json(['valid' => false, 'message' => 'Tidak bisa menggunakan diskon untuk paket gratis']);
        }

        $discount = Discount::where('discount_code', $code)->first();

        if ($discount) {
            if (!$discount->discount_active) {
                return response()->json(['valid' => false, 'message' => 'Diskon tidak aktif']);
            }

            $now = now();
            if ($discount->discount_start && $now < $discount->discount_start) {
                return response()->json(['valid' => false, 'message' => 'Diskon belum berlaku']);
            }
            if ($discount->discount_end && $now > $discount->discount_end) {
                return response()->json(['valid' => false, 'message' => 'Diskon sudah kedaluwarsa']);
            }

            if ($discount->discount_min_transaction > 0 && $subtotal < $discount->discount_min_transaction) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Minimal transaksi Rp' . number_format($discount->discount_min_transaction),
                ]);
            }

            $amount = $discount->discount_type === 'percentage'
                ? (int) round($subtotal * $discount->discount_value / 100)
                : (int) $discount->discount_value;

            if ($discount->discount_max_amount && $amount > $discount->discount_max_amount) {
                $amount = (int) $discount->discount_max_amount;
            }

            return response()->json([
                'valid' => true,
                'code' => $discount->discount_code,
                'name' => $discount->discount_nama,
                'type' => $discount->discount_type,
                'rate' => $discount->discount_type === 'percentage' ? $discount->discount_value : null,
                'amount' => $amount,
            ]);
        }

        return response()->json(['valid' => false, 'message' => 'Kode tidak ditemukan']);
    }
}
