<?php

namespace App\Http\Controllers;

use App\Models\RequestForm;
use App\Models\RequestFormSubmission;
use Illuminate\Http\Request;
use App\Helpers\NotificationHelper;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;

class RequestsController extends Controller
{
    function index() {

        $requests = RequestFormSubmission::where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate();

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('requests.index') => __('breadcrumbs.requests.index')
        ];

        return view('requests.index', ['requests' => $requests, 'breadcrumbs' => $breadcrumbs]);
    }

    function create(Request $request)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('requests.index') => __('breadcrumbs.requests.index'),
            route('requests.create') => __('breadcrumbs.requests.create')
        ];

        return view('requests.create', ['breadcrumbs' => $breadcrumbs]);
    }

    function store(Request $request)
    {

        
        $fields = $request->except('_token', 'form_id', 'files');

        $path = RequestForm::with('path')->find($request->form_id)->path()->orderBy('step_order')->get();

        $firstStep = $path->first();

        if(!$firstStep){
            return abort(404);
        }

          if($request->has('files')){
            foreach($request->file('files') as $name => $file){
                foreach($file as $f){
                    $fields[$name][] = $f->store('files');
                }
            }
          }




        $submittedRequest = RequestFormSubmission::create([
            'request_form_id' => $request->form_id,
            'user_id' => auth()->id(),
            'fields' => ['data' => $fields, 'order' => array_keys($fields)],
            'steps' => $path->toJson(),
            'current_step' => $firstStep->step_order

        ]);

        if (!is_null($firstStep->user_id))
            NotificationHelper::sendNotification(User::find($firstStep->user_id), 'لديك طلب جديد يتطلب اجراءاتك', 'requests.show', $submittedRequest->id);

        if (!is_null($firstStep->role_id)){
            $users = User::where('department_id', $firstStep->department_id)->whereHas('roles', function ($query) use ($firstStep) {
                $query->where('id', $firstStep->role_id);
            })->get();
            foreach ($users as $user) {
                NotificationHelper::sendNotification($user, 'لديك طلب جديد يتطلب اجراءاتك', 'requests.show', $submittedRequest->id);
            }
        }

        return redirect()->route('requests.index');
    }

    function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ]);
    }

    function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);
    }

    function show($id)
    {
        $request = RequestFormSubmission::with('requestForm')->find($id);

        if(!$request){
            return abort(404);
        }

        if($request->steps == '[]'){
            return abort(404);
        }

        $steps = json_decode($request->steps);
        $currentStep = $steps[$request->current_step - 1];

        $isApprover = $currentStep->user_id == auth()->id() || (!is_null($currentStep->role_id) && $currentStep->role_id == auth()->user()->roles()->where('id', $currentStep->role_id)->first()?->id);

        if(
            !$isApprover &&
            $request->user_id != auth()->id()
            ){
            return abort(403);
        }

        
        foreach ($steps as $step) {
            $step->user = User::find($step->user_id);
            $step->role = $step->role_id ? Role::find($step->role_id) : null;
            $step->department = $step->department_id ? Department::find($step->department_id) : null;
            $step->approved = $step->approved ?? false;
            $step->rejected = $step->rejected ?? false;
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('requests.index') => __('breadcrumbs.requests.index'),
            route('requests.show', $id) => $request->requestForm->title
        ];
        return view('requests.show', [
            'request' => $request,
            'breadcrumbs' => $breadcrumbs,
            'isApprover' => $isApprover,
            'steps' => $steps,
        ]);
    }

    function search(Request $request)
    {
        $request->validate([
            'search' => 'required',
        ]);
    }

    function approve($id)
    {
        $submission = RequestFormSubmission::find($id);

        $steps = json_decode($submission->steps);

        $currentStep = $steps[$submission->current_step - 1];
        $nextStep = null;

        $isApprover = $currentStep->user_id == auth()->id() ||  (!is_null($currentStep->role_id) && $currentStep->role_id == auth()->user()->roles()->where('id', $currentStep->role_id)->first()?->id);

        if(!$isApprover){
            return abort(403);
        }

        $steps[$submission->current_step - 1]->approved = true;
        $steps[$submission->current_step - 1]->approved_at = now();

        // if last step
        if ($submission->current_step == count($steps)) {
            $submission->status = 'approved';

            NotificationHelper::sendNotification(User::find($submission->user_id), 'تمت الموافقة على طلبك', 'requests.show', $submission->id);

        }else{

            $nextStep = $steps[$submission->current_step];

            $submission->current_step = $nextStep->step_order;

        }

        $submission->steps = json_encode($steps);

        if (!is_null($nextStep?->user_id))
            NotificationHelper::sendNotification(User::find($nextStep->user_id), 'لديك طلب جديد يتطلب اجراءاتك', 'requests.show', $submission->id);

        if (!is_null($nextStep?->role_id)){
            $users = User::where('department_id', $nextStep->department_id)->whereHas('roles', function ($query) use ($nextStep) {
                $query->where('id', $nextStep->role_id);
            })->get();
            foreach ($users as $user) {
                NotificationHelper::sendNotification($user, 'لديك طلب جديد يتطلب اجراءاتك', 'requests.show', $submission->id);
            }
        }

        $submission->save();

        return redirect()->route('requests.index');
    }

    function reject($id)
    {
        $submission = RequestFormSubmission::find($id);

        $steps = json_decode($submission->steps);

        $currentStep = $steps[$submission->current_step - 1];

        $isApprover = $currentStep->user_id == auth()->id() ||  (!is_null($currentStep->role_id) && $currentStep->role_id == auth()->user()->roles()->where('id', $currentStep->role_id)->first()?->id);

        if(!$isApprover){
            return abort(403);
        }

        $steps[$submission->current_step - 1]->rejected = true;
        $steps[$submission->current_step - 1]->rejected_at = now();

        $submission->steps = json_encode($steps);

        $submission->status = 'rejected';

        $submission->save();

        return redirect()->route('requests.index');
    }
}
