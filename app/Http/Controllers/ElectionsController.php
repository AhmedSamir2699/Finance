<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\ElectionVote;
use Illuminate\Http\Request;

class ElectionsController extends Controller
{
    function show($id)
    {
        $election = Election::findOrFail($id);
        $state = null;
        if ($election->is_public == false) {
            abort(404);
        }

        // check voting cookie
        if (request()->hasCookie('voted') || $election->votes()->where('ip_address', request()->ip())->count() >= 5) {
            $state = 'voted';
        }
        if ($election->start_date > now()) {
            $state = 'future';
        }
        if ($election->end_date < now()) {
            $state = 'past';
        }

        return view('elections.show', [
            'election' => $election,
            'state' => $state,
            'candidates' => $election->candidates_collection,
        ]);
    }

    function voteStore(Request $request, $id)
    {
        $election = Election::findOrFail($id);
        if ($election->is_public == false) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'nationalId' => 'required|string|max:10|min:10',
            'selectedCandidates' => 'required|array',
            'selectedCandidates.*' => 'required|integer|distinct',
        ]);

        foreach ($request->selectedCandidates as $candidateId) {
            $existingVote = ElectionVote::where('election_id', $election->id)
                ->where('nin', $request->nationalId)
                ->where('candidate_id', $candidateId)
                ->first();
            $countVotes = ElectionVote::where('election_id', $election->id)
                ->where('nin', $request->nationalId)
                ->count();

            if ($countVotes < 5 && !$existingVote) {
                ElectionVote::create([
                    'election_id' => $election->id,
                    'name' => $request->name,
                    'nin' => $request->nationalId,
                    'candidate_id' => $candidateId,
                    'ip_address' => $request->ip(),
                ]);
            }else {
                $cookie = cookie('voted', 'true', 60 * 24 * 30);
                flash()->error(__('elections.vote.error'));
                return response()->json(__('elections.vote.error'))
                    ->setStatusCode(400)
                    ->withCookie($cookie);
            }
        }
                    flash()->success(__('elections.vote.success'));
        return response()->json(__('elections.vote.success'))
            ->setStatusCode(200);
    }

    function manageIndex()
    {
        $elections = Election::orderBy('start_date', 'desc')->paginate();
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.elections.index') => __('breadcrumbs.elections.index'),
        ];

        return view('manage.elections.index', [
            'elections' => $elections,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    function manageCreate()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.elections.index') => __('breadcrumbs.elections.index'),
            route('manage.elections.create') => __('breadcrumbs.elections.create'),
        ];

        return view('manage.elections.create', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
    function manageStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'candidates' => 'nullable|array',
        ]);

        $candidates = $request->candidates ?? [];

        $candidates = array_map(function ($candidate, $index) {
            return [
                'id' => $index + 1,
                'name' => $candidate['name'],
            ];
        }, $candidates, array_keys($candidates));

        $candidates = array_values($candidates);

        Election::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_public' => $request->is_public,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'candidates' => $candidates,
        ]);

        flash()->success(__('elections.create.success'));
        return redirect()->route('manage.elections.index');
    }

    function manageEdit($id)
    {
        $election = Election::findOrFail($id);
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.elections.index') => __('breadcrumbs.elections.index'),
            route('manage.elections.edit', $election->id) => __('breadcrumbs.elections.edit'),
        ];

        return view('manage.elections.edit', [
            'election' => $election,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
    function manageUpdate(Request $request, $id)
    {
        $election = Election::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'candidates' => 'nullable|array'
        ]);

        if ($election->votes()->exists()) {
            $candidates = $election->candidates;
            if(count($request->candidates) != count($election->candidates)){
                flash()->error(__('manage.elections.edit.candidates_warning'));
            }
        } else {
            $candidates = $request->candidates;
            $candidates = array_map(function ($candidate, $index) {
                return [
                    'id' => $index + 1,
                    'name' => $candidate['name'],
                ];
            }, $candidates, array_keys($candidates));

            $candidates = array_values($candidates);
        }

        $election->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_public' => $request->is_public,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'candidates' => $candidates,
        ]);

        flash()->success(__('manage.elections.edit.success'));
        return redirect()->route('manage.elections.index');
    }
    function manageDelete($id)
    {
        $election = Election::findOrFail($id);
        $election->delete();

        flash()->success(__('manage.elections.index.table_actions.delete_success'));
        return redirect()->route('manage.elections.index');
    }
    function manageShow($id)
    {
        $election = Election::findOrFail($id);
        $election->load('votes');
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.elections.index') => __('breadcrumbs.elections.index'),
            route('manage.elections.show', $election->id) => $election->name,
        ];

        return view('manage.elections.show', [
            'election' => $election,
            'breadcrumbs' => $breadcrumbs,
            'votes' => $election->votes,
        ]);
    }

    function manageClearVotes($id)
    {
        $election = Election::findOrFail($id);
        $election->votes()->delete();

        flash()->success(__('manage.elections.edit.clear_votes_success'));
        return redirect()->route('manage.elections.index');
    }
    
    function manageVotes($id)
    {
        $election = Election::findOrFail($id);
        $votes = $election->votes()->latest()->paginate(10);
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.elections.index') => __('breadcrumbs.elections.index'),
            route('manage.elections.show', $election->id) => $election->name,
            route('manage.elections.votes', $election->id) => __('breadcrumbs.elections.votes'),
        ];

        return view('manage.elections.votes', [
            'election' => $election,
            'breadcrumbs' => $breadcrumbs,
            'votes' => $votes,
        ]);
    }
}
