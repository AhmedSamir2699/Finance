<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Alkoumi\LaravelHijriDate\Hijri;
use App\Helpers\NotificationHelper;
use App\Models\ExecutivePlan;
use App\Models\ExecutivePlanCell;
use App\Models\ExecutivePlanColumn;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExecutivePlanController extends Controller
{

    public function index(User $user = null, $year = null)
    {

        if ($user) {
            if (auth()->user()->cannot('executive-plan.view-any')  && auth()->user()->cannot('executive-plan.view-department')) {
                abort(403);
            }

            $breadcrumbs = [
                route('dashboard') => __('breadcrumbs.dashboard.index'),
                route('users.show', [$user]) => $user->name,
                route('executive-plan.index') => __('breadcrumbs.executive-plan.index'),
            ];
        } else {
            $breadcrumbs = [
                route('dashboard') => __('breadcrumbs.dashboard.index'),
                route('executive-plan.index') => __('breadcrumbs.executive-plan.index'),
            ];

            $user = auth()->user();
        }

        $year = $year ?? Carbon::now()->year;


        $availableYears = ExecutivePlanCell::selectRaw('YEAR(date) as year')
            ->where('user_id', $user)
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect(['year' => Carbon::now()->year]);
        }

        $plans = new ExecutivePlanCell();




        return view('executive-plan.index', [
            'breadcrumbs' => $breadcrumbs,
            'plans' => $plans,
            'availableYears' => $availableYears,
            'currentYear' => $year,
            'hijri' => new Hijri(),
            'user' => $user


        ]);
    }

    function show($month = null, $year = null)
    {



        if ($month) {
            if ($year) {
                $month = Carbon::create($year, $month);
            } else {
                $month = Carbon::create(now()->year, $month);
            }
        } else {
            $month = now();
        }

        if ($user = request()->user) {
            if (auth()->user()->cannot('executive-plan.view-any')  && auth()->user()->cannot('executive-plan.view-department')) {
                abort(403);
            }
            $user = User::find($user);


            $breadcrumbs = [
                route('dashboard') => __('breadcrumbs.dashboard.index'),
                route('users.show', [$user]) => $user->name,
                route('users.executive-plan.index', [$user]) => __('breadcrumbs.executive-plan.index'),
                route('executive-plan.show', ['year' => $month->format('Y'), 'month' => $month->format('m'), 'user' => $user]) => __('breadcrumbs.executive-plan.show', ['month' => $month->translatedFormat('F')]),
            ];
        } else {
            if (auth()->user()->cannot('executive-plan.view-self')) {
                abort(403);
            }

            $user = auth()->user();

            $breadcrumbs = [
                route('dashboard') => __('breadcrumbs.dashboard.index'),
                route('executive-plan.index') => __('breadcrumbs.executive-plan.index'),
                route('executive-plan.show') => __('breadcrumbs.executive-plan.show', ['month' => $month->translatedFormat('F')]),
            ];
        }


        $columns = ExecutivePlanColumn::where('user_id', $user->id)
            ->where('month', $month->month)
            ->where('year', $month->year)
            ->orderBy('order')
            ->get();


        $cells = $user->cells()
            ->where('date', '>=', $month->startOfMonth()->toDateString())
            ->where('date', '<=', $month->endOfMonth()->toDateString())
            ->get();


        $startDate = Carbon::create($month->year, $month->month, 1);


        $daysInMonth = $startDate->daysInMonth;

        $editAny = auth()->user()->can('executive-plan.edit-any');
        $editDepartment = auth()->user()->can('executive-plan.edit-department');
        $editSelf = auth()->user()->can('executive-plan.edit-self');

        $isEditable = (($editAny || $editDepartment) || $editSelf) && ($month->isCurrentMonth() || $month->isFuture());




        return view('executive-plan.show', [
            'breadcrumbs' => $breadcrumbs,
            'columns' => $columns,
            'cells' => $cells,
            'currentYear' => $month->year,
            'currentMonth' => $month->month,
            'startDate' => $startDate,
            'daysInMonth' => $daysInMonth,
            'hijri' => new Hijri(),
            'isEditable' => $isEditable,
            'user' => $user
        ]);
    }

    function exportToExcel($year = null, User $user = null)
    {
        ob_start();

        if (auth()->user()->cannot('executive-plan.export-any') && auth()->user()->cannot('executive-plan.export-self')  && auth()->user()->cannot('executive-plan.export-department')) {
            abort(403);
        }

        $user = $user ?? auth()->user();
        $year = $year ?? Carbon::now()->year;



        $columns = ExecutivePlanColumn::where('user_id', $user->id)
            ->orderBy('order')
            ->get();

        $spreadsheet = new Spreadsheet();

        for ($monthIndex = 1; $monthIndex <= 12; $monthIndex++) {
            // Create the month as a Carbon instance
            $month = Carbon::create($year, $monthIndex);

            // Create a new sheet for each month
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($month->translatedFormat('F-Y'));
            $sheet->freezePane('B2');
            // Set the sheet to RTL
            $sheet->setRightToLeft(true);

            // Add headers
            $sheet->setCellValue('A1', 'اليوم');
            $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('FF035944');
            $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');

            $columnIndex = 2;
            foreach ($columns as $column) {
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                $sheet->setCellValue($columnLetter . '1', $column->name);
                $sheet->getStyle($columnLetter . '1')->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle($columnLetter . '1')->getFill()->getStartColor()->setARGB('FF035944');
                $sheet->getStyle($columnLetter . '1')->getFont()->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle($columnLetter . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($columnLetter . '1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle($columnLetter . '1')->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($columnLetter . '1')->getBorders()->getLeft()->getColor()->setARGB('FF808080');
                $sheet->getStyle($columnLetter . '1')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($columnLetter . '1')->getBorders()->getRight()->getColor()->setARGB('FF808080');

                $columnIndex++;
            }

            // Start date for the month and number of days
            $startDate = Carbon::create($year, $monthIndex, 1);
            $daysInMonth = $startDate->daysInMonth;

            // Populate rows with day name, Gregorian date, and Hijri date
            $rowIndex = 2;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayName = $startDate->translatedFormat('l');
                $gregorianDate = $startDate->format('m/d');

                // Format Hijri date
                $hijriDate = Hijri::Date('m/d', $startDate->format('m/d'));

                $dayCellValue = "$dayName\n$gregorianDate\n$hijriDate";
                $sheet->setCellValue('A' . $rowIndex, $dayCellValue);

                $sheet->getStyle('A' . $rowIndex)->getAlignment()->setWrapText(true);
                $sheet->getStyle('A' . $rowIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $rowIndex)->getFill()->getStartColor()->setARGB('FF035944');
                $sheet->getStyle('A' . $rowIndex)->getFont()->getColor()->setARGB('FFFFFFFF');

                $sheet->getStyle('A' . $rowIndex)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $rowIndex)->getBorders()->getTop()->getColor()->setARGB('FF808080');
                $sheet->getStyle('A' . $rowIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $rowIndex)->getBorders()->getBottom()->getColor()->setARGB('FF808080');

                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getRowDimension($rowIndex)->setRowHeight(50);


                // Fill in columns data for each day
                $columnIndex = 2;
                foreach ($columns as $column) {
                    $cell = ExecutivePlanCell::where('date', $startDate->toDateString())
                        ->where('value', '!=', null)
                        ->where('value', '!=', '')
                        ->where('user_id', $user->id)
                        ->where('executive_plan_column_id', $column->id)
                        ->first();

                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                    $sheet->setCellValue($columnLetter . $rowIndex, $cell ? $cell->value : '');
                    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                    $sheet->getStyle($columnLetter . $rowIndex)->getAlignment()->setWrapText(true);
                    $sheet->getStyle($columnLetter . $rowIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle($columnLetter . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $columnIndex++;
                }

                $rowIndex++;
                $startDate->addDay();
            }
        }

        // Remove the initial default sheet created by PhpSpreadsheet
        $spreadsheet->removeSheetByIndex(0);
        $spreadsheet->setActiveSheetIndex(0);

        // Generate the file name and download
        $filename = str_replace(' ', '_', $user->name) . '-' . str_replace(' ', '_', $user->department->name) . "-$year.xlsx";
        $writer = new Xlsx($spreadsheet);

        // Set headers and return the file as a download response
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function clone($month, $year)
    {
        if (auth()->user()->cannot('executive-plan.edit-any') && auth()->user()->cannot('executive-plan.edit-department')) {
            abort(403);
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('executive-plan.index') => __('breadcrumbs.executive-plan.index'),
            route('executive-plan.show', ['year' => $year, 'month' => $month]) => __('breadcrumbs.executive-plan.show', ['month' => Carbon::create($year, $month)->translatedFormat('F')]),
            route('executive-plan.clone', ['year' => $year, 'month' => $month]) => __('executive-plan.clone'),
        ];

        $columns = ExecutivePlanColumn::where('user_id', auth()->id())
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $availableYears = ExecutivePlanCell::selectRaw('YEAR(date) as year')
            ->where('user_id', auth()->id())
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('year');


        return view('executive-plan.clone', [
            'columns' => $columns,
            'month' => $month,
            'year' => $year,
            'breadcrumbs' => $breadcrumbs,
            'availableYears' => $availableYears,
        ]);
    }

    function cloneStore(Request $request, $month, $year)
    {
        if (auth()->user()->cannot('executive-plan.edit-any') && auth()->user()->cannot('executive-plan.edit-department')) {
            abort(403);
        }

        $existingData =  ExecutivePlanColumn::with('cells')
            ->where('user_id', auth()->id())
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->get();

        $existingData->each(function ($column) {
            $column->cells->each(function ($cell) {
                $cell->delete();
            });
            $column->delete();
        });



        $columns = ExecutivePlanColumn::with('cells')
            ->whereIn('id', $request->columns)
            ->where('user_id', auth()->id())
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        foreach ($columns as $column) {
            $newColumn = $column->replicate()->fill([
                'month' => $request->month,
                'year' => $request->year,
            ]);
            $newColumn->save();

            $column->cells->each(function ($cell) use ($newColumn, $request) {
                $newCell = $cell->replicate()->fill([
                    'executive_plan_column_id' => $newColumn->id,
                    'date' => Carbon::create($request->year, $request->month, Carbon::create($cell->date)->day),
                ]);
                $newCell->save();

            });
        }

        return redirect()->route('executive-plan.show', ['year' => $request->year, 'month' => $request->month]);
    }

    function showCell($id)
    {
        $cell = ExecutivePlanCell::findOrFail($id);

        if (auth()->id() != $cell->user_id && auth()->user()->cannot('executive-plan.view-any') && auth()->user()->cannot('executive-plan.view-department')) {
            abort(403);
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('executive-plan.index') => __('breadcrumbs.executive-plan.index'),
            route('executive-plan.show', ['year' => $cell->column->year, 'month' => $cell->column->month]) => __('breadcrumbs.executive-plan.show', ['month' => Carbon::parse($cell->date)->translatedFormat('F')]),
            "" => $cell->value,
        ];

        return view('executive-plan.show-cell', [
            'cell' => $cell,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
    

    function editCell($id)
    {
        $cell = ExecutivePlanCell::findOrFail($id);

        if (auth()->id() != $cell->user_id && auth()->user()->cannot('executive-plan.edit-any') && auth()->user()->cannot('executive-plan.edit-department')) {
            abort(403);
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('executive-plan.index') => __('breadcrumbs.executive-plan.index'),
            route('executive-plan.show', ['year' => $cell->column->year, 'month' => $cell->column->month]) => __('breadcrumbs.executive-plan.show', ['month' => Carbon::parse($cell->date)->translatedFormat('F')]),
            route('executive-plan.cell.show', [$cell]) => $cell->value,
            route('executive-plan.cell.edit', [$cell]) => __('executive-plan.cell.edit.title'),
        ];

        return view('executive-plan.edit-cell', [
            'cell' => $cell,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
    function editCellStore(Request $request, $id)
    {
        $cell = ExecutivePlanCell::findOrFail($id);

        if (auth()->id() != $cell->user_id && auth()->user()->cannot('executive-plan.edit-any') && auth()->user()->cannot('executive-plan.edit-department')) {
            abort(403);
        }


        $cell->update([
            'value' => $request->value,
            'description' => $request->description,
        ]);

        flash()->success(__('executive-plan.cell.updated'));

        return redirect()->route('executive-plan.cell.show', [$cell]);
    }


    function migrate($user = null)
    {
        if (auth()->user()->cannot('executive-plan.migrate')) {
            abort(403);
        }

        $user = User::findOrFail($user) ?? auth()->user();

        $year = request()->year ?? now()->year;
        $month = request()->month ?? now()->month;

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.users.show', [$user]) => $user->name,
            route('executive-plan.index') => __('breadcrumbs.executive-plan.index'),
            route('users.executive-plan.migrate', ['user' => $user, 'year' => $year, 'month' => $month]) => __('breadcrumbs.executive-plan.migrate'),
        ];

        return view('executive-plan.migrate', [
            'breadcrumbs' => $breadcrumbs,
            'user' => $user,
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'users' => User::where('id', '!=', $user->id)->get(),
        ]);
    }

    function migrateStore(User $user,Request $request)
    {

        if (auth()->user()->cannot('executive-plan.migrate')) {
            abort(403);
        }

        $request->validate([
            'toUser' => 'required|exists:users,id',
            'year' => 'required|integer',
            'month' => 'required|integer',
        ]);

        $fromUser = $user;
        $toUser = User::find($request->toUser);

        if ($fromUser->id == $toUser->id) {
            flash()->error(__('executive-plan.migration_same_user'));
            return redirect()->back();
        }

        // delete toUser old data
        $existingData = ExecutivePlanColumn::with('cells')
            ->where('user_id', $toUser->id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->get();

            $columns = ExecutivePlanColumn::where('user_id', $fromUser->id)
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->get();
        if ($columns->isEmpty()) {
            flash()->error(__('executive-plan.migration_no_data'));
            return redirect()->back();
        }   

        $existingData->each(function ($column) {
            $column->cells->each(function ($cell) {
                $cell->delete();
            });
            $column->delete();
        });

        $columns->each(function ($column) use ($toUser, $request) {
            $newColumn = $column->replicate()->fill([
                'user_id' => $toUser->id,
                'month' => $request->month,
                'year' => $request->year,
            ]);
            $newColumn->save();

            $column->cells->each(function ($cell) use ($newColumn, $toUser, $request) {
                $newCell = $cell->replicate()->fill([
                    'executive_plan_column_id' => $newColumn->id,
                    'user_id' => $toUser->id,
                    'date' => Carbon::create($request->year, $request->month, Carbon::create($cell->date)->day),
                ]);
                $newCell->save();
            });
        });
        // delete fromUser columns and cells
        $columns->each(function ($column) use ($fromUser) {
            $column->cells->each(function ($cell) use ($fromUser) {
                $cell->delete();
            });
            $column->delete();
        });
        flash()->success(__('executive-plan.migration_successful'));

        return redirect()->route('users.executive-plan.index', ['user' => $user]);
    }
}
