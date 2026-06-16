<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Affiliate;

class AffiliateController extends Controller
{
    use ControllerTrait;

    public function __construct(Affiliate $model)
    {
        $this->model = $model::getModel();
    }
}
