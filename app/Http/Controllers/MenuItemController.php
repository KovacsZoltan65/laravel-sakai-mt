<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuItemUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class MenuItemController extends Controller
{
    // Menü elemek lekérése a menü felépítéséhez
    public function index(Request $request)
    {
        $menuTree = $this->buildMenuTree();

        $menuItems = [
            ['items' => $menuTree]
        ];

        return response()->json($menuItems);
    }

    // Manager oldal index
    public function manage(Request $request)
    {
        return Inertia::render('Hq/MenuManager/Index', [
            'title' => 'Employees',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }
    
    // Manager oldal adatkérése
    public function fetch(Request $request): JsonResponse
    {
        $tree = $this->buildFullTree();

//\Log::info('$tree: ' . print_r(json_encode($tree) , true));
        
        return response()->json($tree);
        
        //$menuTree = $this->buildMenuTree();
        
        //return response()->json([ ['items' => $menuTree] ]);
    }
    
    protected function buildMenuTree($parentId = null)
    {
        return MenuItem::where('parent_id', $parentId)
            ->orderBy('order_index')
            ->get()
            ->filter(function ($item) {
                // ❗ Ellenőrizzük a route meglétét (ha van route_name)
                if ($item->route_name && !Route::has($item->route_name)) {
                    return false;
                }
                return true;
            })
            ->map(function ($item) {
                return [
                    'label' => $item->label,
                    'icon' => $item->icon,
                    'to' => $item->url ?? ($item->route_name ? route($item->route_name) : null),
                    'items' => $this->buildMenuTree($item->id)
                ];
            })
            ->values(); // újraintexelés
    }
    
    protected function buildFullTree($parentId = null)
    {
        return MenuItem::where('parent_id', $parentId)
            ->orderBy('order_index')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => $item->label,
                    'icon' => $item->icon,
                    'url' => $item->url,
                    'route_name' => $item->route_name,
                    'can' => $item->can,
                    'order_index' => $item->order_index,
                    'parent_id' => $item->parent_id,
                    'items' => $this->buildFullTree($item->id), // 👈 rekurzió
                ];
            })->values();
    }

    private function isValidMenuItem($item)
    {
        // 1. Ellenőrizzük, hogy a route létezik-e (ha van route neve)
        if ($item->route_name && !Route::has($item->route_name)) {
            return false;
        }

        // 2. Gyerekek rekurzív szűrése
        $item->children = $item->children->filter(function ($child) {
            return $this->isValidMenuItem($child);
        })->values();

        return true;
    }

    public function show(MenuItem $menuItem): JsonResponse
    {
        return response()->json($menuItem->load('children'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'order_index' => 'required|integer',
        ]);

        $menuItem = MenuItem::create($validated);

        return response()->json($menuItem);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order_index' => 'required|integer',
        ]);

        $menuItem->update($validated);

        return response()->json($menuItem);
    }

    public function destroy(MenuItem $menuItem)
    {
        // Gyerekekkel együtt törli
        $menuItem->descendants()->delete(); // ha van ilyen relationshiped
        $menuItem->delete();

        return response()->json(['status' => 'deleted']);
    }

    public function getMenu(): JsonResponse
    {
        $menuItems = MenuItem::whereNull('parent_id')->with('children')->get();

        return response()->json($menuItems);
    }

    public function getSortedMenuItems(): JsonResponse
    {
        $menuItems = MenuItem::whereNull('parent_id')
            ->with(['children'])
            ->orderBy('default_weight', 'asc')
            ->get();

        return response()->json($menuItems);
    }

    public function updateMenuUsage($menuItemId): void
    {
        $usage = MenuItemUsage::firstOrCreate([
            'menu_item_id' => $menuItemId,
            'user_id' => auth()->id()
        ]);

        $usage->increment('usage_count');
    }

    public function updateUsage(Request $request): JsonResponse
    {
        $menuItemId = $request->get('menu_item_id');
        $this->updateMenuUsage($menuItemId);

        return response()->json(['message' => 'Usage updated successfully.']);
    }
}
