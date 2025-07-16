<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuItemUsage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $menuTree = $this->buildMenuTree();

        $menuItems = [
            ['items' => $menuTree]
        ];
//\Log::info('$menuTree: ' . print_r($menuTree, true));

        foreach($menuTree as $menu) {
//\Log::info('$menu: ' . print_r($menu, true));
//\Log::info('$menu label: ' . print_r($menu['label'], true));
            if( count($menu['items']) > 0 ) {
                foreach($menu['items'] as $child_1) {
//\Log::info('$child_1 label: ' . print_r($child_1['label'], true));
                    if( count($child_1['items']) > 0 ) {
                        foreach( $child_1['items'] as $child_2 ) {
//\Log::info('$child_2 label: ' . print_r($child_2['label'], true));
                        }
                    }
                }
            }
        }

        return response()->json($menuItems);
    }
    
    protected function buildMenuTree($parentId = null)
    {
\Log::info('$parentId: ' . print_r($parentId, true));
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
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'default_weight' => 'nullable|integer',
            'parent_id' => 'nullable|exists:menu_items,id'
        ]);

        $menuItem = MenuItem::create($validated);
        return response()->json($menuItem, 201);
    }
    
    public function update(Request $request, MenuItem $menuItem): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'default_weight' => 'sometimes|required|integer',
        ]);

        $menuItem->update($validated);
        return response()->json($menuItem);
    }
    
    public function destroy(MenuItem $menuItem): Response
    {
        $menuItem->delete();
        return response()->noContent();
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
