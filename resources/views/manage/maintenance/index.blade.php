<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="container px-6 py-8 mx-auto">
        <h2 class="text-xl font-semibold text-gray-700 leading-tight">{{ __('maintenance.system_maintenance') }}</h2>
        <p class="text-gray-600 mt-2">{{ __('maintenance.manage_system_logs_cache') }}</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <!-- Log Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 col-span-2">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-red-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('maintenance.log_management') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('maintenance.view_clear_laravel_logs') }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('maintenance.log_content') }}</label>
                            <div class="bg-gray-50 border border-gray-300 rounded-md p-4 h-64 overflow-y-auto">
                                <pre id="logContent" class="text-xs text-gray-800 whitespace-pre-wrap" dir="ltr">{{ __('maintenance.loading_logs') }}</pre>
                            </div>
                        </div>

                        <div class="flex space-x-3 gap-3">
                            <button onclick="loadLogContent()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-sync-alt me-2"></i>{{ __('maintenance.refresh_logs') }}
                            </button>

                            <form method="POST" action="{{ route('manage.maintenance.clear-log') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('{{ __('maintenance.confirm_clear_log') }}')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-trash me-2"></i>{{ __('maintenance.clear_log') }}
                                </button>
                            </form>

                            <button onclick="debugEnvironment()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <i class="fas fa-bug me-2"></i>Debug Environment
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-database text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ms-4 flex-1">
                                <h3 class="text-lg font-medium text-gray-900">{{ __('maintenance.cache_management') }}
                                </h3>
                                <p class="text-sm text-gray-500">{{ __('maintenance.clear_system_cache') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">

                            <form method="POST" action="{{ route('manage.maintenance.clear-cache') }}">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('{{ __('maintenance.confirm_clear_cache') }}')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <i class="fas fa-broom me-2"></i>{{ __('maintenance.clear_all_cache') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Settings Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cogs text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ms-4 flex-1">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ __('maintenance.settings_management') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('maintenance.reset_system_settings') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-purple-50 border border-purple-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="ms-3">
                                        <div class="mt-2 text-sm text-purple-700">
                                            <p>
                                                <i class="me-2 fas fa-info-circle text-purple-400"></i>
                                                
                                                {{ __('maintenance.settings_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('manage.maintenance.reset-settings') }}">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('{{ __('maintenance.confirm_reset_settings') }}')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <i class="fas fa-undo me-2"></i>{{ __('maintenance.reset_settings') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Cache Management -->


        </div>
    </div>

    <script>
        function loadLogContent() {
            fetch('{{ route('manage.maintenance.get-log') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('logContent').textContent = data.content;
                })
                .catch(error => {
                    document.getElementById('logContent').textContent = '{{ __('maintenance.log_read_failed') }}';
                });
        }

        function debugEnvironment() {
            fetch('{{ route('manage.maintenance.debug-env') }}')
                .then(response => response.json())
                .then(data => {
                    let debugInfo = 'Environment Debug Information:\n\n';
                    for (let [key, value] of Object.entries(data)) {
                        debugInfo += `${key}: ${value}\n`;
                    }
                    document.getElementById('logContent').textContent = debugInfo;
                })
                .catch(error => {
                    document.getElementById('logContent').textContent = 'Failed to load environment debug information';
                });
        }

        // Load log content on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadLogContent();
        });
    </script>
</x-app-layout>
