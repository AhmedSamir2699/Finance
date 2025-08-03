<div class="fixed inset-y-0 right-0 z-30 w-64 overflow-y-auto bg-primary-900 transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
    <div class="flex items-center justify-center mt-8">
        <div class="flex flex-col items-center">
            @if(auth()->check())
                @if(auth()->user()->profile_picture)
                    <div class="h-48 w-48 rounded overflow-hidden">
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" class="w-full h-full object-cover" />
                    </div>
                @endif
                <span class="text-white text-sm mt-2">{{ auth()->user()->name }}</span>
                <span class="text-white text-sm mt-2">{{ auth()->user()->department->name }}</span>
            @else
                <img src="{{ asset('storage/'.\App\Helpers\SettingsHelper::get('app_logo', '/images/logo.png')) }}" alt="{{ \App\Helpers\SettingsHelper::appName() }}" class="w-3/4 mx-auto" />
            @endif
        </div>
    </div>
    <nav class="mt-10">

        @foreach(\App\Models\SidebarLink::getSidebarLinks() as $link)
            @if($link->children && $link->children->count() > 0)
                @php
                    $dropdownItems = [];
                    $totalBadgeCount = 0;
                    
                    foreach($link->children as $child) {
                        $url = $child->is_external ? $child->url : ($child->url ? route($child->url) : '#');
                        $isActive = $child->url ? request()->routeIs($child->url . '*') : false;
                        
                        // Calculate badge for this child
                        $badgeCount = 0;
                        if (str_contains($child->url, 'need-approval') && 
                                (auth()->user()->can('task.approve-department') || auth()->user()->can('task.approve-subordinates'))) {
                            $subordinateUsers = auth()->user()->subordinateUsers();
                            $subordinateUserIds = $subordinateUsers->pluck('id')->toArray();
                            
                            $badgeCount = \App\Models\Task::where('status', 'submitted')
                                ->where(function ($query) use ($subordinateUserIds) {
                                    $query->where('assigned_by', auth()->id())
                                        ->orWhereIn('user_id', $subordinateUserIds);
                                })
                                ->whereNot('user_id', auth()->id())
                                ->count();
                        }
                        
                        $dropdownItems[] = [
                            'label' => $child->display_title,
                            'url' => $url,
                            'badgeCount' => $badgeCount,
                        ];
                        $totalBadgeCount += $badgeCount;
                    }
                @endphp
                <x-nav-link href="#" :isDropdown="true" :dropdownItems="$dropdownItems" :badgeCount="$totalBadgeCount">
                    <i class="{{ $link->icon }}"></i>
                    <span class="mx-3">{{ $link->display_title }}</span>
                </x-nav-link>
            @else
                @php
                    $url = $link->is_external ? $link->url : ($link->url ? route($link->url) : '#');
                    $isActive = $link->url ? request()->routeIs($link->url . '*') : false;
                    
                    // Calculate badge for main link
                    $badgeCount = 0;
                    if (str_contains($link->url, 'evaluate') && auth()->user()->can('evaluate')) {
                        $badgeCount = \App\Models\User::with(['evaluationScores' => function ($query) {
                            $query->where('evaluated_at', now()->toDateString());
                        }, 'timesheets' => function ($query) {
                            $query->whereDate('start_at', now());
                        }])
                        ->whereDoesntHave('evaluationScores', function ($query) {
                            $query->where('evaluated_at', now()->toDateString());
                        })
                        ->whereHas('timesheets', function ($query) {
                            $query->whereDate('start_at', now());
                        })
                        ->count();
                    }
                @endphp
                <x-nav-link href="{{ $url }}" :active="$isActive" :badgeCount="$badgeCount">
                    <i class="{{ $link->icon }}"></i>
                    <span class="mx-3">{{ $link->display_title }}</span>
                </x-nav-link>
            @endif
        @endforeach



        @if (!auth()->check())
            <x-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                <i class="fa fa-sign-in-alt"></i>
                <span class="mx-3">{{ __('sidebar.login') }}</span>
            </x-nav-link>
        @endif
    </nav>
</div>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
    const toggleIcon = document.getElementById('toggle-icon');
    const overlay = document.getElementById('sidebar-overlay');
    
    let isOpen = false;
    
    // Function to toggle sidebar
    function toggleSidebar() {
        if (isOpen) {
            // Close sidebar
            sidebar.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-bars');
            isOpen = false;
        } else {
            // Open sidebar
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            toggleIcon.classList.remove('fa-bars');
            toggleIcon.classList.add('fa-times');
            isOpen = true;
        }
    }
    
    // Mobile toggle button
    mobileSidebarToggle.addEventListener('click', toggleSidebar);
    
    // Overlay click to close
    overlay.addEventListener('click', function() {
        if (isOpen) {
            toggleSidebar();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            toggleSidebar();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-bars');
            isOpen = false;
        } else {
            sidebar.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-bars');
            isOpen = false;
        }
    });
});
</script>
