<?php

namespace App\Http\Controllers;

use App\Models\OperationalPlan;
use Illuminate\Http\Request;

class OperationalPlanGuestController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('guest.operational_plan.index') => __('breadcrumbs.executive-plan.index'),
        ];

        $plans = OperationalPlan::where('is_public', true)->latest()->get();
        return view(
            'guest.operational_plan.index',
            [
                'breadcrumbs' => $breadcrumbs,
                'plans' => $plans,
            ]
        );
    }

    public function show($id)
    {
        $plan = OperationalPlan::where('is_public', true)->findOrFail($id);
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('guest.operational_plan.index') => __('breadcrumbs.executive-plan.index'),
            route('guest.operational_plan.show', $plan->id) => $plan->title,
        ];
        $plan->load('departments');
        $plan->increment('views');
        return view(
            'guest.operational_plan.show',
            [
                'breadcrumbs' => $breadcrumbs,
                'plan' => $plan,
                'departments' => $plan->departments,
            ]
        );
    }

    public function create()
    {
        return view('guest.operational_plan.create');
    }

    public function store(Request $request)
    {
        // Store the operational plan
        return redirect()->route('operational_plan.index');
    }
}
