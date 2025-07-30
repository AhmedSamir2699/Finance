<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Flasher\Toastr\Prime\ToastrInterface;
use Illuminate\Support\Facades\Validator;

class DepartmentsController extends Controller
{
    function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
           route('manage.departments.index') => __('breadcrumbs.departments.index')
        ];
        
        $departmentsCount = Department::count();
        return view('manage.departments.index', compact('departmentsCount', 'breadcrumbs'));
    }

    function myDepartment()
    {
        $department = Department::find(auth()->user()->department_id);
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('my-department') => $department->name
        ];
        return view('departments.my-department', compact('department', 'breadcrumbs'));
    }

    function show(Department $department)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.departments.index') => __('breadcrumbs.departments.index'),
            route('manage.departments.show',[$department]) => $department->name
        ];

        return view('manage.departments.show', compact('department', 'breadcrumbs'));

    }

    function create()
    {
        return view('manage.departments.create');
    }



    function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            flash()->error(__('departments.index.validation_error'));
            return back();   
        }


        if(!Department::create([
            'name' => $request->name,
            'description' => $request->description
        ])){
            flash()->error(__('departments.index.create_error'));
            return back();
        }

        flash()->success(__('departments.index.create_success'));
        return redirect()->route('manage.departments.index');
        
    }

    function edit(Department $department)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.departments.index') => __('breadcrumbs.departments.index'),
            route('manage.departments.show',[$department]) => $department->name,
            '' => __('breadcrumbs.departments.edit')
        ];

        return view('manage.departments.edit', compact('department', 'breadcrumbs'));
    }

    function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            flash()->error(__('departments.index.validation_error'));
            return back();   
        }

        if(!$department->update([
            'name' => $request->name,
            'description' => $request->description
        ])){
            flash()->error(__('departments.index.update_error'));
            return back();
        }

        flash()->success(__('departments.index.update_success'));
        return redirect()->route('manage.departments.index');
    }

    function destroy(Department $department)
    {
        if(!$department->delete()){
            flash()->error(__('departments.index.delete_error'));
            return back();
        }

        flash()->success(__('departments.index.delete_success'));
        return redirect()->route('manage.departments.index');
    }
}
