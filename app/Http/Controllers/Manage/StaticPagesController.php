<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaticPagesController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.static-pages.index') => __('static-pages.title'),
        ];

        return view('manage.static-pages.index', compact('breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.static-pages.index') => __('static-pages.title'),
            route('manage.static-pages.create') => __('static-pages.create'),
        ];

        return view('manage.static-pages.create', compact('breadcrumbs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:static_pages,slug',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'visibility' => 'required|in:all,authenticated,guest',
            'is_active' => 'boolean',
        ]);

        $page = StaticPage::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'visibility' => $request->visibility,
            'is_active' => $request->has('is_active'),
        ]);

        toastr()->success(__('static-pages.messages.created'));

        return redirect()->route('manage.static-pages.index');
    }

    public function edit(StaticPage $staticPage)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.static-pages.index') => __('static-pages.title'),
            route('manage.static-pages.edit', $staticPage->id) => __('static-pages.edit'),
        ];

        return view('manage.static-pages.edit', compact('staticPage', 'breadcrumbs'));
    }

    public function update(Request $request, StaticPage $staticPage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:static_pages,slug,' . $staticPage->id,
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'visibility' => 'required|in:all,authenticated,guest',
            'is_active' => 'boolean',
        ]);

        $staticPage->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'visibility' => $request->visibility,
            'is_active' => $request->has('is_active'),
        ]);

        toastr()->success(__('static-pages.messages.updated'));

        return redirect()->route('manage.static-pages.index');
    }

    public function destroy(StaticPage $staticPage)
    {
        $staticPage->delete();

        toastr()->success(__('static-pages.messages.deleted'));

        return redirect()->route('manage.static-pages.index');
    }

    public function toggleStatus(StaticPage $staticPage)
    {
        $staticPage->update(['is_active' => !$staticPage->is_active]);

        $message = $staticPage->is_active 
            ? __('static-pages.messages.activated')
            : __('static-pages.messages.deactivated');

        toastr()->success($message);

        return redirect()->route('manage.static-pages.index');
    }
} 