<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;


class TimesheetController extends Controller
{
    function index()
    {
        return redirect()->route('timesheets.show', ['date' => now()->format('Y-m-d')]);
    }

    function show($date = null, Request $request)
    {
        if (!$date) {
            $date = now()->format('Y-m-d');
        }

        $filter = $request->input('filter');

        $breadcrumbs = [
            route('dashboard') => __('sidebar.overview'),
            route('timesheets.index') => __('sidebar.timesheets'),
            route('timesheets.show', [$date]) => __('breadcrumbs.timesheets.show') . ' ' . $date,
        ];

        // Eager load timesheets for the given date, sorted by start time
        $usersQuery = User::with(['timesheets' => function ($query) use ($date) {
            $query->whereDate('start_at', $date)->orderBy('start_at', 'asc');
        }]);

        if ($filter == 'present') {
            $usersQuery->whereHas('timesheets', function ($query) use ($date) {
                $query->whereDate('start_at', $date);
            });
        } elseif ($filter == 'absent') {
            $usersQuery->whereDoesntHave('timesheets', function ($query) use ($date) {
                $query->whereDate('start_at', $date);
            });
        }

        $users = $usersQuery->get();

        foreach ($users as $user) {
            $timesheetsToday = $user->timesheets;
            $summary = $user->getAttendanceSummaryForDate($date);

            $user->first_start_at = null;
            $user->last_end_at = null;
            $user->is_last_shift_active = false;
            $user->last_timesheet_id = null;
            $user->formatted_total_time = '0';

            if ($timesheetsToday->isNotEmpty()) {
                $firstTimesheet = $timesheetsToday->first();
                $user->first_start_at = Carbon::parse($firstTimesheet->start_at)->format('H:i:s');
                $lastTimesheet = $timesheetsToday->last();

                if ($lastTimesheet->end_at) {
                    $user->last_end_at = Carbon::parse($lastTimesheet->end_at)->format('H:i:s');
                    $totalMinutes = Carbon::parse($firstTimesheet->start_at)->diffInMinutes(Carbon::parse($lastTimesheet->end_at));
                } else {
                    $user->is_last_shift_active = true;
                    $user->last_timesheet_id = $lastTimesheet->id;
                    $totalMinutes = Carbon::parse($firstTimesheet->start_at)->diffInMinutes(now());
                }

                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $user->formatted_total_time = sprintf('%d ساعة، %d دقيقة', $hours, $minutes);
            }

            // Format present time from summary
            $presentHours = floor($summary['present_minutes'] / 60);
            $presentMins = $summary['present_minutes'] % 60;
            $user->formatted_present_time = sprintf('%d ساعة، %d دقيقة', $presentHours, $presentMins);

            // Format break time from summary
            $breakHours = floor($summary['break_minutes'] / 60);
            $breakMins = $summary['break_minutes'] % 60;
            $user->formatted_break_time = sprintf('%d ساعة، %d دقيقة', $breakHours, $breakMins);

            // Format overtime, late arrival, and early leave
            $overtimeHours = floor($summary['overtime_minutes'] / 60);
            $overtimeMins = $summary['overtime_minutes'] % 60;
            $user->formatted_overtime = sprintf('%d ساعة، %d دقيقة', $overtimeHours, $overtimeMins);

            $lateArrivalHours = floor($summary['late_arrival_minutes'] / 60);
            $lateArrivalMins = $summary['late_arrival_minutes'] % 60;
            $user->formatted_late_arrival = sprintf('%d ساعة، %d دقيقة', $lateArrivalHours, $lateArrivalMins);

            $earlyLeaveHours = floor($summary['early_leave_minutes'] / 60);
            $earlyLeaveMins = $summary['early_leave_minutes'] % 60;
            $user->formatted_early_leave = sprintf('%d ساعة، %d دقيقة', $earlyLeaveHours, $earlyLeaveMins);
        }

        $departments = Department::all();
        return view('timesheets.show', compact('breadcrumbs', 'users', 'date', 'departments', 'filter'));
    }

    function create()
    {
        $breadcrumbs = [
            route('dashboard') => __('sidebar.overview'),
            route('timesheets.index') => __('sidebar.timesheets'),
            route('timesheets.create') => __('sidebar.timesheets.create'),
        ];

        return view('timesheets.create', compact('breadcrumbs'));
    }

    function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i',
        ]);

        $timesheet = new Timesheet();
        $timesheet->date = $request->date;
        $timesheet->time_in = $request->time_in;
        $timesheet->time_out = $request->time_out;
        $timesheet->user_id = auth()->id();
        $timesheet->save();

        flash()->success(__('timesheets.create.success'));
        return redirect()->route('timesheets.index');
    }

    function edit(Timesheet $timesheet)
    {
        $breadcrumbs = [
            route('dashboard') => __('sidebar.overview'),
            route('timesheets.index') => __('sidebar.timesheets'),
            route('timesheets.edit', [$timesheet]) => __('breadcrumbs.timesheets.edit'),
        ];

        return view('timesheets.edit', compact('timesheet', 'breadcrumbs'));
    }

    function update(Request $request, Timesheet $timesheet)
    {
        $request->validate([
            'date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i',
        ]);

        $timesheet->date = $request->date;
        $timesheet->time_in = $request->time_in;
        $timesheet->time_out = $request->time_out;
        $timesheet->save();

        flash()->success(__('timesheets.edit.success'));
        return redirect()->route('timesheets.index');
    }

    function export(Request $request)
    {
        $date = Carbon::parse($request->date);
        $isMonth = strlen($request->date) === 7;

        $query = User::query()
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->select('users.*') // Select all from users table to correctly hydrate User models
            ->orderBy('departments.name', 'asc')
            ->orderBy('users.name', 'asc');

        if ($request->filled('department_id') && $request->department_id !== 'all') {
            $query->where('users.department_id', $request->department_id);
        }

        $withs = ['department']; // Eager load department relationship
        if ($isMonth) {
            $withs['timesheets'] = function ($q) use ($date) {
                $q->whereYear('start_at', $date->year)->whereMonth('start_at', $date->month);
            };
        } else {
            $withs['timesheets'] = function ($q) use ($date) {
                $q->whereDate('start_at', $date->toDateString());
            };
        }
        
        $users = $query->with($withs)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('timesheets.export.sheet_name', ['date' => $date->format($isMonth ? 'Y-m' : 'Y-m-d')]));
        $sheet->setRightToLeft(true);

        $headers = [
            __('timesheets.export.employee'),
            __('timesheets.export.department'),
            __('timesheets.export.shift_start'),
            __('timesheets.export.shift_end'),
            __('timesheets.export.check_in'),
            __('timesheets.export.check_out'),
            __('timesheets.export.total_overall'), // NEW COLUMN
            __('timesheets.export.present_time'),
            __('timesheets.export.break_time'),
            __('timesheets.export.overtime'),
            __('timesheets.export.late_arrival'),
            __('timesheets.export.early_leave'),
            __('timesheets.export.status'),
        ];
        if($isMonth) {
            array_unshift($headers, __('Day'));
        }
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDDDDDD']],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
        
        $row = 2;
        $days = $isMonth ? $date->daysInMonth : 1;

        for ($d = 1; $d <= $days; $d++) {
            $currentDate = $isMonth ? $date->copy()->day($d) : $date;

            foreach ($users as $user) {
                $userTimesheets = $user->timesheets->filter(function ($t) use ($currentDate) {
                    return Carbon::parse($t->start_at)->isSameDay($currentDate);
                })->sortBy('start_at');

                $summary = $user->getAttendanceSummaryForDate($currentDate, $userTimesheets);

                $shiftStart = $user->shift_start ? Carbon::parse($user->shift_start)->format('H:i') : '--';
                $shiftEnd = $user->shift_end ? Carbon::parse($user->shift_end)->format('H:i') : '--';

                $firstCheckIn = $userTimesheets->isNotEmpty() ? Carbon::parse($userTimesheets->first()->start_at) : null;
                $lastCheckOut = $userTimesheets->whereNotNull('end_at')->isNotEmpty() ? Carbon::parse($userTimesheets->whereNotNull('end_at')->last()->end_at) : null;

                $status = $userTimesheets->isNotEmpty() ? __('timesheets.export.present') : __('timesheets.export.absent');

                $formatMinutes = fn($mins) => $mins > 0 ? floor($mins / 60) . 'h ' . ($mins % 60) . 'm' : '--';
                $formatTime = fn($carbon) => $carbon ? $carbon->format('H:i') : '--';

                // Calculate total overall (first check-in to last check-out)
                $totalOverall = ($firstCheckIn && $lastCheckOut) ? $firstCheckIn->diffInMinutes($lastCheckOut) : null;

                $rowData = [
                    $user->name,
                    $user->department->name ?? '--',
                    $shiftStart,
                    $shiftEnd,
                    $formatTime($firstCheckIn),
                    $formatTime($lastCheckOut),
                    $formatMinutes($totalOverall), // NEW COLUMN
                    $formatMinutes($summary['present_minutes']),
                    $formatMinutes($summary['break_minutes']),
                    $formatMinutes($summary['overtime_minutes']),
                    $formatMinutes($summary['late_arrival_minutes']),
                    $formatMinutes($summary['early_leave_minutes']),
                    $status,
                ];

                if($isMonth) {
                    array_unshift($rowData, $currentDate->format('D, d M'));
                }
                
                $sheet->fromArray($rowData, null, 'A' . $row);
                $row++;
            }
            if ($isMonth && $d < $days) {
                 $row++;
            }
        }
        
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $filename = 'timesheets-' . $date->format($isMonth ? 'Y-m' : 'Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    function endshift(Timesheet $timesheet)
    {
        if (auth()->user()->cannot('timesheet.edit')) {
            flash()->error(__('timesheets.endshift.permission_error'));
        return redirect()->back();
        }

        $timesheet->update([
            'end_at' => now(),
        ]);

        flash()->success(__('timesheets.endshift.success'));
        return redirect()->back();
    }

    public function unendedTimesheets()
    {
        if (auth()->user()->cannot('timesheet.edit')) {
            flash()->error(__('timesheets.unended.permission_error'));
        return redirect()->back();
        }

        $breadcrumbs = [
            route('dashboard') => __('sidebar.overview'),
            route('timesheets.index') => __('sidebar.timesheets'),
            route('timesheets.unended') => __('timesheets.unended.fix_title'),
        ];

        return view('timesheets.unended', compact('breadcrumbs'));
    }


   
}
