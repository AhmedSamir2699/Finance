<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\EvaluationCriteria;
use Illuminate\Http\Request;

class EvaluationCriteriaController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.evaluation-criteria.index') => __('manage.evaluation_criteria.headline'),
        ];

        $criteria = EvaluationCriteria::orderBy('name')->paginate(10);
        
        return view('manage.evaluation-criteria.index', compact('criteria', 'breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.evaluation-criteria.index') => __('manage.evaluation_criteria.headline'),
            route('manage.evaluation-criteria.create') => __('manage.evaluation_criteria.create'),
        ];

        return view('manage.evaluation-criteria.create', compact('breadcrumbs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:evaluation_criteria',
            'description' => 'nullable|string',
            'min_value' => 'required|integer|min:0',
            'max_value' => 'required|integer|gt:min_value',
            'is_active' => 'boolean',
        ]);

        EvaluationCriteria::create($request->all());

        flash()->success(__('manage.evaluation_criteria.create.success'));
        return redirect()->route('manage.evaluation-criteria.index');
    }

    public function edit(EvaluationCriteria $criteria)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.evaluation-criteria.index') => __('manage.evaluation_criteria.headline'),
            route('manage.evaluation-criteria.edit', $criteria) => __('manage.evaluation_criteria.edit'),
        ];

        return view('manage.evaluation-criteria.edit', compact('criteria', 'breadcrumbs'));
    }

    public function update(Request $request, EvaluationCriteria $criteria)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:evaluation_criteria,name,' . $criteria->id,
            'description' => 'nullable|string',
            'min_value' => 'required|integer|min:0',
            'max_value' => 'required|integer|gt:min_value',
            'is_active' => 'boolean',
        ]);

        $criteria->update($request->all());

        flash()->success(__('manage.evaluation_criteria.edit.success'));
        return redirect()->route('manage.evaluation-criteria.index');
    }

    public function destroy(EvaluationCriteria $criteria)
    {
        // Check if criteria has scores
        if ($criteria->scores()->exists()) {
            flash()->error(__('manage.evaluation_criteria.delete.has_scores'));
            return redirect()->route('manage.evaluation-criteria.index');
        }

        $criteria->delete();

        flash()->success(__('manage.evaluation_criteria.delete.success'));
        return redirect()->route('manage.evaluation-criteria.index');
    }
} 