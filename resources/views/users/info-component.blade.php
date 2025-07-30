<div x-data="{ showPersonalInfo: false, showTimesheet: false, showHierarchy: false }">
    <div class="bg-white rounded-lg shadow-b-xl pb-8">
        <div x-data="{ open: false }" class="w-full h-[150px] bg-gray-200">
        </div>
        <div class="flex flex-col items-center -mt-20">
            @if ($user->profile_picture)
                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                    class="w-40 h-40 border-4 border-white rounded-full">
            @else
                <img src="https://ui-avatars.com/api/?length=1&background=random&name={{ $user->name }}"
                    class="w-40 h-40 border-4 border-white rounded-full" alt="">
            @endif
            <div class="flex items-center space-x-2 mt-2">
                <p class="text-2xl  mx-3">{{ $user->name }}</p>
                @if ($user->isOnline)
                    <span class="bg-green-500 rounded-full p-1 mx-3" title="Online"></span>
                @else
                    <span class="bg-gray-400 rounded-full p-1 mx-3" title="Offline"></span>
                @endif
            </div>
            <p class="text-gray-700">{{ $user->position ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $user->department->name ?? '-' }}</p>
        </div>
    </div>

    <div class="my-4 flex flex-col space-y-4">
        <div class="flex-1 bg-white rounded-lg shadow-xl p-8">
            <h4 class="text-xl text-gray-900 font-bold">{{ __('users.actions') }}</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mt-4">
                <a href="{{ route('messages.create-to', $user) }}"
                    class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                    <i class="fas fa-envelope text-lg mb-1"></i>
                    <span>{{ __('users.message') }}</span>
                </a>
                @can('user.view')
                    <button @click="showPersonalInfo = !showPersonalInfo"
                        class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                        <i class="fas fa-info-circle text-lg mb-1"></i>
                        <span>{{ __('users.personal_info') }}</span>
                    </button>
                @endcan


                <button @click="showTimesheet = !showTimesheet"
                    class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                    <i class="fas fa-clock text-lg mb-1"></i>
                    <span>{{ __('users.timesheet.timesheet') }}</span>
                </button>

                <a href="{{ route('users.reports.show', $user) }}"
                    class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                    <i class="fas fa-file-alt text-lg mb-1"></i>
                    <span>{{ __('users.reports') }}</span>
                </a>

                @php
                    $isCurrentUser = $user->id === auth()->id();
                @endphp
                @if ($isCurrentUser)
                    <a href="{{ route('users.summary.my', [
                        'start_date' => \Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                        'end_date' => \Carbon\Carbon::now()->subMonth()->endOfMonth()->toDateString(),
                    ]) }}" 
                       class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                        <i class="fas fa-chart-line text-lg mb-1"></i>
                        <span>{{ __('users.summary.title_short') }}</span>
                    </a>
                @endif
                
                @if (auth()->user()->subordinateUsers()->contains('id', $user->id))
                    <a href="{{ route('tasks.create', ['user' => $user]) }}"
                        class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                        <i class="fas fa-tasks text-lg mb-1"></i>
                        <span>{{ __('tasks.add_task') }}</span>
                    </a>
                @endif

                @canany(['executive-plan.view-department', 'executive-plan.view-any'])
                    <a href="{{ route('users.executive-plan.index', [$user]) }}"
                        class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                        <i class="fas fa-briefcase text-lg mb-1"></i>
                        <span>{{ __('dashboard.executive_plan') }}</span>
                    </a>
                @endcanany

                @can('departments.view')
                    <a href="{{ route('departments.show', [$user->department]) }}"
                        class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                        <i class="fas fa-building text-lg mb-1"></i>
                        <span>{{ __('departments.show') }}</span>
                    </a>
                @endcan

                <button @click="showHierarchy = !showHierarchy"
                    class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                    <i class="fas fa-sitemap text-lg mb-1"></i>
                    <span>{{ __('users.hierarchy') }}</span>
                </button>

                @can('user.edit')
                    <a href="{{ route('manage.users.edit', $user) }}"
                        class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                        <i class="fas fa-edit text-lg mb-1"></i>
                        <span>{{ __('users.edit_button') }}</span>
                    </a>
                @endcan

                @can('user.impersonate')
                    <a href="{{ route('users.impersonate', $user) }}"
                        class="flex flex-col items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-300 text-sm">
                        <i class="fas fa-user-secret text-lg mb-1"></i>
                        <span>{{ __('users.impersonate') }}</span>
                    </a>
                @endcan
            </div>
        </div>
        @can('user.view')
            <div x-show="showPersonalInfo" x-cloak class="flex-1 bg-white rounded-lg shadow-xl p-8">
                <h4 class="text-xl text-gray-900 font-bold mb-6">{{ __('users.personal_info') }}</h4>
                <div class="bg-gray-50 rounded-lg border p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-semibold mb-1">{{ __('users.fullname') }}</span>
                        <span class="text-gray-900">{{ $user->name }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-semibold mb-1">{{ __('users.phone') }}</span>
                        <span class="text-gray-900">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-semibold mb-1">{{ __('users.email') }}</span>
                        <span class="text-gray-900">{{ $user->email ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-semibold mb-1">{{ __('users.department') }}</span>
                        <span class="text-gray-900">{{ $user->department->name ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-semibold mb-1">{{ __('users.position') }}</span>
                        <span class="text-gray-900">{{ $user->position ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-semibold mb-1">{{ __('users.roles') }}</span>
                        <span class="text-gray-900">{{ $user->roles?->pluck('display_name')->implode(', ') }}</span>
                    </div>
                </div>
            </div>
        @endcan
        <div x-show="showTimesheet" x-cloak class="flex-1 bg-white rounded-lg shadow-xl p-8">
            @livewire('users.timesheet-view', ['user' => $user])
        </div>

        <div x-show="showHierarchy" x-cloak class="flex-1 bg-white rounded-lg shadow-xl p-8">
            <style>
                .tree {
                    direction: rtl;
                    display: flex;
                    justify-content: center;
                    overflow-x: auto;
                    padding-bottom: 2rem;
                }

                .tree ul {
                    padding-top: 20px;
                    position: relative;
                    transition: all 0.5s;
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                }

                .tree li {
                    text-align: center;
                    list-style-type: none;
                    position: relative;
                    padding: 20px 5px 0 5px;
                    transition: all 0.5s;
                    min-width: 180px;
                }

                .tree li::before,
                .tree li::after {
                    content: '';
                    position: absolute;
                    top: 0;
                    right: 50%;
                    border-top: 1px solid #ccc;
                    width: 50%;
                    height: 20px;
                }

                .tree li::after {
                    right: auto;
                    left: 50%;
                    border-left: 1px solid #ccc;
                }

                .tree li:only-child::after,
                .tree li:only-child::before {
                    display: none;
                }

                .tree li:only-child {
                    padding-top: 0;
                }

                /* RTL fix: swap ::before and ::after for first/last child */
                .tree li:first-child::before {
                    border-radius: 0 5px 0 0;
                    border-right: 1px solid #ccc;
                }

                .tree li:last-child::after {
                    border-radius: 5px 0 0 0;
                    border-left: 1px solid #ccc;
                }

                .tree li:first-child::after,
                .tree li:last-child::before {
                    border: 0 none;
                }

                .tree ul ul::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 50%;
                    border-left: 1px solid #ccc;
                    width: 0;
                    height: 20px;
                }

                .tree li a {
                    border: 1px solid #ccc;
                    padding: 5px 10px;
                    text-decoration: none;
                    color: #666;
                    display: inline-block;
                    border-radius: 5px;
                    transition: all 0.5s;
                }
            </style>
            <h4 class="text-xl text-gray-900 font-bold mb-4 text-center">{{ __('users.hierarchy') }}</h4>
            <div class="tree">
                <ul class="flex flex-wrap justify-center">
                    @if ($superior = $user->superiorUser())
                        <li>
                            <x-user-bubble :user="$superior" />
                            <ul>
                                @php
                                    $siblings = $user->siblingUsers()->push($user)->unique('id');
                                    // If current user is employee, filter out department-heads from siblings (already handled in model, but double-check for safety)
                                    if ($user->hasRole('employee')) {
                                        $siblings = $siblings->filter(fn($s) => !$s->hasRole('department-head'));
                                    }
                                @endphp
                                @foreach ($siblings as $sibling)
                                    <li>
                                        <x-user-bubble :user="$sibling" :isCurrent="$sibling->id === $user->id" />
                                        @if ($sibling->id === $user->id && $user->subordinateUsers()->isNotEmpty())
                                            <ul>
                                                @foreach ($user->subordinateUsers() as $subordinate)
                                                    <li>
                                                        <x-user-bubble :user="$subordinate" />
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li>
                            <x-user-bubble :user="$user" :isCurrent="true" />
                            @if ($user->subordinateUsers()->isNotEmpty())
                                <ul>
                                    @foreach ($user->subordinateUsers() as $subordinate)
                                        <li>
                                            <x-user-bubble :user="$subordinate" />
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
