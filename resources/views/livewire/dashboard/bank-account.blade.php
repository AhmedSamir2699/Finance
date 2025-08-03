@php
    $banck_account = (float) \App\Models\Setting::where('key', 'bank_account')->pluck('value')->first();
@endphp

<div wire:poll.15000ms class="w-full px-6 mt-3 md:mt-0 block text-center">
    <div @class([
        'p-6 rounded-lg shadow-lg border',
        'bg-green-100 border-green-200' => $banck_account >= 6000000,
        'bg-gray-100 border-gray-200' => $banck_account <= 600000 && $banck_account > 300000,
        'bg-red-100 border-red-200' => $banck_account <= 300000,
    ])>
        <div class="flex justify-between items-center mb-4">
            <div class="text-sm text-gray-600">
                <span class="font-semibold">الرصيد البنكي:</span> 
            </div>

            <div @class([
                'px-3 py-1 text-xs font-semibold rounded-full',
                'bg-green-200 text-green-800' => $banck_account >= 6000000,
                'bg-gray-200 text-gray-800' => $banck_account <= 600000 && $banck_account > 300000,
                'bg-red-200 text-red-800' => $banck_account <= 300000,
            ])>
                @if ($banck_account >= 6000000)
                    ممتاز
                @elseif ($banck_account <= 300000)
                    سيئ
                @else
                    متوسط
                @endif
            </div>
        </div>

        <div class="text-center my-6">
            <div class="text-4xl font-bold text-gray-800">
                {{ number_format($banck_account, 2) }} ر.س
            </div>
        </div>
    </div>
</div>
