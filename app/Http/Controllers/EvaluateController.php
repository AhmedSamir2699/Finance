<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EvaluateController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('evaluate.index') => __('breadcrumbs.evaluate.index'),
        ];

        return view('evaluate.index', compact('breadcrumbs'));
    }

    public function evaluate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'message' => 'required',
        ]);

        // Send email
        // Mail::to($data['email'])->send(new EvaluateMail($data));

        return redirect()->back()->with('success', 'Your message has been sent successfully.');
    }

    public function random()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('evaluate.index') => __('breadcrumbs.evaluate.index'),
            route('evaluate.random') => __('breadcrumbs.evaluate.random')
        ];
        return view('evaluate.random', ['breadcrumbs' => $breadcrumbs]);
    }
}
