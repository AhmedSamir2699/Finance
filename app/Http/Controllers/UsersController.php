<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Dompdf\Dompdf;
use Dompdf\Options;
use ArPHP\I18N\Arabic;

class UsersController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.users.index') => __('breadcrumbs.users.index')
        ];

        $usersCount = User::count();
        return view('manage.users.index', compact('usersCount', 'breadcrumbs'));
    }

    public function create()
    {
        return view('manage.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required',
            'department_id' => 'required',
        ]);

        User::create($request->all());

        return redirect()->route('manage.users.index');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.users.index') => __('breadcrumbs.users.index'),
            route('manage.users.edit', [$user]) => __('breadcrumbs.users.edit'),
        ];

        $departments = Department::all();
        return view('manage.users.edit', compact('user', 'departments', 'breadcrumbs'));
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'position' => 'required',
            'phone' => 'required',
            'department_id' => 'required',
            'is_active' => 'required',
            'profile_picture' => 'nullable|image|max:5120',
            'shift_start' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'shift_end' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'work_location' => 'nullable|string|max:255',
            'is_remote_worker' => 'nullable|boolean',
        ]);

        $data = $request->all();

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            try {
                // Delete old profile picture if exists
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                
                $file = $request->file('profile_picture');
                $path = $file->store('profile_pictures', 'public');
                $data['profile_picture'] = $path;
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['profile_picture' => 'Error uploading image: ' . $e->getMessage()]);
            }
        }

        $user->update($data);

        flash()->success(__('users.update.success'));
        return redirect()->route('manage.users.index');
    }

    function timesheet(User $user)
    {
        $totalMinutes = $user
            ->timesheets()
            ->whereDate('start_at', now())
            ->get()
            ->map(function ($timesheet) {
                $start = Carbon::parse($timesheet->start_at);

                $end = $timesheet->end_at ? Carbon::parse($timesheet->end_at) : now();

                return $start->diffInMinutes($end);
            })
            ->sum();
        $roundedMinutes = round($totalMinutes);
        if ($roundedMinutes >= 60) {
            $hours = intdiv($roundedMinutes, 60); // Calculate hours
            $minutes = $roundedMinutes % 60; // Remaining minutes

            if ($minutes > 0) {
                $formattedTime = trans_choice('users.timesheet.hours_and_minutes', $hours, [
                    'hours' => trans_choice('users.timesheet.hours', $hours, ['count' => $hours]),
                    'minutes' => trans_choice('users.timesheet.minutes', $minutes, [
                        'count' => $minutes,
                    ]),
                ]);
            } else {
                $formattedTime = trans_choice('users.timesheet.hours', $hours, ['count' => $hours]);
            }
        } elseif ($roundedMinutes > 0) {
            $formattedTime = trans_choice('users.timesheet.minutes', $roundedMinutes, [
                'count' => $roundedMinutes,
            ]);
        } else {
            $formattedTime = __('users.timesheet.no_time_logged');
        }

        return view('users.timesheet', compact('user', 'formattedTime'));
    }

    public function reportsIndex()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.users.index') => __('breadcrumbs.users.index'),
            route('users.reports.index') => __('breadcrumbs.users.reports')
        ];

        return view('users.reports.index', compact('breadcrumbs'));
    }

    public function customDateReportData(User $user, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Timesheets in range
        $timesheets = $user->timesheets()
            ->whereBetween('start_at', [$start, $end])
            ->get();

        // Get evaluation scores in range
        $evaluationScores = $user->evaluationScores()
            ->with('criteria')
            ->whereBetween('evaluated_at', [$start, $end])
            ->get();

        // Get appearance average for backward compatibility
        $appearanceCriteria = \App\Models\EvaluationCriteria::where('name', 'Appearance')->first();
        $appearanceAverage = 0;
        $appearanceRecords = collect();
        
        if ($appearanceCriteria) {
            $appearanceRecords = $evaluationScores->where('criteria_id', $appearanceCriteria->id);
            $appearanceAverage = $appearanceRecords->avg('score') ?? 0;
        }

        // Calculate overall evaluation average
        $overallEvaluationAverage = $evaluationScores->avg('score') ?? 0;

        // Attendance calculations using the new method
        $attendanceSummary = $user->getAttendanceSummaryForDate($start->toDateString(), null, $end->toDateString());
        
        // Calculate total working days in range (excluding weekends)
        $period = CarbonPeriod::create($start, $end, 1);
        $workingDays = 0;
        foreach ($period as $date) {
            // Skip weekends (Friday = 5, Saturday = 6)
            if ($date->dayOfWeek !== 5 && $date->dayOfWeek !== 6) {
                $workingDays++;
            }
        }
        
        // Calculate expected minutes based on user's shift data
        $expectedMinutes = 0;
        if ($user->shift_start && $user->shift_end) {
            $shiftStart = Carbon::parse($user->shift_start);
            $shiftEnd = Carbon::parse($user->shift_end);
            $dailyMinutes = $shiftStart->diffInMinutes($shiftEnd);
            $expectedMinutes = $workingDays * $dailyMinutes;
        } else {
            // Fallback to default 8 hours if no shift data
            $expectedMinutes = $workingDays * 8 * 60;
        }
        $attendancePercent = $expectedMinutes > 0 ? ($attendanceSummary['overall_minutes'] / $expectedMinutes) * 100 : 0;

        // Tasks in range
        $tasksQuery = $user->tasks()->whereBetween('task_date', [$start, $end]);
        $totalEstimatedTime = $tasksQuery->sum('estimated_time');
        $totalActualTime = $tasksQuery->sum('actual_time');
        $tasks = $tasksQuery->get();
        $timeQuality = $totalActualTime == 0 ? 0 : (max($totalEstimatedTime, 1) / max($totalActualTime, 1)) * 100;
        $taskCount = $tasks->count();
        $completedTasks = $user->tasks()->whereBetween('task_date', [$start, $end])->where('status', 'approved');
        $completedTaskCount = $completedTasks->count();
        
        $taskQualityPercentage = ($completedTaskCount > 0) ?
            $completedTasks->sum('quality_percentage') / $completedTaskCount * $completedTaskCount
            : 0;
        $taskCompletionPercentage = $taskCount > 0
            ? ($completedTaskCount / $taskCount) * 100
            : 0;

        // Categorize tasks
        $taskCategories = [
            'scheduled' => $tasks->where('type', 'scheduled'),
            'unscheduled' => $tasks->where('type', 'unscheduled'),
            'training' => $tasks->where('type', 'training'),
            'continous' => $tasks->where('type', 'continous'),
        ];

        // Calculate detailed attendance data for each date
        $detailedAttendance = $this->calculateDetailedAttendance($user, $start, $end);

        // Get all evaluation criteria and calculate percentages
        $evaluationCriteria = \App\Models\EvaluationCriteria::where('is_active', true)->get();
        $evaluationPercentages = [];
        foreach ($evaluationCriteria as $criteria) {
            $criteriaScores = $evaluationScores->where('criteria_id', $criteria->id);
            $averageScore = $criteriaScores->avg('score') ?? 0;
            $percentage = ($averageScore / $criteria->max_value) * 100;
            $evaluationPercentages[$criteria->id] = [
                'name' => $criteria->name,
                'percentage' => number_format($percentage, 2),
                'average_score' => number_format($averageScore, 2),
                'max_value' => $criteria->max_value,
                'records_count' => $criteriaScores->count()
            ];
        }

        // ===============================
        // حساب التقييم النهائي للموظف:
        // التقييم النهائي = (مجموع جميع النسب: الحضور + معايير التقييم) ÷ عدد العناصر
        //
        // حيث:
        // - نسبة الحضور ($attendancePercent): نسبة حضور الموظف خلال الفترة (من 0 إلى 100).
        // - نسب معايير التقييم ($evaluationPercentages): مصفوفة نسب جميع معايير التقييم النشطة.
        // - عدد العناصر ($finalScoreCount): عدد جميع العناصر الداخلة في الحساب (الحضور + عدد معايير التقييم).
        //
        // النتيجة النهائية تظهر كنسبة مئوية (من 0 إلى 100).
        // ===============================
        
        // Flatten evaluation percentages for final score calculation
        $evaluationPercentagesFlat = array_column($evaluationPercentages, 'percentage');
        
        $finalScoreItems = [
            'attendance' => $attendancePercent,
            'taskCompletion' => $taskCompletionPercentage,
        ];
        
        // Add evaluation percentages as individual items
        foreach ($evaluationPercentagesFlat as $percentage) {
            $finalScoreItems[] = $percentage;
        }
        
        $finalScoreCount = count($finalScoreItems);
        $finalScoreSum = array_sum($finalScoreItems);
        $finalScoreRaw = $finalScoreCount > 0 ? $finalScoreSum / $finalScoreCount : 0;

        return [
            'data' => [
                'attendance' => [
                    'present' => number_format($attendancePercent, 2) . '%',
                    'absent' => number_format(100 - $attendancePercent, 2) . '%',
                    'total' => number_format($attendanceSummary['overall_minutes']),
                    'expected_minutes' => $expectedMinutes,
                    'present_minutes' => $attendanceSummary['overall_minutes'],
                    'regular_minutes' => $attendanceSummary['present_minutes'],
                    'overtime_minutes' => $attendanceSummary['overtime_minutes'],
                    'break_minutes' => $attendanceSummary['break_minutes'],
                ],
                'tasks' => [
                    'requiredTime' => number_format($totalEstimatedTime),
                    'actualTime' => number_format($totalActualTime),
                    'timeQuality' => number_format($timeQuality, 2) . '%',
                    'taskQuality' => number_format($taskQualityPercentage, 2),
                    'taskCompletion' => number_format($taskCompletionPercentage, 2),
                ],
                'attendanceRecords' => count($timesheets),


  

                'finalScore' => number_format($finalScoreRaw, 2),

            ],
            'taskCategories' => $taskCategories,
            'evaluationCriteria' => $evaluationPercentages,
            'evaluationScores' => $evaluationScores,
            'attendanceRecords' => $timesheets,
            'totalPresentMinutes' => $attendanceSummary['overall_minutes'],
            'dateRange' => [$start->toDateString(), $end->toDateString()],
            'detailedAttendance' => $detailedAttendance,
        ];
    }

    /**
     * Calculate detailed attendance data for each date in the range
     */
    private function calculateDetailedAttendance(User $user, Carbon $start, Carbon $end)
    {
        $period = CarbonPeriod::create($start, $end);
        $detailedAttendance = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $attendanceSummary = $user->getAttendanceSummaryForDate($dateString);
            
            // Handle both cases: with and without timesheets
            $totalMinutes = $attendanceSummary['overall_minutes'] ?? 0;
            $regularMinutes = $attendanceSummary['present_minutes'] ?? 0;
            $overtimeMinutes = $attendanceSummary['overtime_minutes'] ?? 0;
            $breakMinutes = $attendanceSummary['break_minutes'] ?? 0;
            
            // Calculate totals
            $totalHours = floor($totalMinutes / 60);
            $totalMins = $totalMinutes % 60;
            $regularHours = floor($regularMinutes / 60);
            $regularMins = $regularMinutes % 60;
            $overtimeHours = floor($overtimeMinutes / 60);
            $overtimeMins = $overtimeMinutes % 60;
            $breakHours = floor($breakMinutes / 60);
            $breakMins = $breakMinutes % 60;
            
            // Get timesheets for this date
            $dayTimesheets = $user->timesheets()->whereDate('start_at', $dateString)->orderBy('start_at')->get();
            
            // Format timesheet details
            $timesheetDetails = [];
            foreach ($dayTimesheets as $timesheet) {
                $startTime = Carbon::parse($timesheet->start_at);
                $endTime = $timesheet->end_at ? Carbon::parse($timesheet->end_at) : null;
                
                // For past unended timesheets, show the timesheet but with zero duration
                if (!$timesheet->end_at && $startTime->lt(now()->startOfDay())) {
                    $timesheetDetails[] = [
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => null,
                        'duration' => '0س 0د',
                        'is_active' => false,
                    ];
                    continue;
                }
                
                if ($endTime) {
                    $hours = floor($startTime->diffInMinutes($endTime) / 60);
                    $minutes = $startTime->diffInMinutes($endTime) % 60;
                    $duration = $hours . 'س ' . $minutes . 'د';
                } else {
                    $hours = floor($startTime->diffInMinutes(now()) / 60);
                    $minutes = $startTime->diffInMinutes(now()) % 60;
                    $duration = $hours . 'س ' . $minutes . 'د (جاري)';
                }
                
                $timesheetDetails[] = [
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime ? $endTime->format('H:i:s') : null,
                    'duration' => $duration,
                    'is_active' => !$endTime,
                ];
            }
            
            $detailedAttendance[$dateString] = [
                'date' => $dateString,
                'total_minutes' => $totalMinutes,
                'regular_minutes' => $regularMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'break_minutes' => $breakMinutes,
                'total_hours' => $totalHours,
                'total_mins' => $totalMins,
                'regular_hours' => $regularHours,
                'regular_mins' => $regularMins,
                'overtime_hours' => $overtimeHours,
                'overtime_mins' => $overtimeMins,
                'break_hours' => $breakHours,
                'break_mins' => $breakMins,
                'total_sum' => $regularMinutes + $overtimeMinutes + $breakMinutes,
                'total_sum_hours' => floor(($regularMinutes + $overtimeMinutes + $breakMinutes) / 60),
                'total_sum_mins' => ($regularMinutes + $overtimeMinutes + $breakMinutes) % 60,
                'timesheets' => $timesheetDetails,
                'has_timesheets' => $dayTimesheets->isNotEmpty(),
            ];
        }
        
        return $detailedAttendance;
    }

    public function customDateReport(User $user)
    {
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        $start = $startDate ? Carbon::parse($startDate) : now()->startOfDay();
        $end = $endDate ? Carbon::parse($endDate) : $start;

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.users.index') => __('breadcrumbs.users.index'),
            route('users.show', [$user]) => $user->name,
            route('users.reports.show', [$user, $start->format('Y-m-d'), $end->format('Y-m-d')]) => __('breadcrumbs.users.report'),
        ];

        $data = $this->customDateReportData($user, $start, $end);


        return view(
            'users.report',
            compact(
                'breadcrumbs',
                'user',
                'data',
                'start',
                'end'
            )
        );
    }

    public function userSummaryReport(User $user)
    {
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        
        // Default to current month if no dates provided
        if (!$startDate && !$endDate) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        } else {
            $start = $startDate ? Carbon::parse($startDate) : now()->startOfDay();
            $end = $endDate ? Carbon::parse($endDate) : $start;
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.users.index') => __('breadcrumbs.users.index'),
            route('users.show', [$user]) => $user->name,
            route('users.summary.show', [$user, $start->format('Y-m-d'), $end->format('Y-m-d')]) => __('breadcrumbs.users.summary'),
        ];

        // Get the data from existing function
        $reportData = $this->customDateReportData($user, $start, $end);
        
        // Recalculate working days for summary display
        $period = CarbonPeriod::create($start, $end, 1);
        $workingDays = 0;
        foreach ($period as $date) {
            // Skip weekends (Friday = 5, Saturday = 6)
            if ($date->dayOfWeek !== 5 && $date->dayOfWeek !== 6) {
                $workingDays++;
            }
        }
        
        // Get evaluation scores in range
        $evaluationScores = $user->evaluationScores()
            ->with('criteria')
            ->whereBetween('evaluated_at', [$start, $end])
            ->get();
        
        // Get all evaluation criteria
        $evaluationCriteria = \App\Models\EvaluationCriteria::where('is_active', true)->get();
        
        // Calculate evaluation percentages for each criteria
        $evaluationPercentages = [];
        $evaluationPercentagesList = [];
        foreach ($evaluationCriteria as $criteria) {
            $criteriaScores = $evaluationScores->where('criteria_id', $criteria->id);
            $averageScore = $criteriaScores->avg('score') ?? 0;
            $percentage = ($averageScore / $criteria->max_value) * 100;
            $evaluationPercentages[$criteria->id] = [
                'name' => $criteria->name,
                'percentage' => number_format($percentage, 2),
                'average_score' => number_format($averageScore, 2),
                'max_value' => $criteria->max_value
            ];
            $evaluationPercentagesList[] = $percentage;
        }

        // Calculate combined evaluation percentage
        $combinedEvaluationPercentage = count($evaluationPercentagesList) > 0 ? array_sum($evaluationPercentagesList) / count($evaluationPercentagesList) : 0;
        // Extract attendance percentage from the present field
        $attendancePercentage = (float) str_replace('%', '', $reportData['data']['attendance']['present']);
        
        // Extract time quality percentage from the timeQuality field
        $timeQualityPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['timeQuality']);

        $taskQualityPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['taskQuality']);
        $taskCompletionPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['taskCompletion']);

        // Calculate presence percentage (days present vs total working days)
        $period = CarbonPeriod::create($start, $end, 1);
        $workingDays = 0;
        foreach ($period as $date) {
            // Skip weekends (Friday = 5, Saturday = 6)
            if ($date->dayOfWeek !== 5 && $date->dayOfWeek !== 6) {
                $workingDays++;
            }
        }
        $timesheets = $reportData['attendanceRecords'];
        $presentDays = $timesheets->groupBy(function($timesheet) {
            return Carbon::parse($timesheet->start_at)->format('Y-m-d');
        })->count();
        $presencePercentage = $workingDays > 0 ? ($presentDays / $workingDays) * 100 : 0;
        
        // Calculate discipline percentage as (attendance percentage + presence percentage) / 2
        $disciplinePercentage = ($attendancePercentage + $presencePercentage) / 2;

        // Calculate final total percentage
        $allPercentages = [
            $disciplinePercentage,
            $timeQualityPercentage,
            $taskCompletionPercentage,
            $taskQualityPercentage,
        ];
        
        foreach ($evaluationPercentages as $percentage) {
            $allPercentages[] = $percentage['percentage'];
        }
        
        $finalTotal = count($allPercentages) > 0 ? array_sum($allPercentages) / count($allPercentages) : 0;


        $summaryData = [
            'user' => $user,
            'dateRange' => [
                'start' => $start,
                'end' => $end
            ],
            'discipline' => [
                'required_time' => $reportData['data']['attendance']['expected_minutes'],
                'actual_time' => $reportData['data']['attendance']['present_minutes'],
                'percentage' => $disciplinePercentage
            ],
            'timeQuality' => number_format($timeQualityPercentage, 2),
            'taskCompletion' => number_format($taskCompletionPercentage, 2),
            'taskQuality' => number_format($taskQualityPercentage, 2),
            'evaluationCriteria' => $evaluationPercentages,
            'finalTotal' => number_format($finalTotal, 2)
        ];

        return view(
            'users.summary-report',
            compact(
                'breadcrumbs',
                'user',
                'summaryData',
                'start',
                'end'
            )
        );
    }

    public function mySummaryReport()
    {
        $user = auth()->user();
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        
        // Default to current month if no dates provided
        if (!$startDate && !$endDate) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        } else {
            $start = $startDate ? Carbon::parse($startDate) : now()->startOfDay();
            $end = $endDate ? Carbon::parse($endDate) : $start;
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('profile.edit') => __('users.profile_info'),
            route('users.summary.my', ['start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d')]) => __('users.summary.title_short'),
        ];

        // Get the data from existing function
        $reportData = $this->customDateReportData($user, $start, $end);
        
        // Recalculate working days for summary display
        $period = CarbonPeriod::create($start, $end, 1);
        $workingDays = 0;
        foreach ($period as $date) {
            // Skip weekends (Friday = 5, Saturday = 6)
            if ($date->dayOfWeek !== 5 && $date->dayOfWeek !== 6) {
                $workingDays++;
            }
        }
        
        // Get evaluation scores in range
        $evaluationScores = $user->evaluationScores()
            ->with('criteria')
            ->whereBetween('evaluated_at', [$start, $end])
            ->get();
        
        // Get all evaluation criteria
        $evaluationCriteria = \App\Models\EvaluationCriteria::where('is_active', true)->get();
        
        // Calculate evaluation percentages for each criteria
        $evaluationPercentages = [];
        $evaluationPercentagesList = [];
        foreach ($evaluationCriteria as $criteria) {
            $criteriaScores = $evaluationScores->where('criteria_id', $criteria->id);
            $averageScore = $criteriaScores->avg('score') ?? 0;
            $percentage = ($averageScore / $criteria->max_value) * 100;
            $evaluationPercentages[$criteria->id] = [
                'name' => $criteria->name,
                'percentage' => number_format($percentage, 2),
                'average_score' => number_format($averageScore, 2),
                'max_value' => $criteria->max_value
            ];
            $evaluationPercentagesList[] = $percentage;
        }

        // Calculate combined evaluation percentage
        $combinedEvaluationPercentage = count($evaluationPercentagesList) > 0 ? array_sum($evaluationPercentagesList) / count($evaluationPercentagesList) : 0;
        // Extract attendance percentage from the present field
        $attendancePercentage = (float) str_replace('%', '', $reportData['data']['attendance']['present']);
        
        // Extract time quality percentage from the timeQuality field
        $timeQualityPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['timeQuality']);

        $taskQualityPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['taskQuality']);
        $taskCompletionPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['taskCompletion']);

        // Calculate presence percentage (days present vs total working days)
        $period = CarbonPeriod::create($start, $end, 1);
        $workingDays = 0;
        foreach ($period as $date) {
            // Skip weekends (Friday = 5, Saturday = 6)
            if ($date->dayOfWeek !== 5 && $date->dayOfWeek !== 6) {
                $workingDays++;
            }
        }
        $timesheets = $reportData['attendanceRecords'];
        $presentDays = $timesheets->groupBy(function($timesheet) {
            return Carbon::parse($timesheet->start_at)->format('Y-m-d');
        })->count();
        $presencePercentage = $workingDays > 0 ? ($presentDays / $workingDays) * 100 : 0;
        
        // Calculate discipline percentage as (attendance percentage + presence percentage) / 2
        $disciplinePercentage = ($attendancePercentage + $presencePercentage) / 2;

        // Calculate final total percentage
        $allPercentages = [
            $disciplinePercentage,
            $timeQualityPercentage,
            $taskCompletionPercentage,
            $taskQualityPercentage,
        ];
        
        foreach ($evaluationPercentages as $percentage) {
            $allPercentages[] = $percentage['percentage'];
        }
        
        $finalTotal = count($allPercentages) > 0 ? array_sum($allPercentages) / count($allPercentages) : 0;


        $summaryData = [
            'user' => $user,
            'dateRange' => [
                'start' => $start,
                'end' => $end
            ],
            'discipline' => [
                'required_time' => $reportData['data']['attendance']['expected_minutes'],
                'actual_time' => $reportData['data']['attendance']['present_minutes'],
                'percentage' => $disciplinePercentage
            ],
            'timeQuality' => number_format($timeQualityPercentage, 2),
            'taskCompletion' => number_format($taskCompletionPercentage, 2),
            'taskQuality' => number_format($taskQualityPercentage, 2),
            'evaluationCriteria' => $evaluationPercentages,
            'finalTotal' => number_format($finalTotal, 2)
        ];

        return view(
            'users.summary-report',
            compact(
                'breadcrumbs',
                'user',
                'summaryData',
                'start',
                'end'
            )
        );
    }

    public function mySummaryReportPdf()
    {
        $user = auth()->user();
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        
        // Default to current month if no dates provided
        if (!$startDate && !$endDate) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        } else {
            $start = $startDate ? Carbon::parse($startDate) : now()->startOfDay();
            $end = $endDate ? Carbon::parse($endDate) : $start;
        }

        // Get the data from existing function
        $reportData = $this->customDateReportData($user, $start, $end);
        
        // Recalculate working days for summary display
        $period = CarbonPeriod::create($start, $end, 1);
        $workingDays = 0;
        foreach ($period as $date) {
            // Skip weekends (Friday = 5, Saturday = 6)
            if ($date->dayOfWeek !== 5 && $date->dayOfWeek !== 6) {
                $workingDays++;
            }
        }
        
        // Get evaluation scores in range
        $evaluationScores = $user->evaluationScores()
            ->with('criteria')
            ->whereBetween('evaluated_at', [$start, $end])
            ->get();
        
        // Get all evaluation criteria
        $evaluationCriteria = \App\Models\EvaluationCriteria::where('is_active', true)->get();
        
        // Calculate evaluation percentages for each criteria
        $evaluationPercentages = [];
        $evaluationPercentagesList = [];
        foreach ($evaluationCriteria as $criteria) {
            $criteriaScores = $evaluationScores->where('criteria_id', $criteria->id);
            $averageScore = $criteriaScores->avg('score') ?? 0;
            $percentage = ($averageScore / $criteria->max_value) * 100;
            $evaluationPercentages[$criteria->id] = [
                'name' => $criteria->name,
                'percentage' => number_format($percentage, 2),
                'average_score' => number_format($averageScore, 2),
                'max_value' => $criteria->max_value
            ];
            $evaluationPercentagesList[] = $percentage;
        }

        // Calculate combined evaluation percentage
        $combinedEvaluationPercentage = count($evaluationPercentagesList) > 0 ? array_sum($evaluationPercentagesList) / count($evaluationPercentagesList) : 0;
        // Extract attendance percentage from the present field
        $attendancePercentage = (float) str_replace('%', '', $reportData['data']['attendance']['present']);
        
        // Extract time quality percentage from the timeQuality field
        $timeQualityPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['timeQuality']);

        $taskQualityPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['taskQuality']);
        $taskCompletionPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['taskCompletion']);

        // Calculate presence percentage (days present vs total working days)
        $period = CarbonPeriod::create($start, $end, 1);
        $workingDays = 0;
        foreach ($period as $date) {
            // Skip weekends (Friday = 5, Saturday = 6)
            if ($date->dayOfWeek !== 5 && $date->dayOfWeek !== 6) {
                $workingDays++;
            }
        }
        $timesheets = $reportData['attendanceRecords'];
        $presentDays = $timesheets->groupBy(function($timesheet) {
            return Carbon::parse($timesheet->start_at)->format('Y-m-d');
        })->count();
        $presencePercentage = $workingDays > 0 ? ($presentDays / $workingDays) * 100 : 0;
        
        // Calculate discipline percentage as (attendance percentage + presence percentage) / 2
        $disciplinePercentage = ($attendancePercentage + $presencePercentage) / 2;

        // Calculate final total percentage
        $allPercentages = [
            $disciplinePercentage,
            $timeQualityPercentage,
            $taskCompletionPercentage,
            $taskQualityPercentage,
        ];
        
        // Add evaluation percentages as individual items
        foreach ($evaluationPercentages as $percentage) {
            $allPercentages[] = (float) $percentage['percentage'];
        }
        
        $finalTotal = count($allPercentages) > 0 ? array_sum($allPercentages) / count($allPercentages) : 0;

        $summaryData = [
            'workingDays' => $workingDays,
            'discipline' => [
                'requiredTime' => $reportData['data']['attendance']['expected_minutes'],
                'actualTime' => $reportData['data']['attendance']['present_minutes'],
                'percentage' => number_format($disciplinePercentage, 2)
            ],
            'timeQuality' => number_format($timeQualityPercentage, 2),
            'taskCompletion' => number_format($taskCompletionPercentage, 2),
            'taskQuality' => number_format($taskQualityPercentage, 2),
            'evaluationCriteria' => $evaluationPercentages,
            'finalTotal' => number_format($finalTotal, 2)
        ];

        // Generate PDF using Blade view
        $html = view('users.summary-report-pdf', compact('user', 'summaryData', 'start', 'end'))->render();
        
        // Process Arabic text using Ar-PHP library
        $arabic = new \ArPHP\I18N\Arabic();
        $arabic->setNumberFormat(1);
        $arabic->setDateMode(1);
        
        // Process the HTML content
        $p = $arabic->arIdentify($html);
        
        if (is_array($p) && count($p) > 0) {
            for ($i = count($p)-1; $i >= 0; $i-=2) {
                if (isset($p[$i-1]) && isset($p[$i])) {
                    $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i-1], $p[$i] - $p[$i-1]));
                    $html = substr_replace($html, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
                }
            }
        }
        
        // Use DomPDF to generate PDF
        $dompdf = new \Dompdf\Dompdf();
        
        // Configure DomPDF with basic settings
        $options = new \Dompdf\Options([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
            'enableCssFloat' => true,
            'enableJavascript' => false,
            'enablePhp' => false,
            'enableRemote' => false,
            'defaultMediaType' => 'screen',
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
        ]);
        
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'summary-report-' . $user->name . '-' . $start->format('Y-m-d') . '-' . $end->format('Y-m-d') . '.pdf';
        
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function generalSummaryIndex()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('users.summary.general.index') => __('breadcrumbs.users.summary_general'),
        ];

        $departments = \App\Models\Department::orderBy('name')->get();

        return view('users.general-summary-index', compact('breadcrumbs', 'departments'));
    }

    public function generalSummaryUsers($departmentId)
    {
        $department = \App\Models\Department::findOrFail($departmentId);
        
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('users.summary.general.index') => __('breadcrumbs.users.summary_general'),
            route('users.summary.general.department', $department->id) => $department->name,
        ];

        $users = $department->users()->orderBy('name')->get();

        return view('users.general-summary-users', compact('breadcrumbs', 'department', 'users'));
    }

    public function exportCustomDateReport(User $user)
    {
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $reportData = $this->customDateReportData($user, $start, $end);

        $spreadsheet = new Spreadsheet();

        // Common styles
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD8E4BC']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        ];
        $sectionHeaderStyle = [
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2DDDD']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        ];
        $labelStyle = [
            'font' => ['bold' => true, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        ];
        $valueStyle = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        ];
        $defaultCellStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFFFF'], // White fill
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF'], // White border (invisible)
                ],
            ],
        ];
        // --- Summary Sheet ---
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->getStyle('A1:G30')->applyFromArray($defaultCellStyle);
        $summarySheet->setTitle('نظرة عامة');
        $summarySheet->setRightToLeft(true);

        // Set column widths for summary sheet
        foreach (range('A', 'G') as $col) {
            $summarySheet->getColumnDimension($col)->setWidth(20);
        }

        // Header info
        $summarySheet->setCellValue('A1', 'اسم الموظف');
        $summarySheet->setCellValue('B1', 'القسم');
        $summarySheet->setCellValue('C1', 'تاريخ البداية');
        $summarySheet->setCellValue('D1', 'تاريخ النهاية');
        $summarySheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        $summarySheet->setCellValue('A2', $user->name);
        $summarySheet->setCellValue('B2', $user->department->name ?? 'N/A');
        $summarySheet->setCellValue('C2', $start->toDateString());
        $summarySheet->setCellValue('D2', $end->toDateString());
        $summarySheet->getStyle('A2:D2')->applyFromArray($valueStyle);

        // Final score
        $summarySheet->setCellValue('A4', 'التقييم النهائي');
        $summarySheet->mergeCells('A4:G4');
        $summarySheet->getStyle('A4:G4')->applyFromArray($sectionHeaderStyle);

        $summarySheet->setCellValue('A5', 'النسبة المئوية');
        $summarySheet->setCellValue('B5', $reportData['data']['finalScore'] . '%');
        $summarySheet->getStyle('A5')->applyFromArray($labelStyle);
        $summarySheet->getStyle('B5')->applyFromArray($valueStyle);

        // Attendance summary
        $summarySheet->setCellValue('A7', 'تقييم الانضباط');
        $summarySheet->mergeCells('A7:G7');
        $summarySheet->getStyle('A7:G7')->applyFromArray($sectionHeaderStyle);

        $summarySheet->setCellValue('A8', 'المعايير والتقييم');
        $summarySheet->setCellValue('B8', 'النسبة المئوية');
        $summarySheet->getStyle('A8:B8')->applyFromArray($headerStyle);
        
        // Calculate attendance totals from detailed data
        $totalRegularMinutes = 0;
        $totalOvertimeMinutes = 0;
        $totalBreakMinutes = 0;
        
        foreach ($reportData['detailedAttendance'] as $attendanceData) {
            $totalRegularMinutes += $attendanceData['regular_minutes'];
            $totalOvertimeMinutes += $attendanceData['overtime_minutes'];
            $totalBreakMinutes += $attendanceData['break_minutes'];
        }
        
        // Calculate expected minutes per day based on user's shift settings
        $expectedMinutesPerDay = 0;
        if ($user->shift_start && $user->shift_end) {
            $shiftStart = Carbon::parse($user->shift_start);
            $shiftEnd = Carbon::parse($user->shift_end);
            
            // Handle overnight shifts
            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }
            
            $expectedMinutesPerDay = $shiftStart->diffInMinutes($shiftEnd);
        } else {
            // Fallback to default 8 hours if no shift data
            $expectedMinutesPerDay = 8 * 60;
        }
        
        // Calculate total expected minutes for the entire period
        $totalExpectedMinutes = $expectedMinutesPerDay * count($reportData['detailedAttendance']);
        
        $totalPresentMinutes = $totalRegularMinutes + $totalOvertimeMinutes + $totalBreakMinutes;
        $attendancePercentage = $totalExpectedMinutes > 0 ? number_format(($totalPresentMinutes / $totalExpectedMinutes) * 100, 2) : 0;
        
        // Format time values
        $totalRegularHours = floor($totalRegularMinutes / 60);
        $totalRegularMins = $totalRegularMinutes % 60;
        $totalOvertimeHours = floor($totalOvertimeMinutes / 60);
        $totalOvertimeMins = $totalOvertimeMinutes % 60;
        $totalBreakHours = floor($totalBreakMinutes / 60);
        $totalBreakMins = $totalBreakMinutes % 60;
        $totalPresentHours = floor($totalPresentMinutes / 60);
        $totalPresentMins = $totalPresentMinutes % 60;
        
        $summarySheet->setCellValue('A9', 'العمل المنتظم');
        $summarySheet->setCellValue('B9', $totalRegularHours . 'س ' . $totalRegularMins . 'د');
        
        $summarySheet->setCellValue('A10', 'الوقت الإضافي');
        $summarySheet->setCellValue('B10', $totalOvertimeHours . 'س ' . $totalOvertimeMins . 'د');
        
        $summarySheet->setCellValue('A11', 'وقت الراحة');
        $summarySheet->setCellValue('B11', $totalBreakHours . 'س ' . $totalBreakMins . 'د');
        
        $summarySheet->setCellValue('A12', 'إجمالي الحضور');
        $summarySheet->setCellValue('B12', $totalPresentHours . 'س ' . $totalPresentMins . 'د');
        
        $summarySheet->setCellValue('A13', 'الوقت المطلوب');
        $summarySheet->setCellValue('B13', floor($totalExpectedMinutes / 60) . 'س ' . ($totalExpectedMinutes % 60) . 'د');
        
        $summarySheet->setCellValue('A14', 'النسبة المئوية');
        $summarySheet->setCellValue('B14', $attendancePercentage . '%');
        
        $summarySheet->getStyle('A9:A14')->applyFromArray($labelStyle);
        $summarySheet->getStyle('B9:B14')->applyFromArray($valueStyle);

        // Task summary
        $summarySheet->setCellValue('A16', 'تقييم كفاءة استثمار الوقت');
        $summarySheet->mergeCells('A16:G16');
        $summarySheet->getStyle('A16:G16')->applyFromArray($sectionHeaderStyle);

        $summarySheet->setCellValue('A17', 'المعايير والتقييم');
        $summarySheet->setCellValue('B17', 'الوقت المطلوب');
        $summarySheet->setCellValue('C17', 'الوقت المستثمر');
        $summarySheet->setCellValue('D17', 'النسبة المئوية');
        $summarySheet->getStyle('A17:D17')->applyFromArray($headerStyle);

        $summarySheet->setCellValue('A18', 'كفاءة استثمار وقت العمل');
        $summarySheet->setCellValue('B18', $reportData['data']['tasks']['requiredTime']);
        $summarySheet->setCellValue('C18', $reportData['data']['tasks']['actualTime']);
        $summarySheet->setCellValue('D18', $reportData['data']['tasks']['timeQuality']);
        $summarySheet->getStyle('A18')->applyFromArray($labelStyle);
        $summarySheet->getStyle('B18:D18')->applyFromArray($valueStyle);


        // Evaluation summary
        if (count($reportData['evaluationCriteria']) > 0) {
            $summarySheet->setCellValue('A24', 'تقييم المعايير');
            $summarySheet->mergeCells('A24:G24');
            $summarySheet->getStyle('A24:G24')->applyFromArray($sectionHeaderStyle);

            $summarySheet->setCellValue('A25', 'المعيار');
            $summarySheet->setCellValue('B25', 'متوسط التقييم');
            $summarySheet->setCellValue('C25', 'النسبة المئوية');
            $summarySheet->setCellValue('D25', 'عدد السجلات');
            $summarySheet->getStyle('A25:D25')->applyFromArray($headerStyle);

            $row = 26;
            foreach ($reportData['evaluationCriteria'] as $criteria) {
                $summarySheet->setCellValue("A{$row}", $criteria['name']);
                $summarySheet->setCellValue("B{$row}", $criteria['average_score'] . '/' . $criteria['max_value']);
                $summarySheet->setCellValue("C{$row}", $criteria['percentage'] . '%');
                $summarySheet->setCellValue("D{$row}", $criteria['records_count']);
                $summarySheet->getStyle("A{$row}")->applyFromArray($labelStyle);
                $summarySheet->getStyle("B{$row}:D{$row}")->applyFromArray($valueStyle);
                $row++;
            }
        }

        // --- Attendance Details Sheet ---
        $attendanceSheet = $spreadsheet->createSheet();
        $attendanceSheet->getStyle('A1:I100')->applyFromArray($defaultCellStyle);
        $attendanceSheet->setTitle('تفاصيل الحضور');
        $attendanceSheet->setRightToLeft(true);
        $attendanceSheet->getColumnDimension('A')->setWidth(20);
        $attendanceSheet->getColumnDimension('B')->setWidth(15);
        $attendanceSheet->getColumnDimension('C')->setWidth(15);
        $attendanceSheet->getColumnDimension('D')->setWidth(15);
        $attendanceSheet->getColumnDimension('E')->setWidth(15);
        $attendanceSheet->getColumnDimension('F')->setWidth(15);
        $attendanceSheet->getColumnDimension('G')->setWidth(15);
        $attendanceSheet->getColumnDimension('H')->setWidth(15);
        $attendanceSheet->getColumnDimension('I')->setWidth(15);

        $attendanceSheet->setCellValue('A1', 'التاريخ');
        $attendanceSheet->setCellValue('B1', 'العمل المنتظم');
        $attendanceSheet->setCellValue('C1', 'الوقت الإضافي');
        $attendanceSheet->setCellValue('D1', 'وقت الراحة');
        $attendanceSheet->setCellValue('E1', 'الإجمالي');
        $attendanceSheet->setCellValue('F1', 'الوقت المطلوب');
        $attendanceSheet->setCellValue('G1', 'النسبة المئوية');
        $attendanceSheet->setCellValue('H1', 'وقت البداية');
        $attendanceSheet->setCellValue('I1', 'وقت النهاية');
        $attendanceSheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Calculate expected time from user's shift settings
        $expectedMinutes = 0;
        if ($user->shift_start && $user->shift_end) {
            $shiftStart = Carbon::parse($user->shift_start);
            $shiftEnd = Carbon::parse($user->shift_end);
            
            // Handle overnight shifts
            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }
            
            $expectedMinutes = $shiftStart->diffInMinutes($shiftEnd);
        } else {
            // Default to 8 hours if no shift settings
            $expectedMinutes = 480;
        }

        $row = 2;
        $totalPresentMinutes = 0;
        $totalExpectedMinutes = 0;
        
        foreach ($reportData['detailedAttendance'] as $date => $attendanceData) {
            $attendanceSheet->setCellValue("A{$row}", $date);
            
            // Regular work
            $regularTime = $attendanceData['regular_hours'] . ':' . str_pad($attendanceData['regular_mins'], 2, '0', STR_PAD_LEFT);
            $attendanceSheet->setCellValue("B{$row}", $regularTime);
            
            // Overtime
            $overtimeTime = $attendanceData['overtime_hours'] . ':' . str_pad($attendanceData['overtime_mins'], 2, '0', STR_PAD_LEFT);
            $attendanceSheet->setCellValue("C{$row}", $overtimeTime);
            
            // Break time
            $breakTime = $attendanceData['break_hours'] . ':' . str_pad($attendanceData['break_mins'], 2, '0', STR_PAD_LEFT);
            $attendanceSheet->setCellValue("D{$row}", $breakTime);
            
            // Total
            $totalTime = $attendanceData['total_sum_hours'] . ':' . str_pad($attendanceData['total_sum_mins'], 2, '0', STR_PAD_LEFT);
            $attendanceSheet->setCellValue("E{$row}", $totalTime);
            
            // Expected time
            $expectedHours = floor($expectedMinutes / 60);
            $expectedMins = $expectedMinutes % 60;
            $expectedTime = $expectedHours . ':' . str_pad($expectedMins, 2, '0', STR_PAD_LEFT);
            $attendanceSheet->setCellValue("F{$row}", $expectedTime);
            
            // Percentage
            $percentage = $attendanceData['total_minutes'] > 0 ? number_format(($attendanceData['total_minutes'] / $expectedMinutes) * 100, 2) . '%' : '0%';
            $attendanceSheet->setCellValue("G{$row}", $percentage);
            
            // Timesheet details (first timesheet of the day)
            if ($attendanceData['has_timesheets'] && !empty($attendanceData['timesheets'])) {
                $firstTimesheet = $attendanceData['timesheets'][0];
                $attendanceSheet->setCellValue("H{$row}", $firstTimesheet['start_time']);
                $attendanceSheet->setCellValue("I{$row}", $firstTimesheet['end_time'] ?? '');
            } else {
                $attendanceSheet->setCellValue("H{$row}", '');
                $attendanceSheet->setCellValue("I{$row}", '');
                // Highlight row in red for no attendance
                $attendanceSheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFC7CE');
            }

            $attendanceSheet->getStyle("A{$row}:I{$row}")->applyFromArray($valueStyle);
            $totalPresentMinutes += $attendanceData['total_minutes'];
            $totalExpectedMinutes += $expectedMinutes;
            $row++;
        }

        // --- Tasks Details Sheets by Category ---
        foreach ($reportData['taskCategories'] as $category => $tasks) {
            $taskSheet = $spreadsheet->createSheet();
            $taskSheet->getStyle('A1:F100')->applyFromArray($defaultCellStyle);
            $taskSheet->setTitle("مهام - " . __('tasks.type.' . lcfirst($category)));
            $taskSheet->setRightToLeft(true);

            $taskSheet->getColumnDimension('A')->setWidth(10); // ID or index
            $taskSheet->getColumnDimension('B')->setWidth(30); // Task name or description
            $taskSheet->getColumnDimension('C')->setWidth(15); // Date
            $taskSheet->getColumnDimension('D')->setWidth(15); // Estimated time
            $taskSheet->getColumnDimension('E')->setWidth(15); // Actual time
            $taskSheet->getColumnDimension('F')->setWidth(15); // Status or type

            // Header row
            $taskSheet->setCellValue('A1', 'م'); // Index
            $taskSheet->setCellValue('B1', 'الوصف');
            $taskSheet->setCellValue('C1', 'تاريخ المهمة');
            $taskSheet->setCellValue('D1', 'الوقت المقدر');
            $taskSheet->setCellValue('E1', 'الوقت المستثمر');
            $taskSheet->setCellValue('F1', 'نوع المهمة');
            $taskSheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            $row = 2;
            foreach ($tasks as $index => $task) {
                $taskSheet->setCellValue("A{$row}", $index + 1);
                $taskSheet->setCellValue("B{$row}", $task->description ?? 'N/A');
                $taskSheet->setCellValue("C{$row}", Carbon::parse($task->task_date)->toDateString());
                $taskSheet->setCellValue("D{$row}", $task->estimated_time ?? 0);
                $taskSheet->setCellValue("E{$row}", $task->actual_time ?? 0);
                $taskSheet->setCellValue("F{$row}", __('tasks.type.' . lcfirst($category)));
                $taskSheet->getStyle("A{$row}:F{$row}")->applyFromArray($valueStyle);
                $row++;
            }
        }

        // --- Evaluation Details Sheet ---
        if (count($reportData['evaluationCriteria']) > 0) {
            $evaluationSheet = $spreadsheet->createSheet();
            $evaluationSheet->getStyle('A1:E100')->applyFromArray($defaultCellStyle);
            $evaluationSheet->setTitle('تفاصيل التقييم');
            $evaluationSheet->setRightToLeft(true);
            
            $evaluationSheet->getColumnDimension('A')->setWidth(15); // Date
            $evaluationSheet->getColumnDimension('B')->setWidth(25); // Criteria
            $evaluationSheet->getColumnDimension('C')->setWidth(15); // Score
            $evaluationSheet->getColumnDimension('D')->setWidth(15); // Percentage
            $evaluationSheet->getColumnDimension('E')->setWidth(20); // Max Value

            $evaluationSheet->setCellValue('A1', 'التاريخ');
            $evaluationSheet->setCellValue('B1', 'المعيار');
            $evaluationSheet->setCellValue('C1', 'الدرجة');
            $evaluationSheet->setCellValue('D1', 'النسبة المئوية');
            $evaluationSheet->setCellValue('E1', 'الدرجة القصوى');
            $evaluationSheet->getStyle('A1:E1')->applyFromArray($headerStyle);

            $row = 2;
            foreach ($reportData['evaluationScores'] as $score) {
                $evaluationSheet->setCellValue("A{$row}", Carbon::parse($score->evaluated_at)->format('Y-m-d'));
                $evaluationSheet->setCellValue("B{$row}", $score->criteria->name ?? 'N/A');
                $evaluationSheet->setCellValue("C{$row}", $score->score);
                $evaluationSheet->setCellValue("D{$row}", $score->criteria ? number_format(($score->score / $score->criteria->max_value) * 100, 2) . '%' : 'N/A');
                $evaluationSheet->setCellValue("E{$row}", $score->criteria->max_value ?? 'N/A');
                $evaluationSheet->getStyle("A{$row}:E{$row}")->applyFromArray($valueStyle);
                $row++;
            }
        }

        // --- Appearance Records Sheet ---
        // $appearanceSheet = $spreadsheet->createSheet();
        // $appearanceSheet->setTitle('تقييم المظهر');
        // $appearanceSheet->setRightToLeft(true);

        // $appearanceSheet->getColumnDimension('A')->setWidth(10);
        // $appearanceSheet->getColumnDimension('B')->setWidth(25);
        // $appearanceSheet->getColumnDimension('C')->setWidth(15);

        // $appearanceSheet->setCellValue('A1', 'م');
        // $appearanceSheet->setCellValue('B1', 'تاريخ التقييم');
        // $appearanceSheet->setCellValue('C1', 'درجة المظهر');
        // $appearanceSheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // $row = 2;
        // foreach ($reportData['appearance'] as $index => $record) {
        //     $appearanceSheet->setCellValue("A{$row}", $index + 1);
        //     $appearanceSheet->setCellValue("B{$row}", Carbon::parse($record->created_at)->toDateString());
        //     $appearanceSheet->setCellValue("C{$row}", $record->appearance ?? 0);
        //     $appearanceSheet->getStyle("A{$row}:C{$row}")->applyFromArray($valueStyle);
        //     $row++;
        // }



        // Set active sheet back to summary before output
        $spreadsheet->setActiveSheetIndex(0);

        $filename = "تقرير_الموظف_{$user->name}_من_{$start->format('Y-m-d')}_الى_{$end->format('Y-m-d')}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportSummaryToPdf(User $user)
    {
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        $start = $startDate ? Carbon::parse($startDate) : now()->startOfDay();
        $end = $endDate ? Carbon::parse($endDate) : $start;

        // Get the data from existing function
        $reportData = $this->customDateReportData($user, $start, $end);
        
        // Recalculate working days for summary display
        $period = CarbonPeriod::create($start, $end, 1);
        $workingDays = 0;
        foreach ($period as $date) {
            // Skip weekends (Friday = 5, Saturday = 6)
            if ($date->dayOfWeek !== 5 && $date->dayOfWeek !== 6) {
                $workingDays++;
            }
        }
        
        // Get evaluation scores in range
        $evaluationScores = $user->evaluationScores()
            ->with('criteria')
            ->whereBetween('evaluated_at', [$start, $end])
            ->get();
        
        // Get all evaluation criteria
        $evaluationCriteria = \App\Models\EvaluationCriteria::where('is_active', true)->get();
        
        // Calculate evaluation percentages for each criteria
        $evaluationPercentages = [];
        $evaluationPercentagesList = [];
        foreach ($evaluationCriteria as $criteria) {
            $criteriaScores = $evaluationScores->where('criteria_id', $criteria->id);
            $averageScore = $criteriaScores->avg('score') ?? 0;
            $percentage = ($averageScore / $criteria->max_value) * 100;
            $evaluationPercentages[$criteria->id] = [
                'name' => $criteria->name,
                'percentage' => number_format($percentage, 2),
                'average_score' => number_format($averageScore, 2),
            ];
            $evaluationPercentagesList[] = $percentage;
        }

        // Calculate combined evaluation percentage
        $combinedEvaluationPercentage = count($evaluationPercentagesList) > 0 ? array_sum($evaluationPercentagesList) / count($evaluationPercentagesList) : 0;

        // Extract attendance percentage from the present field
        $attendancePercentage = (float) str_replace('%', '', $reportData['data']['attendance']['present']);
        
        // Extract time quality percentage from the timeQuality field
        $timeQualityPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['timeQuality']);

        // Extract task completion and quality percentages
        $taskCompletionPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['taskCompletion']);
        $taskQualityPercentage = (float) str_replace(['%', ','], '', $reportData['data']['tasks']['taskQuality']);

        // Calculate presence percentage (days present vs total working days)
        $timesheets = $reportData['attendanceRecords'];
        $presentDays = $timesheets->groupBy(function($timesheet) {
            return Carbon::parse($timesheet->start_at)->format('Y-m-d');
        })->count();
        $presencePercentage = $workingDays > 0 ? ($presentDays / $workingDays) * 100 : 0;
        
        // Calculate discipline percentage as (attendance percentage + presence percentage) / 2
        $disciplinePercentage = ($attendancePercentage + $presencePercentage) / 2;

        // Calculate final total percentage
        $allPercentages = [
            $disciplinePercentage,
            $timeQualityPercentage,
            $taskCompletionPercentage,
            $taskQualityPercentage,
        ];
        
        // Add evaluation percentages as individual items
        foreach ($evaluationPercentages as $percentage) {
            $allPercentages[] = (float) $percentage['percentage'];
        }
        
        $finalTotal = count($allPercentages) > 0 ? array_sum($allPercentages) / count($allPercentages) : 0;

        $summaryData = [
            'workingDays' => $workingDays,
            'discipline' => [
                'requiredTime' => $reportData['data']['attendance']['expected_minutes'],
                'actualTime' => $reportData['data']['attendance']['present_minutes'],
                'percentage' => number_format($disciplinePercentage, 2)
            ],
            'timeQuality' => number_format($timeQualityPercentage, 2),
            'taskCompletion' => number_format($taskCompletionPercentage, 2),
            'taskQuality' => number_format($taskQualityPercentage, 2),
            'evaluationCriteria' => $evaluationPercentages,
            'finalTotal' => number_format($finalTotal, 2)
        ];

        // Generate PDF using Blade view
        $html = view('users.summary-report-pdf', compact('user', 'summaryData', 'start', 'end'))->render();
        
        // Process Arabic text using Ar-PHP library
        $arabic = new \ArPHP\I18N\Arabic();
        $arabic->setNumberFormat(1);
        $arabic->setDateMode(1);
        
        // Process the HTML content
        $p = $arabic->arIdentify($html);
        
        if (is_array($p) && count($p) > 0) {
            for ($i = count($p)-1; $i >= 0; $i-=2) {
                if (isset($p[$i-1]) && isset($p[$i])) {
                    $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i-1], $p[$i] - $p[$i-1]));
                    $html = substr_replace($html, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
                }
            }
        }
        
        // Use DomPDF to generate PDF
        $dompdf = new \Dompdf\Dompdf();
        
        // Configure DomPDF with basic settings
        $options = new \Dompdf\Options([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
            'enableCssFloat' => true,
            'enableJavascript' => false,
            'enablePhp' => false,
            'enableRemote' => false,
            'defaultMediaType' => 'screen',
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
        ]);
        
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'summary-report-' . $user->name . '-' . $start->format('Y-m-d') . '-' . $end->format('Y-m-d') . '.pdf';
        
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }


    function destroy(User $user)
    {
        $user->delete();
        flash()->success(__('manage.users.delete.success'));
        return redirect()->route('manage.users.index');
    }
}
