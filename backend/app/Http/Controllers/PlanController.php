<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Plan;

class PlanController extends Controller
{
    use ControllerTrait;

    public function __construct(Plan $model)
    {
        $this->model = $model::getModel();
    }
}
