<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position',
        'is_active',
        'department_id',
        'profile_picture',
        'shift_start',
        'shift_end',
        'work_location',
        'is_remote_worker',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    * Relationships
    */
    function department()
    {
        return $this->belongsTo(Department::class);
    }

    function evaluations()
    {
        return $this->hasMany(DailyEvaluation::class);
    }

    function evaluationScores()
    {
        return $this->hasMany(EvaluationScore::class);
    }

    function sessions()
    {
        return $this->hasMany(Session::class);
    }

    function notifications()
    {
        return $this->hasMany(Notification::class);
    }


    function cells()
    {
        return $this->hasMany(ExecutivePlanCell::class);
    }
    function messages()
    {
        return $this->hasManyThrough(Message::class, MessageRecipient::class, 'user_id', 'id', 'id', 'message_id');
    }

    function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    function tasks()
    {
        return $this->hasMany(Task::class);
    }

    function events()
    {
        return $this->hasMany(ExecutivePlanCell::class);
    }
    function unreadMessages()
    {
        return $this->messages()->unread();
    }

    public function allSubordinates()
    {
        return $this->roles->map(function ($role) {
            return $role->allSubordinates();
        })->flatten()->unique();
    }

    public function subordinate()
    {
        return Role::whereHas('superior', function ($query) {
            $query->where('superior_role_id', $this->roles()->first()->id);
        })->first();
    }

    public function superior()
    {
        return $this->roles->map(function ($role) {
            return $role->superior()->first();
        })->filter()->first();
    }

    public function superiorUser()
    {
        $superiorRole = $this->superior();
        if (!$superiorRole) {
            return null;
        }

        $query = User::whereHas('roles', function ($q) use ($superiorRole) {
            $q->where('id', $superiorRole->id);
        })->where('id', '!=', $this->id);

        // If current user is employee and superior is department-head, require same department
        if ($this->hasRole('employee') && $superiorRole->name === 'department-head') {
            if ($this->department_id) {
                $superiorInDepartment = (clone $query)->where('department_id', $this->department_id)->first();
                return $superiorInDepartment ?: null;
            }
            return null;
        }

        if ($this->department_id) {
            $superiorInDepartment = (clone $query)->where('department_id', $this->department_id)->first();
            if ($superiorInDepartment) {
                return $superiorInDepartment;
            }
        }
        return $query->first();
    }

    public function subordinateUsers()
    {
        if ($this->hasRole('department-head')) {
            return User::where('id', '!=', $this->id)
                ->where('department_id', $this->department_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'employee');
                })->get();
        }

        $subordinateRoleNames = $this->allSubordinates();
        if ($subordinateRoleNames->isEmpty()) {
            return collect();
        }
        return User::where('id', '!=', $this->id)
            ->whereHas('roles', function ($query) use ($subordinateRoleNames) {
                $query->whereIn('name', $subordinateRoleNames);
            })->get();
    }

    public function siblingUsers()
    {
        $superior = $this->superiorUser();
        if (!$superior) {
            return collect();
        }

        $query = User::where('id', '!=', $this->id)
            ->whereHas('roles', function ($q) {
                $q->whereIn('id', $this->roles->pluck('id'));
            });
        if ($this->department_id) {
            $query->where('department_id', $this->department_id);
        }
        // If current user is employee, exclude department-heads from siblings
        if ($this->hasRole('employee')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'department-head');
            });
        }
        return $query->get();
    }


    /*
    * Accessors
    */
    function getLatestActivityAttribute()
    {
        $session = $this->sessions()->orderBy('last_activity', 'desc')->first();
        return $session ? Carbon::parse($session->last_activity)->diffForHumans() : 'لا يوجد';
    }

    function getRoleAttribute()
    {
        return $this->roles()->first()?->display_name;
    }

    function getFirstnameAttribute()
    {
        return explode(' ', $this->name)[0];
    }

    function getHasUnreadNotificationsAttribute()
    {
        return $this->notifications()->unread()->count() > 0;
    }

    function getHasUnreadMessagesAttribute()
    {
        return $this->messages()->unread()->count() > 0;
    }

    function getIsOnlineAttribute()
    {
        $session = $this->sessions()->orderBy('last_activity', 'desc')->first();
        return $session && Carbon::parse($session->last_activity)->diffInMinutes() < 5;
    }


    /*
    * Methods
    */
    function sendNotification($content, $action_route = null, $action_params = null)
    {
        $this->notifications()->create([
            'content' => $content,
            'action_route' => $action_route,
            'action_params' => $action_params,
        ]);
    }

    function seenAllNotifications()
    {
        $this->notifications()->unread()->get()->each->markAsSeen();
    }


    function impersonate(User $user)
    {
        // Check if the authenticated user is not already impersonating
        if (!session()->has('impersonator_id')) {
            // Store the current user's ID to revert back later
            session(['impersonator_id' => Auth::id()]);
        }

        // Login as the given user
        Auth::login($user);

        // Redirect to the intended page as the impersonated user
        return redirect()->route('dashboard'); // Adjust the route as needed
    }

    public function stopImpersonating()
    {
        // Check if impersonation is active
        if (session()->has('impersonator_id')) {
            // Get the original user ID
            $impersonatorId = session()->pull('impersonator_id');

            // Login back as the original user
            Auth::loginUsingId($impersonatorId);
        }

        // Redirect back to the dashboard
        return redirect()->route('dashboard'); // Adjust the route as needed
    }

    public function isCheckedIn()
    {
        return $this->timesheets()->whereDate('start_at', now())->whereNull('end_at')->exists();
    }

    public function checkIn()
    {
        // A new check-in always creates a new timesheet record.
        // The isCheckedIn() method prevents this from being called if a shift is already active.
        $this->timesheets()->create([
            'start_at' => now(),
        ]);
    }

    public function checkOut()
    {
        $this->timesheets()->whereDate('start_at', now())->whereNull('end_at')->first()->update([
            'end_at' => now(),
        ]);
    }

    
    public function isEvaluatedToday()
    {
        return $this->evaluations()->whereDate('created_at', now())->exists();
    }

    /**
     * Get attendance summary for a given date.
     *
     * @param string|Carbon $date
     * @param \Illuminate\Database\Eloquent\Collection|null $timesheets
     * @return array
     */
    public function getAttendanceSummaryForDate($date, $timesheets = null, $endDate = null)
    {
        // If $endDate is provided, treat as range
        $isRange = $endDate !== null;
        $carbonStart = Carbon::parse($date)->startOfDay();
        $carbonEnd = $isRange ? Carbon::parse($endDate)->endOfDay() : $carbonStart->copy()->endOfDay();

        // Fetch tolerance settings with priority: user > department > global
        $settings = \App\Models\AttendanceSetting::where('scope_type', 'user')->where('scope_id', $this->id)->first()
            ?? \App\Models\AttendanceSetting::where('scope_type', 'department')->where('scope_id', $this->department_id)->first()
            ?? \App\Models\AttendanceSetting::where('scope_type', 'global')->first();

        $lateArrivalTolerance = $settings ? $settings->late_arrival_tolerance : 0;
        $earlyLeaveTolerance = $settings ? $settings->early_leave_tolerance : 0;

        if ($timesheets === null) {
            $timesheets = $this->timesheets()
                ->whereBetween('start_at', [$carbonStart, $carbonEnd])
                ->orderBy('start_at')
                ->get();
        }

        if ($timesheets->isEmpty()) {
            return [
                'overall_minutes' => 0,
                'present_minutes' => 0,
                'break_minutes' => 0,
                'overtime_minutes' => 0,
                'late_arrival_minutes' => 0,
                'early_leave_minutes' => 0,
            ];
        }

        $shiftStart = $this->shift_start ? Carbon::parse($carbonStart->toDateString() . ' ' . $this->shift_start) : null;
        $shiftEnd = $this->shift_end ? Carbon::parse($carbonStart->toDateString() . ' ' . $this->shift_end) : null;
        
        // Handle overnight shifts
        if ($shiftStart && $shiftEnd && $shiftEnd->lt($shiftStart)) {
            $shiftEnd->addDay();
        }

        $workSegments = [];
        $breakSegments = [];
        $presentMinutes = 0;
        $breakMinutes = 0;
        $regularMinutes = 0;
        $overtimeMinutes = 0;
        $lateArrivalMinutes = 0;
        $earlyLeaveMinutes = 0;

        // Calculate break time and collect break segments
        for ($i = 0; $i < $timesheets->count() - 1; $i++) {
            $current = $timesheets[$i];
            $next = $timesheets[$i + 1];
            
            // Skip if current timesheet is past unended
            $currentStart = Carbon::parse($current->start_at);
            if (!$current->end_at && $currentStart->lt(now()->startOfDay())) {
                continue;
            }
            
            // Skip if next timesheet is past unended
            $nextStart = Carbon::parse($next->start_at);
            if (!$next->end_at && $nextStart->lt(now()->startOfDay())) {
                continue;
            }
            
            if ($current->end_at && !$current->is_day_end) {
                $end = Carbon::parse($current->end_at);
                $start = Carbon::parse($next->start_at);
                $duration = $end->diffInMinutes($start);
                $breakMinutes += $duration;
                $breakSegments[] = [
                    'start' => $end->toDateTimeString(),
                    'end' => $start->toDateTimeString(),
                    'duration' => $duration
                ];
            }
        }

        // Robust segment-by-segment calculation and collect work segments
        foreach ($timesheets as $timesheet) {
            $start = Carbon::parse($timesheet->start_at);
            
            // Zero out past unended timesheets
            if (!$timesheet->end_at && $start->lt(now()->startOfDay())) {
                continue; // Skip this timesheet entirely
            }
            
            $end = $timesheet->end_at ? Carbon::parse($timesheet->end_at) : now();
            if ($end->lte($start)) continue;
            $segment = $start->diffInMinutes($end);
            $presentMinutes += $segment;
            $segmentRegular = 0;
            $segmentOvertime = 0;

            if ($shiftStart && $shiftEnd) {
                // Calculate overtime based on user's shift times
                // Overtime before shift
                $overtimeBefore = 0;
                if ($start->lt($shiftStart)) {
                    $beforeShiftEnd = $end->lt($shiftStart) ? $end : $shiftStart;
                    $overtimeBefore = $start->diffInMinutes($beforeShiftEnd);
                }
                // Overtime after shift
                $overtimeAfter = 0;
                if ($end->gt($shiftEnd)) {
                    $afterShiftStart = $start->gt($shiftEnd) ? $start : $shiftEnd;
                    $overtimeAfter = $afterShiftStart->diffInMinutes($end);
                }
                $segmentOvertime = $overtimeBefore + $overtimeAfter;
                $segmentRegular = $segment - $segmentOvertime;
                if ($segmentRegular < 0) $segmentRegular = 0;
            }
            $regularMinutes += $segmentRegular;
            $overtimeMinutes += $segmentOvertime;
            $workSegments[] = [
                'start' => $timesheet->start_at,
                'end' => $timesheet->end_at,
                'duration' => $segment,
                'regular' => $segmentRegular,
                'overtime' => $segmentOvertime
            ];
        }

        // Check for late arrival
        if ($shiftStart) {
            $firstCheckIn = Carbon::parse($timesheets->first()->start_at);
            if ($firstCheckIn->gt($shiftStart)) {
                $lateDiff = $firstCheckIn->diffInMinutes($shiftStart);
                if ($lateDiff > $lateArrivalTolerance) {
                    $lateArrivalMinutes = $lateDiff;
                }
            }
        }

        // Check for early leave
        if ($shiftEnd) {
            $lastTimesheetWithEnd = $timesheets->whereNotNull('end_at')->last();
            if ($lastTimesheetWithEnd && $lastTimesheetWithEnd->is_day_end) {
                $lastCheckOut = Carbon::parse($lastTimesheetWithEnd->end_at);
                if ($lastCheckOut->lt($shiftEnd)) {
                    $earlyDiff = $lastCheckOut->diffInMinutes($shiftEnd);
                    if ($earlyDiff > $earlyLeaveTolerance) {
                        $earlyLeaveMinutes = $earlyDiff;
                    }
                }
            }
        }

        // Fill in missing minutes as break (legacy/edge case fix) - only for valid timesheets
        $validTimesheets = $timesheets->filter(function($timesheet) {
            $start = Carbon::parse($timesheet->start_at);
            return $timesheet->end_at || $start->gte(now()->startOfDay());
        });
        
        if ($validTimesheets->count() > 0) {
            $firstCheckIn = Carbon::parse($validTimesheets->first()->start_at);
            $lastTimesheet = $validTimesheets->last();
            $lastCheckOut = $lastTimesheet->end_at ? Carbon::parse($lastTimesheet->end_at) : now();
            $totalSpan = $firstCheckIn->diffInMinutes($lastCheckOut);
            $accounted = $presentMinutes + $breakMinutes;
            $missing = $totalSpan - $accounted;
            if ($missing > 0) {
                $breakMinutes += $missing;
            }
        }

        return [
            'overall_minutes' => round($presentMinutes), // for top summary
            'present_minutes' => round($regularMinutes), // for expandable section
            'break_minutes' => round($breakMinutes),
            'overtime_minutes' => round($overtimeMinutes > 0 ? $overtimeMinutes : 0),
            'late_arrival_minutes' => round($lateArrivalMinutes),
            'early_leave_minutes' => round($earlyLeaveMinutes),
        ];
    }
}
