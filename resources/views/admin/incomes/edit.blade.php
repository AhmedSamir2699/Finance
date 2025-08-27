<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="container mt-4 mx-auto">
        <form method="POST" action="{{ route('incomes.update', $income) }}" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-6 rounded-md shadow-md">
            @csrf
           

            {{-- Income Category --}}
            <div class="col-span-2">
                <label for="income_category_id" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.income_category') }}
                </label>
                <select name="income_category_id" id="income_category_id"
                        class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('income_category') border-red-500 @enderror">
                    @foreach($income_categories as $id => $entry)
                        <option value="{{ $id }}" {{ old('income_category_id',$income->income_category_id)==$id ? 'selected' : '' }}>
                            {{ $entry }}
                        </option>
                    @endforeach
                </select>
                @error('income_category') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.income_category_helper') }}</p>
            </div>

            {{-- Entry Date --}}
            <div class="col-span-1">
                <label for="entry_date" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.entry_date') }} <span class="text-red-500">*</span>
                </label>
                <input type="date" name="entry_date" id="entry_date"
                       value="{{ old('entry_date', optional($income->entry_date)->format('Y-m-d')) }}"
                       class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('entry_date') border-red-500 @enderror"
                       required>
                @error('entry_date') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.entry_date_helper') }}</p>
            </div>

            {{-- Amount --}}
            <div class="col-span-1">
                <label for="amount" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.amount') }} <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" step="0.01"
                       value="{{ old('amount', $income->amount) }}"
                       class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('amount') border-red-500 @enderror"
                       required>
                @error('amount') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.amount_helper') }}</p>
            </div>

            {{-- Description --}}
            <div class="col-span-2">
                <label for="description" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.description') }}
                </label>
                <input type="text" name="description" id="description"
                       value="{{ old('description', $income->description) }}"
                       class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('description') border-red-500 @enderror">
                @error('description') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.description_helper') }}</p>
            </div>

            {{-- Percent allocations --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 py-2">
                    {{ __('Allocate by percentage (≤ 100% allowed)') }}
                </label>

                @php
                    $allocationsPrefill = $allocationsPrefill ?? $income->allocations->map(fn($a) => [
                        'finance_item_id' => $a->finance_item_id,
                        'percentage'      => (float)$a->percentage,
                        'amount'          => (float)$a->amount,
                    ])->values();
                    $allocPayload = ['amount' => (float)$income->amount, 'prefill' => $allocationsPrefill];
                @endphp

                <div
                    x-data="allocationsEdit(@js($allocPayload))"
                    x-init="init()"
                    class="space-y-3"
                >
                    <template x-for="(row, idx) in rows" :key="row.key">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                            <div class="md:col-span-7">
                                <select
                                    class="rounded border border-gray-300 px-3 py-2 w-full"
                                    :name="`allocations[${idx}][finance_item_id]`"
                                    x-model="row.finance_item_id"
                                >
                                    <option value="">{{ __('Select finance item') }}</option>
                                    @foreach($finance_items as $id => $entry)
                                        <option value="{{ $id }}">{{ $entry }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <input type="number" step="0.001" min="0"
                                       class="rounded border border-gray-300 px-3 py-2 w-full"
                                       :name="`allocations[${idx}][percentage]`"
                                       x-model.number="row.percentage"
                                       @input="recalc()"
                                       placeholder="%">
                            </div>
                            <div class="md:col-span-2 text-right text-xs text-slate-600">
                                <span>{{ __('≈ Amount:') }}</span>
                                <span x-text="previewAmount(row).toFixed(2)"></span>
                                <button type="button" class="ml-3 text-red-600 text-sm font-medium hover:underline"
                                        @click="remove(idx)">
                                    {{ __('Remove') }}
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-between">
                        <button type="button" @click="add()"
                                class="bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold py-1.5 px-3 rounded">
                            + {{ __('Add allocation') }}
                        </button>

                        <div class="text-sm">
                            <span class="font-medium">{{ __('Total') }}:</span>
                            <span x-text="total.toFixed(3)"></span>% /
                            <span class="font-medium">{{ __('Income') }}:</span>
                            <span x-text="amount.toFixed(2)"></span>
                            <span x-show="total > 100.01" class="text-red-600">
                                ({{ __('must be ≤ 100%') }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="col-span-2 flex justify-end mt-4">
                <button type="submit"
                        class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:ring focus:ring-primary-300">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>

    <script>
        function allocationsEdit(payload) {
            return {
                amount: 0,
                rows: [],
                total: 0,

                init() {
                    this.amount = Number(payload?.amount ?? 0);

                    const pre = Array.isArray(payload?.prefill) ? payload.prefill : [];
                    if (pre.length) {
                        this.rows = pre.map(p => ({
                            key: Date.now() + Math.random(),
                            finance_item_id: String(p.finance_item_id ?? ''),
                            percentage: Number(p.percentage ?? 0),
                            amount: Number(p.amount ?? 0),
                        }));
                    } else {
                        this.rows = [{ key: Date.now(), finance_item_id: '', percentage: 100.000, amount: 0 }];
                    }
                    this.recalc();
                },

                add() {
                    this.rows.push({ key: Date.now() + Math.random(), finance_item_id: '', percentage: 0.000, amount: 0 });
                    this.recalc();
                },

                remove(i) {
                    this.rows.splice(i, 1);
                    this.recalc();
                },

                recalc() {
                    this.total = this.rows.reduce((s, r) => {
                        const v = Number(r.percentage);
                        return s + (isFinite(v) ? v : 0);
                    }, 0);
                },

                previewAmount(row) {
                    const pct = Number(row.percentage || 0);
                    const base = Number(this.amount || 0);
                    return (base * pct / 100);
                },
            }
        }

        // Optional: your other scripts (flatpickr etc.)
        flatpickr("#due_date", { dateFormat: "Y-m-d" });
        flatpickr("#task_date", { dateFormat: "Y-m-d" });
    </script>

    {{-- Select2 (if needed elsewhere) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-selection--multiple {
            background-color: #fff; border-radius: .375rem; border: 1px solid #cbd5e1; min-height: 2.5rem;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3b82f6; color:#fff; border:none; border-radius:.375rem; padding-left:1.2em!important; padding-right:1.2em!important; margin-top:.25rem;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color:#fff!important; border-radius:50%; margin-right:.5rem; font-weight:bold; font-size:1.1em; padding:0 .4em; transition:background .2s;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background:#dc2626!important; color:#fff!important;
        }
    </style>
</x-app-layout>