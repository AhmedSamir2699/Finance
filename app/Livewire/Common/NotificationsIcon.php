<?php

namespace App\Livewire\Common;

use Livewire\Component;

class NotificationsIcon extends Component
{
    public $notifications = false;
    public $hasUnseenNotifications = false;
    public $notificationsOpen = false; 


    public function closeNotifications()
    {
        $this->notificationsOpen = false;
    }

    public function showNotifications()
    {
        $this->notifications = auth()->user()->notifications()->latest()->limit(5)->get();
        $this->notificationsOpen = true;
        auth()->user()->seenAllNotifications();
        $this->hasUnseenNotifications = false;
    }

    public function render()
    {
        $countUnseenNotifications = auth()->user()->notifications()->unseen()->count();
        if ($countUnseenNotifications > 0) $this->hasUnseenNotifications = true;

        return view('livewire.common.notifications-icon');
    }
}
