<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('notifications.index') => __('breadcrumbs.notifications.index')
        ];

        $notifications = auth()->user()->notifications()->latest()->paginate();


        return view('notifications', compact('notifications', 'breadcrumbs'));
    }

    function show($notification)
    {
        $notification = auth()->user()->notifications()->findOrFail($notification);
        $notification->markAsSeen();

        return redirect($notification->action_url);
    }

    function markAllAsSeen()
    {
        auth()->user()->notifications()->unseen()->get()->each->markAsRead();

        return back();
    }
}
