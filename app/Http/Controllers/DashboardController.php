<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Role;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todaysIncomes = Income::whereDate('entry_date', now())->get();
        $todaysExpenses = Expense::whereDate('entry_date', now())->get();

        return view('dashboard', [
            'todaysIncomes' => $todaysIncomes,
            'todaysExpenses' => $todaysExpenses,
        ]);

    }
}
