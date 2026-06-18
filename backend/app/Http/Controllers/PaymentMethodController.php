<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\PaymentMethod;
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
            ->orderBy('payment_method_id')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->payment_method_id,
                'nama' => $m->payment_method_nama,
                'person' => $m->payment_method_person,
                'rekening' => $m->payment_method_rekening,
                'transfer' => $m->payment_method_transfer,
            ]);

        return response()->json(['payment_methods' => $methods]);
    }
}
