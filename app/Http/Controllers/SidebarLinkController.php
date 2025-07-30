<?php

namespace App\Http\Controllers;

use App\Models\SidebarLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class SidebarLinkController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.sidebar-links.index') => __('sidebar.manage.sidebar_links'),
        ];

        return view('manage.sidebar-links.index', compact('breadcrumbs'));
    }

    public function create()
    {
        $parentLinks = SidebarLink::whereNull('parent_id')->orderBy('title')->get();
        $permissions = $this->getGroupedPermissions();
        
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.sidebar-links.index') => __('sidebar.manage.sidebar_links'),
            route('manage.sidebar-links.create') => __('common.create'),
        ];

        return view('manage.sidebar-links.create', compact('parentLinks', 'permissions', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255|min:1',
            'icon' => 'required|string|max:100',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'visibility' => 'required|in:all,authenticated,guest',
            'parent_id' => 'nullable|exists:sidebar_links,id',
            'is_active' => 'boolean',
        ]);

        // Normalize the URL to ensure it's a valid route name or path
        $normalizedUrl = $this->normalizeUrl($request->url);

        // Validate internal route exists if it's not external
        $isExternal = $this->isExternalUrl($normalizedUrl);
        if (!$isExternal && $normalizedUrl) {
            // If it looks like a route name but doesn't exist, treat as external
            if (strpos($normalizedUrl, '.') !== false && !Route::has($normalizedUrl)) {
                $isExternal = true;
            } else {
                try {
                    route($normalizedUrl);
                } catch (\Exception $e) {
                    return back()->withErrors(['url' => __('sidebar.errors.route_not_found', ['route' => $normalizedUrl])])->withInput();
                }
            }
        }

        // Auto-assign order (next available order number)
        $maxOrder = SidebarLink::where('parent_id', $request->parent_id)->max('order') ?? 0;
        $nextOrder = $maxOrder + 1;

        $sidebarLink = SidebarLink::create([
            'title' => $request->title,
            'url' => $normalizedUrl,
            'icon' => $request->icon,
            'permission' => $request->permissions ? implode(',', $request->permissions) : null,
            'visibility' => $request->visibility,
            'parent_id' => $request->parent_id,
            'order' => $nextOrder,
            'is_active' => $request->has('is_active'),
            'is_external' => $isExternal,
        ]);

        flash()->success(__('sidebar.success.created'));
        return redirect()->route('manage.sidebar-links.index');
    }

    public function edit(SidebarLink $sidebarLink)
    {
        $parentLinks = SidebarLink::whereNull('parent_id')
            ->where('id', '!=', $sidebarLink->id)
            ->orderBy('title')
            ->get();
        $permissions = $this->getGroupedPermissions();

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.sidebar-links.index') => __('sidebar.manage.sidebar_links'),
            route('manage.sidebar-links.edit', $sidebarLink) => __('common.edit'),
        ];

        return view('manage.sidebar-links.edit', compact('sidebarLink', 'parentLinks', 'permissions', 'breadcrumbs'));
    }

    public function update(Request $request, SidebarLink $sidebarLink)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255|min:1',
            'icon' => 'required|string|max:100',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'visibility' => 'required|in:all,authenticated,guest',
            'parent_id' => 'nullable|exists:sidebar_links,id',
            'is_active' => 'boolean',
        ]);

        // Normalize the URL to ensure it's a valid route name or path
        $normalizedUrl = $this->normalizeUrl($request->url);

        // Validate internal route exists if it's not external
        $isExternal = $this->isExternalUrl($normalizedUrl);
        if (!$isExternal && $normalizedUrl) {
            // If it looks like a route name but doesn't exist, treat as external
            if (strpos($normalizedUrl, '.') !== false && !Route::has($normalizedUrl)) {
                $isExternal = true;
            } else {
                try {
                    route($normalizedUrl);
                } catch (\Exception $e) {
                    return back()->withErrors(['url' => __('sidebar.errors.route_not_found', ['route' => $normalizedUrl])])->withInput();
                }
            }
        }

        // Auto-assign order if parent_id changed or order is not set
        $order = $sidebarLink->order;
        if ($request->parent_id != $sidebarLink->parent_id || !$order) {
            $maxOrder = SidebarLink::where('parent_id', $request->parent_id)->max('order') ?? 0;
            $order = $maxOrder + 1;
        }

        $sidebarLink->update([
            'title' => $request->title,
            'url' => $normalizedUrl,
            'icon' => $request->icon,
            'permission' => $request->permissions ? implode(',', $request->permissions) : null,
            'visibility' => $request->visibility,
            'parent_id' => $request->parent_id,
            'order' => $order,
            'is_active' => $request->has('is_active'),
            'is_external' => $isExternal,
        ]);

        flash()->success(__('sidebar.success.updated'));
        return redirect()->route('manage.sidebar-links.index');
    }

    public function destroy(SidebarLink $sidebarLink)
    {
        $sidebarLink->delete();

        flash()->success(__('sidebar.messages.deleted'));
        return redirect()->route('manage.sidebar-links.index');
    }

    public function reorder(Request $request)
    {
        // Handle JSON request for drag-and-drop reordering
        if ($request->isJson()) {
            $data = $request->json()->all();
            
            // Handle parent links reordering
            if (isset($data['parents'])) {
                foreach ($data['parents'] as $item) {
                    SidebarLink::where('id', $item['id'])->update(['order' => $item['order']]);
                }
            }
            
            // Handle children reordering
            if (isset($data['children'])) {
                foreach ($data['children'] as $parentId => $children) {
                    foreach ($children as $item) {
                        SidebarLink::where('id', $item['id'])->update(['order' => $item['order']]);
                    }
                }
            }
            
            return response()->json(['success' => true]);
        }
        
        // Handle legacy array format
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:sidebar_links,id',
        ]);

        SidebarLink::reorder($request->order);

        return response()->json(['success' => true]);
    }

    public function toggleStatus(SidebarLink $sidebarLink)
    {
        $sidebarLink->update(['is_active' => !$sidebarLink->is_active]);

        flash()->success(__('sidebar.messages.status_updated'));
        return redirect()->route('manage.sidebar-links.index');
    }

    private function isExternalUrl($url): bool
    {
        if (!$url || trim($url) === '') {
            return false;
        }
        
        // Check if URL starts with http:// or https:// or //
        if (preg_match('/^https?:\/\//', $url) || str_starts_with($url, '//')) {
            return true;
        }
        
        // Check if it's a valid internal route (either dot notation or path)
        try {
            // First try as dot notation route name
            if (strpos($url, '.') !== false) {
                if (Route::has($url)) {
                    route($url);
                    return false; // Route exists, so it's internal
                } else {
                    return true; // Route name doesn't exist, treat as external
                }
            }
            
            // Try as path URL
            $routeName = $this->getRouteNameFromPath($url);
            if ($routeName && Route::has($routeName)) {
                return false; // Path can be converted to route, so it's internal
            }
            
            // If neither works, treat as external
            return true;
        } catch (\Exception $e) {
            // If any error occurs, treat as external
            return true;
        }
    }

    private function getRouteNameFromPath($path): ?string
    {
        if (!$path || trim($path) === '') {
            return null;
        }
        
        // Remove leading slash if present
        $path = ltrim($path, '/');
        
        // Get all routes
        $routes = Route::getRoutes();
        
        foreach ($routes as $route) {
            $routePath = $route->uri();
            
            // Try exact match first
            if ($routePath === $path) {
                return $route->getName();
            }
            
            // Try to match with parameters
            try {
                // Create a mock request to test the route
                $request = \Illuminate\Http\Request::create('/' . $path);
                if ($route->matches($request)) {
                    return $route->getName();
                }
            } catch (\Exception $e) {
                // Continue to next route if this one fails
                continue;
            }
        }
        
        return null;
    }

    private function normalizeUrl($url): string
    {
        if (!$url || trim($url) === '') {
            return $url;
        }
        
        // If it's already a dot notation route name, validate it exists
        if (strpos($url, '.') !== false && !str_starts_with($url, 'http')) {
            try {
                route($url); // Test if it's a valid route name
                return $url; // Return as is if valid
            } catch (\Exception $e) {
                // Not a valid route name, continue to path conversion
            }
        }
        
        // If it's a path URL, try to convert to route name
        if (str_starts_with($url, '/') || !str_starts_with($url, 'http')) {
            $routeName = $this->getRouteNameFromPath($url);
            if ($routeName && Route::has($routeName)) {
                return $routeName;
            }
        }
        
        // Return original URL if no conversion possible
        return $url;
    }

    private function getGroupedPermissions()
    {
        $permissions = \Spatie\Permission\Models\Permission::all();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $category = explode('.', $permission->name)[0];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [
                    'text' => __('permissions.categories.' . $category),
                    'children' => []
                ];
            }
            $grouped[$category]['children'][] = [
                'id' => $permission->name,
                'text' => $permission->display_name ?? $permission->name
            ];
        }
        
        return array_values($grouped);
    }
} 