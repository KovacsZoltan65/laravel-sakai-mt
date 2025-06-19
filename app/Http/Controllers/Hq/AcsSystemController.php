<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;


class AcsSystemController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('AcsSystems/Index',
            [
                'title' => 'Hq Acs Systems',
                'filters' => $request->all(['search', 'field', 'order']),
            ]
        );
    }

    public function fetch(Request $request)
    {
        //
    }
}
