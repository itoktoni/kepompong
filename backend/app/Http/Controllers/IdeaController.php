<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Idea;

class IdeaController extends Controller
{
    use ControllerTrait;

    public function __construct(Idea $model)
    {
        $this->model = $model::getModel();
    }
}
