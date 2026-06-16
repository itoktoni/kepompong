<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Cashout;

class CashoutController extends Controller
{
    use ControllerTrait;

    public function __construct(Cashout $model)
    {
        $this->model = $model::getModel();
    }
}
