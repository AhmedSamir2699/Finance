<x-app-layout>
    <div class="space-y-8">
        <!-- Top Row Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-3 md:gap-6">
            <div class="col-span-2">
                <livewire:dashboard.user-info />
            </div>

            <livewire:dashboard.bank-account />

        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column: incomes -->
            <div class="">
                <x-dashboard-table :title="__('اليوم - الإيرادات')">
                    <x-slot name="header">
                        <th class="px-5 py-3">التصنيف</th>
                        <th class="px-5 py-3">المبلغ</th>
                        <th class="px-5 py-3">الوصف</th>
                    </x-slot>
                    @forelse($todaysIncomes as $income)
                        <tr>
                            <td class="px-5 py-5 text-start">{{ optional($income->income_category)->name }}</td>
                            <td class="px-5 py-5 text-start text-green-600 font-semibold">
                                {{ number_format($income->amount, 2) }} س.ر</td>
                            <td class="px-5 py-5 text-start">{{ $income->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-5">لا توجد إيرادات اليوم</td>
                        </tr>
                    @endforelse
                </x-dashboard-table>
            </div>

            <!-- Right Column: Expenses -->
            <div class="">
                <x-dashboard-table :title="__('اليوم - المصروفات')">
                    <x-slot name="header">
                        <th class="px-5 py-3">التصنيف</th>
                        <th class="px-5 py-3">المبلغ</th>
                        <th class="px-5 py-3">الوصف</th>
                    </x-slot>
                    @forelse($todaysExpenses as $expense)
                        <tr>
                            <td class="px-5 py-5 text-start">{{ optional($expense->expense_category)->name }}</td>
                            <td class="px-5 py-5 text-start text-red-600 font-semibold">
                                {{ number_format($expense->amount, 2) }} س.ر</td>
                            <td class="px-5 py-5 text-start">{{ $expense->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-5">لا توجد مصروفات اليوم</td>
                        </tr>
                    @endforelse
                </x-dashboard-table>
            </div>

        </div>
    </div>
</x-app-layout>
