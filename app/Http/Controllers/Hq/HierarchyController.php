<?php

namespace App\Http\Controllers\Hq;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Exception;

class HierarchyController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Hq/Hierarchy/Index', [
            'title' => 'Hierarchy',
            'filter' => $reauest->all(['search', 'field', 'order']),
        ]);
    }
}
