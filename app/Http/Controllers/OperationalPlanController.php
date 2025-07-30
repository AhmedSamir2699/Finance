<?php

namespace App\Http\Controllers;

use App\Models\OperationalPlan;
use App\Models\OperationalPlanProgram;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OperationalPlanController extends Controller
{
    public function importExcel($id, Request $request)
    {
        $operationalPlan = OperationalPlan::findOrFail($id);
        set_time_limit(0);
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathName());

        $sheetsData = [];

        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            $sheet = $spreadsheet->getSheet($sheetIndex);
            $csvPath = storage_path('app/tmp_' . Str::random(10) . '.csv');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setSheetIndex($sheetIndex);
            $writer->save($csvPath);

            $handle = fopen($csvPath, 'r');
            if (!$handle) {
                @unlink($csvPath);
                continue;
            }

            $headers = fgetcsv($handle);
            $headers = array_map('trim', $headers);
            $headers = array_filter($headers, fn($h) => !is_null($h) && $h !== '');
            $headers = array_values($headers);

            $columnsCount = count($headers);

            $current = array_fill(0, $columnsCount, null);
            $hierarchy = [];

            while (($row = fgetcsv($handle)) !== false) {
                $row = array_slice($row, 0, $columnsCount);

                $rowData = [];
                foreach ($headers as $i => $header) {
                    $value = $row[$i] ?? null;
                    if (empty($value) && isset($current[$i]) && !in_array($header, ['البنود', 'الأنشطة الرئيسية'])) {
                        $value = $current[$i];
                    } else {
                        $current[$i] = $value;
                    }
                    $rowData[$header] = $value;
                }

                if (count(array_filter($rowData, fn($v) => trim($v) !== '')) === 0) {
                    continue;
                }

                $goal    = trim($rowData['الهدف الاستراتيجي'] ?? 'N/A');
                $program = trim($rowData['البرنامج'] ?? '');
                $subProg = trim($rowData['البرامج الفرعية'] ?? '');
                $subProg = $subProg !== '' ? $subProg : '__empty__';


                if (!isset($hierarchy[$goal])) {
                    $hierarchy[$goal] = [];
                }
                if (!isset($hierarchy[$goal][$program])) {
                    $hierarchy[$goal][$program] = [];
                }
                if (!isset($hierarchy[$goal][$program][$subProg])) {
                    $hierarchy[$goal][$program][$subProg] = [];
                }

                // Add Item
                if (isset($rowData['البنود']) && trim($rowData['البنود']) !== '') {
                    $hierarchy[$goal][$program][$subProg][] = [
                        'type' => 'item',
                        'البند' => $rowData['البنود'],
                        'العدد' => $rowData['العدد'] ?? 0,
                        'التكلفة الفردية' => $rowData['التكلفة الفردية'] ?? 0,
                    ];
                }

                // Add Activity + Monthly data
                if (!empty($rowData['الأنشطة الرئيسية'])) {
                    $activity = [
                        'type' => 'activity',
                        'النشاط الرئيسي' => $rowData['الأنشطة الرئيسية'],
                        'المستهدف' => $rowData['المستهدف'] ?? 0,
                        'months' => []
                    ];
                    for ($i = 0; $i < 12; $i++) {
                        $csvIndex = 9 + $i; // Columns J to U
                        $raw = $row[$csvIndex] ?? '';
                        $value = is_numeric(trim($raw)) ? (int)trim($raw) : 0;
                        $activity['months'][$i + 1] = $value;
                    }

                    $hierarchy[$goal][$program][$subProg][] = $activity;
                }
            }


            fclose($handle);
            @unlink($csvPath);

            if (!empty($hierarchy)) {
                $sheetsData[$sheetName] = $hierarchy;
            }

            /**
             * Insert the data into the database
             */

            $department = $operationalPlan->departments()->firstOrCreate([
                'title' => $sheetName,
                'description' => '',
            ]);

            foreach ($hierarchy as $goal => $programs) {
                $strategicGoal = $department->strategicGoals()->firstOrCreate([
                    'title' => $goal,
                    'operational_plan_id' => $operationalPlan->id,
                ]);

                foreach ($programs as $program => $subPrograms) {
                    $operationalPlanProgram = $strategicGoal->programs()->firstOrCreate([
                        'title' => $program,
                        'operational_plan_department_id' => $department->id,
                    ]);

                    foreach ($subPrograms as $subProgram => $items) {
                        $subProgramModel = $operationalPlanProgram->subPrograms()->firstOrCreate([
                            'title' => $subProgram,
                        ]);

                        foreach ($items as $item) {
                            if ($item['type'] === 'item') {
                                // Create Item
                                $subProgramModel->items()->create([
                                    'title' => $item['البند'],
                                    'quantity' => (int)$item['العدد'],
                                    'unit_cost' => (float)$item['التكلفة الفردية'],
                                    'total_cost' => (int)$item['العدد'] * (float)$item['التكلفة الفردية'],
                                    'operational_plan_sub_program_id' => $subProgramModel->id,
                                ]);
                            } elseif ($item['type'] === 'activity') {
                                // Create Activity
                                $activity = $subProgramModel->activities()->create([
                                    'title' => $item['النشاط الرئيسي'],
                                    'yearly_target' => (int)$item['المستهدف'],
                                ]);

                                // Add Monthly Data
                                foreach ($item['months'] as $month => $value) {
                                    if ($value > 0) {
                                        for ($i = 1; $i <= $value; $i++) {
                                            $activity->logs()->create([
                                                'completed_at' => Carbon::createFromDate(null, $month, 1),
                                                'notes' => '',
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        flash()->success(__('operational-plan.import.success', ['count' => count($sheetsData)]));
        return redirect()->route('operational-plan.show', $operationalPlan->id);
    }


    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('operational-plan.index') => __('breadcrumbs.operational-plans.index'),
        ];

        // Fetch operational plans from the database
        $operationalPlans = OperationalPlan::paginate();
        return view('operational-plans.index', [
            'breadcrumbs' => $breadcrumbs,
            'operationalPlans' => $operationalPlans,
        ]);
    }

    public function create()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('operational-plan.index') => __('breadcrumbs.operational-plans.index'),
            route('operational-plan.create') => __('breadcrumbs.operational-plans.create'),
        ];

        return view('operational-plans.create', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period' => 'string|max:255',
        ]);

        $operationalPlan = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'period' => $request->input('period'),
            'is_public' => $request->input('is_public') ? 1 : 0,
        ];
        OperationalPlan::create($operationalPlan);

        flash()->success(__('operational-plan.create.success'));
        return redirect()->route('operational-plan.index');
    }

    public function show($id)
    {
        // Fetch the operational plan from the database
        $operationalPlan = OperationalPlan::findOrFail($id);

        // Fetch related data if needed
        $operationalPlan->load('departments', 'summaryPrograms');
        $operationalPlan->load('summaryPrograms.strategicGoal');

        $operationalPlan->load('departments.strategicGoals');
        $operationalPlan->load('departments.programs');
        $operationalPlan->load('departments.programs.strategicGoal');
        $operationalPlan->load('departments.programs.subPrograms');

        // breadcrumbs
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('operational-plan.index') => __('breadcrumbs.operational-plans.index'),
            route('operational-plan.show', $operationalPlan->id) => $operationalPlan->title,
        ];
        return view('operational-plans.show', [
            'breadcrumbs' => $breadcrumbs,
            'operationalPlan' => $operationalPlan,
        ]);
    }

    public function edit($id)
    {
        $operationalPlan = OperationalPlan::findOrFail($id);

        // breadcrumbs
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('operational-plan.index') => __('breadcrumbs.operational-plans.index'),
            route('operational-plan.show', $operationalPlan->id) => $operationalPlan->title,
            route('operational-plan.edit', $operationalPlan->id) => __('breadcrumbs.operational-plans.edit'),
        ];

        return view('operational-plans.edit', [
            'breadcrumbs' => $breadcrumbs,
            'operationalPlan' => $operationalPlan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'views' => 'integer',
            'period' => 'string|max:255',
        ]);

        $operationalPlan = OperationalPlan::findOrFail($id);
        $operationalPlan->update($request->all());

        flash()->success(__('operational-plan.edit.success'));
        return redirect()->route('operational-plan.show', $operationalPlan->id);
    }

    public function destroy($id)
    {
        // delete all relations

        $operationalPlan = OperationalPlan::with(['departments', 'summaryPrograms'])->findOrFail($id);
        $operationalPlan->summaryPrograms()->delete();
        $operationalPlan->departments()->each(function ($department) {
            $department->strategicGoals()->delete();
            $department->programs()->each(function ($program) {
                $program->subPrograms()->each(function ($subProgram) {
                    $subProgram->items()->delete();
                    $subProgram->activities()->delete();
                });
                $program->subPrograms()->delete();
                $program->delete();
            });
            $department->delete();
        });
        $operationalPlan = OperationalPlan::findOrFail($id);

        $operationalPlan->delete();

        flash()->success(__('operational-plan.delete.success'));
        return redirect()->route('operational-plan.index');
    }

    public function storeDepartment(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $operationalPlan = OperationalPlan::findOrFail($id);
        $operationalPlan->departments()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        flash()->success(__('operational-plan.show.departments.add_department.success'));
        return redirect()->route('operational-plan.show', $operationalPlan->id);
    }

    public function destroyDepartment($planId, $departmentId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);

        $department->delete();

        flash()->success(__('operational-plan.show.departments.delete.success'));
        return redirect()->route('operational-plan.show', $operationalPlan->id);
    }

    public function storeStrategicGoal(Request $request, $planId, $departmentId)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $department->strategicGoals()->create([
            'title' => $request->input('title'),
            'operational_plan_id' => $operationalPlan->id,
        ]);

        flash()->success(__('operational-plan.show.summary.add_strategic_goal.success'));
        return redirect()->route('operational-plan.show', $operationalPlan->id);
    }

    public function showStrategicGoal($planId, $departmentId, $strategicGoalId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $strategicGoal = $department->strategicGoals()->findOrFail($strategicGoalId);

        $strategicGoal->load('programs');

        // breadcrumbs
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('operational-plan.index') => __('breadcrumbs.operational-plans.index'),
            route('operational-plan.show', $operationalPlan->id) => $operationalPlan->title,
            route('operational-plan.show', $operationalPlan->id) => $department->title,
            route('operational-plan.departments.strategic-goals.show', [$operationalPlan->id, $department->id, $strategicGoal->id]) => $strategicGoal->title,
        ];

        return view('operational-plans.strategic-goal.show', [
            'breadcrumbs' => $breadcrumbs,
            'operationalPlan' => $operationalPlan,
            'department' => $department,
            'strategicGoal' => $strategicGoal,
        ]);
    }


    public function destroyStrategicGoal($planId, $departmentId, $strategicGoalId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $strategicGoal = $department->strategicGoals()->findOrFail($strategicGoalId);

        $strategicGoal->delete();

        flash()->success(__('operational-plan.show.summary.delete.success'));
        return redirect()->route('operational-plan.show', $operationalPlan->id);
    }

    public function storeProgram(Request $request, $planId, $departmentId, $strategicGoalId)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $strategicGoal = $department->strategicGoals()->findOrFail($strategicGoalId);

        $strategicGoal->programs()->create([
            'title' => $request->input('title'),
            'operational_plan_id' => $operationalPlan->id,
            'operational_plan_department_id' => $department->id,
            'operational_plan_strategic_goal_id' => $strategicGoal->id,
        ]);

        flash()->success(__('operational-plan.show.summary.add_program.success'));
        return redirect()->route('operational-plan.departments.strategic-goals.show', [$operationalPlan->id, $department->id, $strategicGoal->id]);
    }

    function storeSubProgram(Request $request, $planId, $departmentId, $programId)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();



        $program->subPrograms()->create([
            'title' => $request->input('title'),
            'operational_plan_id' => $operationalPlan->id,
            'operational_plan_department_id' => $department->id,
            'operational_plan_strategic_goal_id' => $program->operational_plan_strategic_goal_id,
            'operational_plan_program_id' => $program->id,
        ]);

        flash()->success(__('operational-plan.show.summary.add_sub_program.success'));
        return redirect()->route('operational-plan.departments.strategic-goals.show', [$operationalPlan->id, $department->id, $program->operational_plan_strategic_goal_id]);
    }

    function destroySubProgram($planId, $departmentId, $programId, $subProgramId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();

        $subProgram = $program->subPrograms()->findOrFail($subProgramId);

        // delete items if exist
        $subProgram->items()->delete();
        $subProgram->delete();

        flash()->success(__('operational-plan.show.summary.delete.success'));
        return redirect()->route('operational-plan.departments.strategic-goals.show', [$operationalPlan->id, $department->id, $strategicGoal->id]);
    }

    function showSubProgram($planId, $departmentId, $programId, $subProgramId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('operational-plan.index') => __('breadcrumbs.operational-plans.index'),
            route('operational-plan.show', $operationalPlan->id) => $operationalPlan->title,
            route('operational-plan.show', $operationalPlan->id) => $department->title,
            route('operational-plan.departments.strategic-goals.show', [$operationalPlan->id, $department->id, $strategicGoal->id]) => $strategicGoal->title,
            route('operational-plan.departments.subprograms.show', [$operationalPlan->id, $department->id, $program->id, $subProgram->id]) => $subProgram->title,
        ];


        return view('operational-plans.sub-program.show', [
            'breadcrumbs' => $breadcrumbs,
            'operationalPlan' => $operationalPlan,
            'department' => $department,
            'strategicGoal' => $strategicGoal,
            'program' => $program,
            'subProgram' => $subProgram,
        ]);
    }

    // activities

    function storeActivity(Request $request, $planId, $departmentId, $programId, $subProgramId)
    {

        $request->validate([
            'title' => 'required',
            'yearly_target' => 'required|integer',
        ]);

        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);
        $subProgram->activities()->create([
            'title' => $request->input('title'),
            'yearly_target' => $request->input('yearly_target'),
        ]);
        flash()->success(__('operational-plan.show.summary.add_activity.success'));
        return redirect()->route('operational-plan.departments.subprograms.show', [$operationalPlan->id, $department->id, $program->id, $subProgram->id]);
    }
    function destroyActivity($planId, $departmentId, $programId, $subProgramId, $activityId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);

        // delete activity
        $activity = $subProgram->activities()->findOrFail($activityId);
        $activity->delete();

        flash()->success(__('operational-plan.show.summary.delete.success'));
        return redirect()->route('operational-plan.departments.subprograms.show', [$operationalPlan->id, $department->id, $program->id, $subProgram->id]);
    }
    function showActivity($planId, $departmentId, $programId, $subProgramId, $activityId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);
        $activity = $subProgram->items()->findOrFail($activityId);

        // breadcrumbs
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('operational-plan.index') => __('breadcrumbs.operational-plans.index'),
            route('operational-plan.show', $operationalPlan->id) => __('breadcrumbs.operational-plans.show'),
            route('operational-plan.departments.strategic-goals.show', [$operationalPlan->id, $department->id, $strategicGoal->id]) => __('breadcrumbs.operational-plans.strategic-goal.show'),

        ];

        return view('operational-plans.activity.show', [
            'breadcrumbs' => $breadcrumbs,
            'operationalPlan' => $operationalPlan,
            'department' => $department,
            'strategicGoal' => $strategicGoal,
            'program' => $program,
            'subProgram' => $subProgram,
            'activity' => $activity,
        ]);
    }
    function editActivity($planId, $departmentId, $programId, $subProgramId, $activityId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);
        $activity = $subProgram->items()->findOrFail($activityId);

        // breadcrumbs
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('operational-plan.index') => __('breadcrumbs.operational-plans.index'),
            route('operational-plan.show', $operationalPlan->id) => __('breadcrumbs.operational-plans.show'),
            route('operational-plan.departments.strategic-goals.show', [$operationalPlan->id, $department->id, $strategicGoal->id]) => __('breadcrumbs.operational-plans.strategic-goal.show'),

        ];

        return view('operational-plans.activity.edit', [
            'breadcrumbs' => $breadcrumbs,
            'operationalPlan' => $operationalPlan,
            'department' => $department,
            'strategicGoal' => $strategicGoal,
            'program' => $program,
            'subProgram' => $subProgram,
            'activity' => $activity,
        ]);
    }
    function updateActivity(Request $request, $planId, $departmentId, $programId, $subProgramId, $activityId)
    {
        $request->validate([
            'title' => 'required',
            'budget' => 'required|numeric',
        ]);

        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);

        // update activity
        $activity = $subProgram->items()->findOrFail($activityId);
        $activity->update([
            'title' => $request->input('title'),
            'budget' => $request->input('budget'),
        ]);

        flash()->success(__('operational-plan.show.summary.update_activity.success'));
        return redirect()->route('operational-plan.departments.strategic-goals.show', [$operationalPlan->id, $department->id, $strategicGoal->id]);
    }

    public function storeItem(Request $request, $planId, $departmentId, $programId, $subProgramId)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);

        // create item
        $subProgram->items()->create([
            'title' => $request->input('title'),
            'quantity' => $request->input('quantity'),
            'unit_cost' => $request->input('unit_cost'),
            'total_cost' => $request->input('quantity') * $request->input('unit_cost'),
            'operational_plan_sub_program_id' => $subProgram->id,
        ]);

        flash()->success(__('operational-plan.show.summary.add_item.success'));
        return redirect()->route('operational-plan.departments.subprograms.show', [$operationalPlan->id, $department->id, $program->id, $subProgram->id]);
    }

    public function destroyItem($planId, $departmentId, $programId, $subProgramId, $itemId)
    {
        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);

        // delete item
        $item = $subProgram->items()->findOrFail($itemId);
        $item->delete();

        flash()->success(__('operational-plan.show.summary.delete.success'));
        return redirect()->route('operational-plan.departments.subprograms.show', [$operationalPlan->id, $department->id, $program->id, $subProgram->id]);
    }

    // update item
    public function updateItem(Request $request, $planId, $departmentId, $programId, $subProgramId, $itemId)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $operationalPlan = OperationalPlan::findOrFail($planId);
        $department = $operationalPlan->departments()->findOrFail($departmentId);
        $program = OperationalPlanProgram::findOrFail($programId);
        $strategicGoal = $department->strategicGoals()->where('id', $program->operational_plan_strategic_goal_id)->firstOrFail();
        $subProgram = $program->subPrograms()->findOrFail($subProgramId);

        // update item
        $item = $subProgram->items()->findOrFail($itemId);
        $item->update([
            'title' => $request->input('title')
        ]);

        flash()->success(__('operational-plan.show.summary.update_item.success'));
        return redirect()->route('operational-plan.departments.subprograms.show', [$operationalPlan->id, $department->id, $program->id, $subProgram->id]);
    }
}
