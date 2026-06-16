<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Subscribe;

class SubscribeController extends Controller
{
    use ControllerTrait;

    public function __construct(Subscribe $model)
    {
        $this->model = $model::getModel();
    }
}
