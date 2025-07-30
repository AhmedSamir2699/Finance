<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todaysTasks = auth()->user()->tasks()->whereDate('due_date','<=',now())->whereNot('status','submitted')->whereNot('status','approved')->get();
        $todaysEvents = auth()->user()->events()->with('column')->whereDate('date','=',now())->get();
        

        return view('dashboard', [
            'todaysTasks' => $todaysTasks,
            'todaysEvents' => $todaysEvents,
        ]);
    }
}
