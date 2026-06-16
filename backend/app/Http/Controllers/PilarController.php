<?php

namespace App\Http\Controllers;

use App\Models\Pilar;
use App\Models\MasterSkill;
use App\StatusEnum;
use Illuminate\Http\Request;

class PilarController extends Controller
{
    public function index(Request $request)
    {
        $pilars = Pilar::where('pilar_active', true)
            ->where('pilar_status', StatusEnum::APPROVED->value)
            ->orderBy('pilar_sort_order')
            ->get()
            ->map(fn ($p) => $p->toArray());

        $skills = MasterSkill::where('skill_active', true)
            ->orderBy('skill_sort_order')
            ->get()
            ->map(fn ($s) => $s->toArray());

        return response()->json([
            'pilars' => $pilars,
            'skills' => $skills,
        ]);
    }
}
