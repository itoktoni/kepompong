<?php

namespace App\Actions;

use App\Concerns\PayloadTrait;
use App\Models\Discount;
use App\Models\Plan;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class PlanAction
{
    use AsAction, PayloadTrait;

    public function handle(Request $request)
    {
        $data = $request->validate(Plan::getModel()->rules());

        try {
            return $this->payload(TOAST_SUCCESS, Plan::create($data));
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(Plan::getModel()->rules());

        try {
            $plan = Plan::findOrFail($id);
            $plan->update($data);

            return $this->payload(TOAST_SUCCESS, $plan);
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }

    public function delete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        try {
            Plan::whereIn(Plan::field_primary(), $request->ids)->delete();

            return $this->payload(TOAST_SUCCESS, $request->ids);
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }

    public function remove($id)
    {
        try {
            Plan::findOrFail($id)->delete();

            return $this->payload(TOAST_SUCCESS, ['id' => $id]);
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }

    public function find($id)
    {
        try {
            return $this->payload(TOAST_SUCCESS, Plan::findOrFail($id));
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }

    public static function all(Request $request = null)
    {
        $query = Plan::filter()->sort();

        if ($request) {
            $data = $query->cursorPaginate($request->input('per_page', 25))->withQueryString();
        } else {
            $data = $query->cursorPaginate(25);
        }

        return $data;
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'discount_code' => 'nullable|string',
        ]);

        $user = $request->user();
        $subscriptionPlan = SubscriptionPlan::with('features')->findOrFail($request->plan_id);

        $price = $subscriptionPlan->price;
        $discountAmount = 0;
        $discountDescription = null;
        $discountModel = null;

        if ($request->discount_code) {
            $code = strtoupper(trim($request->discount_code));
            $discountModel = Discount::where('discount_code', $code)->first();

            if (! $discountModel) {
                return response()->json(['message' => 'Kode diskon tidak valid'], 422);
            }

            if (! $discountModel->isValid($price)) {
                return response()->json(['message' => 'Kode diskon tidak aktif atau sudah kedaluwarsa'], 422);
            }

            $discountAmount = $discountModel->calculate($price);
            $discountDescription = $discountModel->discount_nama;
        }

        $finalPrice = max(0, $price - $discountAmount);

        $user->plan = $subscriptionPlan->name;
        $user->plan_start_date = Carbon::now();
        $user->plan_end_date = Carbon::now()->addYear();
        $user->role = 'premium';
        $user->save();

        return response()->json([
            'message' => 'Pembayaran berhasil (simulasi)',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'subscribe_id' => $user->plan,
                'plan_start_date' => $user->plan_start_date?->toIso8601String(),
                'plan_end_date' => $user->plan_end_date?->toIso8601String(),
                'trial_start_date' => $user->trial_start_date?->toIso8601String(),
            ],
            'payment' => [
                'plan_id' => $subscriptionPlan->id,
                'plan_name' => $subscriptionPlan->name,
                'original_price' => $price,
                'discount_code' => $discountModel->discount_code ?? null,
                'discount_type' => $discountModel->discount_type ?? null,
                'discount_value' => $discountModel->discount_value ?? 0,
                'discount_amount' => $discountAmount,
                'discount_description' => $discountDescription,
                'final_price' => (int) $finalPrice,
                'currency' => $subscriptionPlan->currency,
            ],
            'features' => $subscriptionPlan->features->map(fn ($f) => [
                'slug' => $f->slug,
                'name' => $f->name,
                'value' => $f->value,
            ]),
        ]);
    }

    public function validatePlan(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'trial') {
            $trialDays = (int) config('langkahkecil.trial_days', 10);
            $trialStart = $user->trial_start_date;
            $trialEnd = $trialStart ? $trialStart->addDays($trialDays) : null;
            $isExpired = $trialEnd && Carbon::now()->isAfter($trialEnd);

            return response()->json([
                'valid' => ! $isExpired,
                'type' => 'trial',
                'role' => $user->role,
                'trial_start_date' => $trialStart?->toIso8601String(),
                'trial_end_date' => $trialEnd?->toIso8601String(),
                'days_remaining' => $trialEnd ? max(0, Carbon::now()->diffInDays($trialEnd, false)) : 0,
            ]);
        }

        $planEnd = $user->plan_end_date;
        $isActive = ! $planEnd || Carbon::now()->isBefore($planEnd);

        return response()->json([
            'valid' => $isActive,
            'type' => 'subscription',
            'role' => $user->role,
            'plan' => $user->plan,
            'plan_start_date' => $user->plan_start_date?->toIso8601String(),
            'plan_end_date' => $planEnd?->toIso8601String(),
            'days_remaining' => $planEnd ? max(0, Carbon::now()->diffInDays($planEnd, false)) : null,
        ]);
    }
}
