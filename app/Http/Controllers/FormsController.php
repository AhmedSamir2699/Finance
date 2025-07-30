<?php

namespace App\Http\Controllers;

use App\Models\RequestForm;
use App\Models\RequestFormCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormsController extends Controller
{
    function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            "" => __('breadcrumbs.manage.index'),
            route('manage.forms.index') => __('breadcrumbs.manage.forms')
        ];

        $forms = RequestForm::paginate();

        return view('manage.forms.index', [
            'breadcrumbs' => $breadcrumbs,
            'forms' => $forms
        ]);
    }

    function create()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            "" => __('breadcrumbs.manage.index'),
            route('manage.forms.index') => __('breadcrumbs.manage.forms')
        ];

        $categories = RequestFormCategory::all();

        return view('manage.forms.create', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories
        ]);
    }

    function store(Request $request)
    {

        $background = $request->file('background')->store('forms', 'public');

        $form = RequestForm::create([
            'title' => $request->title,
            'description' => $request->description,
            'background' => $background,
            'request_form_category_id' => $request->category
        ]);

        return redirect()->route('manage.forms.index');
    }

    function fields(RequestForm $form)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            "" => __('breadcrumbs.manage.index'),
            route('manage.forms.index') => __('breadcrumbs.manage.forms')
        ];

        return view('manage.forms.fields', [
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ]);
    }

    function edit(RequestForm $form)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.forms.index') => __('breadcrumbs.manage.forms'),
            "" => $form->title

        ];

        $categories = RequestFormCategory::all();

        return view('manage.forms.edit', [
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'categories' => $categories
        ]);
    }

    function update(Request $request, RequestForm $form)
    {
        if ($request->hasFile('background')) {
            Storage::disk('public')->delete($form->background);
            $background = $request->file('background')->store('forms', 'public');
            $form->update(['background' => $background]);
        }
        $form->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return redirect()->route('manage.forms.index');
    }

    function destroy(RequestForm $form)
    {
        $form->departments()->delete();
        $form->delete();

        return redirect()->route('manage.forms.index');
    }

    function fieldPosition(RequestForm $form)
    {
        $fields = $form->fields;
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.forms.index') => __('breadcrumbs.manage.forms'),
            route('manage.forms.fields', $form) => $form->title,
            "" => __('breadcrumbs.manage.forms.fields-position')
        ];

        return view('manage.forms.fields-position', [
          
            'breadcrumbs' => $breadcrumbs,
            'record' => $form

        ]);
    }
}
