<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuItemUsage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $host = $request->getHost();
        $type = str_contains($host, 'hq') ? 'hq' : 'tenant';

        $menuItems = MenuItem::with(['children', 'usages'])
            ->whereNull('parent_id')
            ->orderBy('order_index')->get();

        return response()->json($menuItems);
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
