<div>
{{-- checkable elements for the allowed departments to view this form --}}
<div class="flex flex-col">
    <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($departments as $department)
            <div class="flex items-center border border-primary-base p-3 rounded">
                <input id="department-{{ $department->id }}" name="departments[]" type="checkbox" value="{{ $department->id }}"
                    class="focus:ring-primary-500 h-4 w-4 text-primary-600 border border-primary-base p-3 rounded"
                    @if ($form->departments->pluck('department_id')->contains($department->id)) checked @endif wire:change="updateVisibility({{ $department->id }})" /
                    >
                <label for="department-{{ $department->id }}" class="mx-3 block text-lg font-medium text-gray-700">
                    {{ $department->name }}
                </label>
            </div>
        @endforeach
    </div>
</div>
