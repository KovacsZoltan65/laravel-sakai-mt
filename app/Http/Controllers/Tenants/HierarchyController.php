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
    
    public function root()
    {
        $companyId = session('selected_company_id'); // vagy auth()->user()->company_id

        // Legfelsőbb szint: akinek nincs parent-je a hierarchy táblában
        $ceoId = \DB::table('employees')
            ->leftJoin('hierarchy', 'employees.id', '=', 'hierarchy.child_id')
            ->whereNull('hierarchy.parent_id')
            ->where('employees.company_id', $companyId)
            ->value('employees.id');
        
\Log::info('$ceoId: ' . print_r($ceoId, true));
        
        if (! $ceoId) {
            return response()->json(['error' => 'No CEO found'], 404);
        }
        
        $ceo = Employee::findOrFail($ceoId);
        
        $children = $ceo->children()->get()->map(function ($child) {
            return [
                'id' => (string) $child->id,
                'label' => $child->name,
                'hasChildren' => $child->children()->exists(),
            ];
        });
        
        return response()->json([
            'employee' => [
                'id' => (string) $ceo->id,
                'label' => $ceo->name
            ],
            'children' => $children
        ]);
    }
    
    public function children(int $employee_id)
    {
        $employee = Employee::find($employee_id);

        $children = $employee->children()->get()->map(function ($child) {
            return [
                'id' => (string) $child->id,
                'label' => $child->name,
                'hasChildren' => $child->children()->exists(),
            ];
        });
        
        return response()->json([
            'employee' => [
                'id' => (string) $employee->id,
                'label' => $employee->name,
            ],
            'children' => $children
        ]);
        
    }
}
