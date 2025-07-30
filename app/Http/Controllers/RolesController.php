<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Helpers\BreadcrumbHelper;
use App\Models\RoleRelationship;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.roles.index') => __('breadcrumbs.roles.index'),
        ];

        $rolesCount = Role::count();
        $roles = Role::withCount('permissions', 'users')->get();
        return view('manage.roles.index', compact('rolesCount', 'roles', 'breadcrumbs'));
    }

    function edit(Role $role)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.roles.index') => __('breadcrumbs.roles.index'),
            route('manage.roles.edit', [$role]) => __('breadcrumbs.roles.edit'),
        ];
        return view('manage.roles.edit', compact('role', 'breadcrumbs'));
    }

    function permissions(Role $role)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.roles.index') => __('breadcrumbs.roles.index'),
            route('manage.roles.edit.permissions', [$role]) => __('breadcrumbs.roles.permissions'),
        ];
        return view('manage.roles.permissions', compact('role', 'breadcrumbs'));
    }


    function show() {}

    function create()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.roles.index') => __('breadcrumbs.roles.index'),
            route('manage.roles.create') => __('breadcrumbs.roles.create'),
        ];

        return view('manage.roles.create', compact('breadcrumbs'));
    }

    function store(Request $request)
    {
        $data = $request->except('_token', 'answers_to');

        $data['name'] = str_replace(' ', '-', $data['name']);

        $role = Role::create($data);
        if ($request->has('answers_to') && $request->answers_to) {
            RoleRelationship::create([
                'superior_role_id' => $request->answers_to,
                'subordinate_role_id' => $role->id
            ]);
        }

        flash()->success(__('roles.index.success'));
        return redirect()->route('manage.roles.index');
    }

    function update(Role $role, Request $request)
    {

        if ($request->has('answers_to') && !is_null($request->answers_to)) {
            RoleRelationship::where('subordinate_role_id', $role->id)
                            ->delete();
                            
            RoleRelationship::create([
                'superior_role_id' => $request->answers_to,
                'subordinate_role_id' => $role->id
            ]);
        }

        $role->update($request->except('_token', 'answers_to'));

        flash()->success(__('roles.index.success'));
        return redirect()->route('manage.roles.index');
    }

    function destroy(Role $role)
    {
        if(!auth()->user()->can('role.delete')) {
            flash()->error(__('roles.index.error'));
            return redirect()->route('manage.roles.index');
        }
        $role->delete();
        flash()->success(__('roles.index.success'));
        return redirect()->route('manage.roles.index');
    }

    function permissionsStore(Role $role, Request $request)
    {
        $data = $request->except('_token', 'answers_to');

        $data['name'] = str_replace(' ', '-', $data['name']);

        Permission::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'],
        ]);
        flash()->success(__('roles.index.success'));

        return redirect()->route('manage.roles.edit.permissions', [$role]);
    }
}
