<?php

namespace App\Http\Controllers;

use App\Charts\DashboardChart;

class DashboardController extends Controller
{
    public function __invoke(DashboardChart $chart)
    {
        return view('dashboard', ['chart' => $chart->build()]);
    }
}
