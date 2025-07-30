<?php

namespace App\Http\Controllers;

use App\Models\StaticPage;
use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    public function show($slug)
    {
        $page = StaticPage::where('slug', $slug)->firstOrFail();

        // Check if user has access to this page
        if (!$page->hasAccess()) {
            abort(403);
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('static-pages.show', $page->slug) => $page->title,
        ];

        // Increment view count
        $page->incrementViews();

        return view('static-pages.show', compact('page', 'breadcrumbs'));
    }

    public function index()
    {
        $pages = StaticPage::getPublicPages();

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('static-pages.index') => __('static-pages.public.index'),
        ];

        return view('static-pages.index', compact('pages', 'breadcrumbs'));
    }
} 