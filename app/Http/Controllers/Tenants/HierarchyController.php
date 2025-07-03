<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Models\Tenants\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use \DB;

class HierarchyController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Tenants/Hierarchy/Index', [
            'title' => 'Hierarchy',
            'filters' => $request->all(['search', 'rield', 'order']),
        ]);
    }

    // Dolgozó hozzáadása vezetőhöz
    public function assignChild(Request $request)
    {
        $request->validate([
            'parent_id' => 'request|exists:employees,id',
            'child_id' => 'request|exists:employees,id|diferent:parent_id',
        ]);

        DB::table('hierarchy')->insert([
            'parent_id' => $request->parent_id,
            'child_id' => $request->child_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Dolgozó hozzárendelve.']);
    }

    // Dolgozó áttétele más vezető alá
    public function reassignChild(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:employees,id',
            'child_id' => 'required|exists:employees,id|different:parent_id',
        ]);

        DB::table('hierarchy')
            ->where('child_id', $request->child_id)
            ->update([
                'parent_id' => $request->new_parent_id,
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Dolgozó áthelyezve.']);
    }

    // Dolgozó kivétele a hierarchiából
    public function removeFromHierarchy(int $employeeId)
    {
        DB::table('hierarchy')
            ->where('child_id', $employeeId)
            ->delete();

        return response()->json(['message' => 'Dolgozó eltávolítva a hierarchiából.']);
    }

    public function search(Request $request)
    {
        $query = trim($request->get('q'));
        if (!$query) {
            return response()->json(['error' => 'Üres keresés'], 400);
        }

        /** @var \App\Models\Tenants\Employee|null $employee */
        $employee = Employee::where('name', 'like', "%{$query}%")
            ->first();

        if (! $employee) {
            return response()->json(['message' => 'Nincs találat'], 404);
        }

        // Ha van beosztottja, ő legyen a középpont
        if ($employee->children()->exists()) {
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
                'children' => $children,
            ]);
        }

        // Ha nincs beosztottja, keressük meg a felettesét
        $parentId = DB::table('hierarchy')
            ->where('child_id', $employee->id)
            ->value('parent_id');

        if (! $parentId) {
            // Se beosztott, se felettes — egyedül van
            return response()->json([
                'employee' => [
                    'id' => (string) $employee->id,
                    'label' => $employee->name,
                ],
                'children' => []
            ]);
        }

        $parent = Employee::find($parentId);

        $siblings = $parent->children()->get()->map(function ($child) {
            return [
                'id' => (string) $child->id,
                'label' => $child->name,
                'hasChildren' => $child->children()->exists(),
            ];
        });

        return response()->json([
            'employee' => [
                'id' => (string) $parent->id,
                'label' => $parent->name,
            ],
            'children' => $siblings,
            'highlight' => (string) $employee->id, // opcionális: frontend tudja kiemelni
        ]);

        /*
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
            'children' => $children,
        ]);
        */
    }

    public function root()
    {
        $companyId = session('selected_company_id'); // vagy auth()->user()->company_id

        // Legfelsőbb szint: akinek nincs parent-je a hierarchy táblában
        $ceoId = DB::table('employees')
            ->leftJoin('hierarchy', 'employees.id', '=', 'hierarchy.child_id')
            ->whereNull('hierarchy.parent_id')
            ->where('employees.company_id', $companyId)
            ->value('employees.id');

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
