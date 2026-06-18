<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\PaymentMethod;
use App\PaymentMethodCategoryEnum;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    use ControllerTrait;

    public function __construct(PaymentMethod $model)
    {
        $this->model = $model::getModel();
    }

    public function xgetActive(Request $request)
    {
        $methods = PaymentMethod::where('payment_method_active', 1)
            ->orderBy('payment_method_category')
            ->orderBy('payment_method_id')
            ->get()
            ->map(function ($m) {
                $categoryAttr = $m->getAttributes()['payment_method_category'] ?? null;
                // Handle both string and enum value
                $categoryValue = $categoryAttr instanceof \BackedEnum ? $categoryAttr->value : ($categoryAttr ?? 'qris');
                $enum = PaymentMethodCategoryEnum::tryFrom($categoryValue);
                return [
                    'id' => $m->payment_method_id,
                    'nama' => $m->payment_method_nama,
                    'person' => $m->payment_method_person,
                    'rekening' => $m->payment_method_rekening,
                    'transfer' => $m->payment_method_transfer,
                    'category' => $categoryValue,
                    'group' => $enum?->group() ?? $this->guessGroupFromCategory($categoryValue),
                ];
            });

        $grouped = $methods->groupBy('category')->map(function ($items, $cat) {
            $enum = PaymentMethodCategoryEnum::tryFrom($cat);
            return [
                'group' => $enum?->group() ?? $this->guessGroupFromCategory($cat),
                'items' => $items->map(fn ($m) => [
                    'value' => $m['category'],
                    'name' => $m['nama'],
                ])->values(),
            ];
        })->values();

        return response()->json([
            'payment_methods' => $methods,
            'bank_options' => $grouped,
        ]);
    }

    public function xgetCategories(Request $request)
    {
        $categories = collect(PaymentMethodCategoryEnum::cases())->map(function ($enum) {
            return [
                'value' => $enum->value,
                'name' => $enum->description(),
                'group' => $enum->group(),
            ];
        });

        $grouped = $categories->groupBy('group')->map(function ($items, $group) {
            return [
                'group' => $group,
                'items' => $items->values(),
            ];
        })->values();

        return response()->json([
            'categories' => $categories,
            'grouped' => $grouped,
        ]);
    }

    public function xgetList(Request $request)
    {
        // Use enum directly
        $methods = collect(PaymentMethodCategoryEnum::cases())->map(function ($enum) {
            return [
                'id' => $enum->value,
                'nama' => $enum->description(),
                'category' => $enum->value,
                'group' => $enum->group(),
                'description' => $enum->description(),
            ];
        });

        return response()->json([
            'payment_methods' => $methods,
        ]);
    }

    private function guessGroupFromCategory(string $category): string
    {
        $category = strtolower($category);

        if (str_contains($category, 'qris')) return 'QRIS';
        if (in_array($category, ['gopay', 'ovo', 'dana', 'shopeepay', 'linkaja'])) return 'E-Wallet';
        if (str_contains($category, 'ewallet') || str_contains($category, 'wallet')) return 'E-Wallet';

        return 'Bank Transfer';
    }
}
