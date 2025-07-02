<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Models\Tenants\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class HierarchyController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Tenants/Hierarchy/Index', [
            'title' => 'Hierarchy',
            'filters' => $request->all(['search', 'rield', 'order']),
        ]);
    }
    
    public function children(Employee $employee)
    {
        $children = $employee->children()->get()->map(function ($child) {
            return [
                'id' => (string) $child->id,
                'label' => $child->name,
                'hasChildren' => $child->children()->exists(),
            ];
        });
        
\Log::info('$employee: ' . print_r($employee, true));
\Log::info('$children: ' . print_r($children, true));
        
        return response()->json([
            'employee' => [
                'id' => (string) $employee->id,
                'label' => $employee->name,
            ],
            'children' => $children
        ]);
    }
}
