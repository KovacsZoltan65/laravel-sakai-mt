<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Models\MenuItem;
use App\Models\MenuItemUsage;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $menuTree = $this->buildMenuTree();

        $menuItems = [
            ['items' => $menuTree]
        ];
        
        return response()->json($menuItems);
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
    
    /*
    protected function buildMenuTree($parentId = null)
    {
        return MenuItem::where('parent_id', $parentId)
            ->orderBy('order_index') // 💡 fontos
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->label,
                    'icon' => $item->icon,
                    'to' => $item->url ?? ($item->route_name ? route($item->route_name) : null),
                    'items' => $this->buildMenuTree($item->id) // 🧠 ez is rendezett lesz
                ];
            });
    }
    */

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
    
    public function index_old(Request $request)
    {
        $host = $request->getHost();
        //$type = str_contains($host, 'hq') ? 'hq' : 'tenant';

        $menuItems = MenuItem::with(['children', 'usages'])
            ->whereNull('parent_id')
            ->orderBy('order_index')->get();

        // Rekurzívan szűrjük a menüpontokat
        $filtered = $menuItems->filter(function ($item) {
            return $this->isValidMenuItem($item);
        })->values(); // újraindexeljük az elemeket

        /*
        foreach($filtered as $filter) {
//\Log::info('$filter: ' . print_r($filter->label, true));

            if( $filter->children ) {
                foreach($filter->children as $cild) {
//\Log::info('$cild: ' . print_r($cild->label, true));
                }
            }
        }
        */

        return response()->json($filtered);

        //return response()->json($menuItems);
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

/*
        // Itt később jogosultság/tenant szerint is szűrhetsz
        return response()->json([
            [
                'items' => [
                    [
                        'label' => 'home',
                        'icon' => 'pi pi-home',
                        'to' => null,
                        'items' => [
                            [
                                'label' => 'dashboard',
                                'icon' => 'pi pi-th-large',
                                'to' => 'http://company-02.mt/dashboard',
                                'items' => []
                            ]
                        ]
                    ],
                    [
                        'label' => 'administration',
                        'icon' => 'pi pi-cog',
                        'to' => null,
                        'items' => [
                            [
                                'label' => 'employees',
                                'icon' => 'pi pi-users',
                                'to' => 'http://company-02.mt/employees',
                                'items' => []
                            ],
                            [
                                'label' => 'hierarchy',
                                'icon' => 'pi pi-share-alt',
                                'to' => 'http://company-02.mt/hierarchy',
                                'items' => []
                            ],
                            [
                                'label' => 'companies',
                                'icon' => 'pi pi-building',
                                'to' => 'http://company-02.mt/companies',
                                'items' => []
                            ]
                        ]
                    ],
                    [
                        'label' => 'reports',
                        'icon' => 'pi pi-chart-line',
                        'to' => null,
                        'items' => [
                            [
                                'label' => 'monthly',
                                'icon' => 'pi pi-calendar',
                                'to' => null,
                                'items' => [
                                    [
                                        'label' => 'monthly_01',
                                        'icon' => '',
                                        'to' => 'http://company-02.mt/dashboard',
                                        'items' => []
                                    ]
                                ]
                            ],
                            [
                                'label' => 'annual',
                                'icon' => 'pi pi-file',
                                'to' => 'http://company-02.mt/dashboard',
                                'items' => []
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        */