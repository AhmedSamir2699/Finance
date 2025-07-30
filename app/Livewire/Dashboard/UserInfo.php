<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
use Alkoumi\LaravelHijriDate\Hijri;

class UserInfo extends Component
{
    public $user;
    public $greeting;
    public $gregorianDate;
    public $hijriDate;
    public $dayName;

    public function mount()
    {
        $this->user = Auth::user();
        $this->setGreeting();
        $this->dayName = Carbon::now()->translatedFormat('l');
        $this->gregorianDate = Carbon::now()->translatedFormat('j F Y');
        $this->hijriDate = Hijri::Date('j F Y');
    }

    public function setGreeting()
    {
        $hour = Carbon::now()->hour;
        if ($hour < 12) {
            $this->greeting = __('dashboard.greetings.morning');
        } elseif ($hour < 18) {
            $this->greeting = __('dashboard.greetings.afternoon');
        } else {
            $this->greeting = __('dashboard.greetings.evening');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.user-info');
    }
}
