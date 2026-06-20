<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Addon;

class AddonWebController extends Controller
{
    use ControllerTrait;

    public function __construct(Addon $model)
    {
        $this->model = $model::getModel();
    }
}
