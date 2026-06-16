<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    use ControllerTrait;

    public function __construct(Discount $model)
    {
        $this->model = $model::getModel();
    }
    public function index(Request $request)
    {
        $discounts = Discount::where('discount_created_by', $request->user()->id)
            ->orderByDesc('discount_created_at')
            ->get()
            ->map(fn ($d) => $this->format($d));

        $maxDiscounts = (int) config('langkahkecil.affiliate.max_discounts', 3);
        $maxValue = (int) config('langkahkecil.affiliate.max_discount_value', 15);
        $maxNominal = (int) config('langkahkecil.affiliate.max_discount_nominal', 10000);

        return response()->json([
            'discounts' => $discounts,
            'config' => [
                'max_discounts' => $maxDiscounts,
                'max_value' => $maxValue,
                'max_nominal' => $maxNominal,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $maxDiscounts = (int) config('langkahkecil.affiliate.max_discounts', 3);
        $maxValue = (int) config('langkahkecil.affiliate.max_discount_value', 15);
        $maxNominal = (int) config('langkahkecil.affiliate.max_discount_nominal', 10000);

        $currentCount = Discount::where('discount_created_by', $user->id)->count();
        if ($currentCount >= $maxDiscounts) {
            return response()->json(['message' => "Maksimal {$maxDiscounts} kode diskon"], 422);
        }

        $request->validate([
            'discount_code' => 'required|string|min:4|max:20|alpha_dash|unique:discounts,discount_code',
            'discount_nama' => 'required|string|max:100',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
        ]);

        $value = (int) $request->discount_value;
        if ($request->discount_type === 'percentage' && $value > $maxValue) {
            return response()->json(['message' => "Maksimal diskon {$maxValue}%"], 422);
        }
        if ($request->discount_type === 'fixed' && $value > $maxNominal) {
            return response()->json(['message' => "Maksimal diskon Rp" . number_format($maxNominal)], 422);
        }

        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->discount_code));

        $discount = Discount::create([
            'discount_code' => $code,
            'discount_nama' => $request->discount_nama,
            'discount_type' => $request->discount_type,
            'discount_value' => $value,
            'discount_active' => true,
            'discount_created_at' => now(),
            'discount_updated_at' => now(),
        ]);

        return response()->json(['discount' => $this->format($discount)]);
    }

    public function destroy(Request $request, $id)
    {
        $discount = Discount::where('discount_id', $id)
            ->where('discount_created_by', $request->user()->id)
            ->firstOrFail();

        $discount->delete();

        return response()->json(['message' => 'Diskon dihapus']);
    }

    private function format(Discount $d): array
    {
        return [
            'id' => $d->discount_id,
            'code' => $d->discount_code,
            'name' => $d->discount_nama,
            'type' => $d->discount_type,
            'value' => $d->discount_value,
            'active' => $d->discount_active,
            'created_at' => $d->discount_created_at,
        ];
    }
}
