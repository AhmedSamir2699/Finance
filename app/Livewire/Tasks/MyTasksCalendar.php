<?php

namespace App\Livewire\Tasks;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Omnia\LivewireCalendar\LivewireCalendar;
use Flasher\Toastr\Prime\ToastrInterface;

class MyTasksCalendar extends LivewireCalendar
{
    public $EditEventModal = false;
    public $title;
    public $description;
    public $task_date;
    public $CreateEventModal = false;
    public $taskID;


    public function mount(
        $initialYear = null,
        $initialMonth = null,
        $weekStartsAt = null,
        $calendarView = null,
        $dayView = null,
        $eventView = null,
        $dayOfWeekView = null,
        $dragAndDropClasses = null,
        $beforeCalendarView = null,
        $afterCalendarView = null,
        $pollMillis = null,
        $pollAction = null,
        $dragAndDropEnabled = true,
        $dayClickEnabled = true,
        $eventClickEnabled = true,
        $extras = [],

    ) {

        $this->task_date = Carbon::today();
        $initialYear = $initialYear ?? Carbon::today()->year;
        $initialMonth = $initialMonth ?? Carbon::today()->month;

        $this->startsAt = Carbon::createFromDate($initialYear, $initialMonth, 1)->startOfDay();

        $this->weekStartsAt = $this->startsAt->dayOfWeek();
        $this->weekEndsAt = 4;

        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();

        $this->calculateGridStartsEnds();

        $this->setupViews($calendarView, $dayView, $eventView, $dayOfWeekView, $beforeCalendarView, $afterCalendarView);

        $this->setupPoll($pollMillis, $pollAction);

        $this->dragAndDropEnabled = $dragAndDropEnabled;
        $this->dragAndDropClasses = $dragAndDropClasses ?? 'border border-blue-400 border-4';

        $this->dayClickEnabled = $dayClickEnabled;
        $this->eventClickEnabled = $eventClickEnabled;

        $this->afterMount($extras);
    }

    public function calculateGridStartsEnds()
    {
        $this->gridStartsAt = $this->startsAt->clone()->startOfWeek($this->weekStartsAt)->shiftTimezone(config('app.timezone'));;
        $this->gridEndsAt = $this->endsAt->clone()->endOfWeek($this->weekEndsAt - 2)->shiftTimezone(config('app.timezone'));;
    }

    public function monthGrid()
    {
        $firstDayOfGrid = $this->gridStartsAt;
        $lastDayOfGrid = $this->gridEndsAt;


        $monthGrid = collect();
        $currentDay = $firstDayOfGrid->clone();

        while (!$currentDay->greaterThan($lastDayOfGrid)) {
            $monthGrid->push($currentDay->clone());
            if ($currentDay->dayOfWeek == 4) {
                $currentDay->addDays(3);
            } else {
                $currentDay->addDay();
            }
        }

        $monthGrid = $monthGrid->chunk(5);

        return $monthGrid;
    }

    public function onEventDropped($eventId, $year, $month, $day)
    {
        $task = auth()->user()->tasks()->find($eventId);
        
        $task->histories()->create([
            'user_id' => auth()->id(),
            'action' => 'إعادة جدولة المهمة من تاريخ ' . Carbon::parse($task->task_date)->format('Y-m-d') . ' إلى تاريخ ' . Carbon::createFromDate($year, $month, $day)->format('Y-m-d')
        ]);

        $task->update([
            'task_date' => Carbon::createFromDate($year, $month, $day)->startOfDay()
        ]);
        $this->dispatch('refreshTasks');

    }

    public function onEventClick($eventId)
    {
        
        $task = auth()->user()->tasks()->find($eventId);
        if(!$task) return;
        $this->taskID = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        
        $this->EditEventModal = true;
        
    }

    public function getTasks()
    {
        return auth()->user()->tasks()
            ->whereDate('task_date', '>=', $this->startsAt->startOfMonth()->startOfDay())
            ->whereDate('task_date', '<=', $this->startsAt->endOfMonth()->endOfDay())->get();
    }

    public function events(): Collection
    {

        $tasks = $this->getTasks();

        return $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'priority' => $task->priority,
                'date' => $task->task_date,
            ];
        });
    }

    // edit
    public function update($id)
    {
        $task = auth()->user()->tasks()->find($id);
        if(!$task) return;

        $task->update([
            'title' => $this->title,
            'description' => $this->description,
        ]);

                    flash()->success(__('tasks.update_success'));

        $this->EditEventModal = false;

    }

    function onDayClick($year, $month, $day)
    {
        $this->title = '';
        $this->description = '';
        $this->task_date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
        $this->CreateEventModal = true;
    }

    public function create()
    {
 
        auth()->user()->tasks()->create([
            'title' => $this->title,
            'description' => $this->description,
            'task_date' => Carbon::parse($this->task_date)->startOfDay()
        ]);

        $this->title = '';
        $this->description = '';
        $this->task_date = '';
        $this->CreateEventModal = false;

                    flash()->success(__('tasks.create_success'));

        $this->dispatch('refreshTasks');

    }

    public function render()
    {
        $events = $this->events();

        return view($this->calendarView)
            ->with([
                'componentId' => $this->getId(),
                'monthGrid' => $this->monthGrid(),
                'events' => $events,
                'getEventsForDay' => function ($day) use ($events) {
                    return $this->getEventsForDay($day, $events);
                },
                'EditEventModal'=>$this->EditEventModal
            ]);
    }
}
