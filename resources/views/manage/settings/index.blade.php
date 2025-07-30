<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="container px-6 py-8 mx-auto">
        <h2 class="text-xl font-semibold text-gray-700 leading-tight">{{ __('settings.hub') }}</h2>
        <p class="text-gray-600 mt-2">{{ __('settings.manage_all_aspects') }}</p>

        <!-- Quick Actions Section -->
        <div class="mt-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('settings.quick_actions') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('manage.users.create') }}"
                            class="inline-flex items-center justify-center px-4 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-user-plus me-2"></i>{{ __('settings.add_new_user') }}
                        </a>
                        <a href="{{ route('manage.departments.create') }}"
                            class="inline-flex items-center justify-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-building me-2"></i>{{ __('settings.add_department') }}
                        </a>
                        <a href="{{ route('manage.elections.create') }}"
                            class="inline-flex items-center justify-center px-4 py-2 border border-yellow-300 text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <i class="fas fa-vote-yea me-2"></i>{{ __('settings.create_election') }}
                        </a>
                        <a href="{{ route('manage.forms.create') }}"
                            class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-clipboard-list me-2"></i>{{ __('settings.create_form') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
            <!-- User Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-primary-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.user_management') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_users_roles_permissions') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.users.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{ __('settings.manage_users') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Department Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.departments') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_organizational_structure') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.departments.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            {{ __('settings.manage_departments') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Elections Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-vote-yea text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.elections') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_voting_elections') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.elections.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            {{ __('settings.manage_elections') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Forms Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.forms') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_dynamic_forms_fields') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.forms.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('settings.manage_forms') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
            <!-- System Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cog text-gray-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.system_settings') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.configure_system_preferences') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.settings.edit') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            {{ __('settings.system_settings') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Attendance Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-red-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.attendance_settings') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.configure_attendance_policies') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.attendance-settings.edit') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ __('settings.attendance_settings') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bars text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.sidebar_links') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_navigation_menu') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.sidebar-links.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            {{ __('settings.manage_links') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Roles & Permissions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gray-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.roles_permissions') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_access_control') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.roles.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            {{ __('settings.manage_roles') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Maintenance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tools text-orange-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.system_maintenance') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_logs_cache') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.maintenance.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            {{ __('settings.system_maintenance') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Evaluation Criteria -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-teal-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('settings.evaluation_criteria') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_evaluation_methods') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.evaluation-criteria.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            {{ __('settings.manage_evaluation_criteria') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Static Pages -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-indigo-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('static-pages.title') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('settings.manage_static_pages') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manage.static-pages.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('settings.manage_static_pages') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>



    </div>
</x-app-layout>
