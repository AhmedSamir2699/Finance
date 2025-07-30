@props(['user', 'isCurrent' => false])

@if ($isCurrent)
    <div class="user-bubble bg-gray-100 border-2 border-blue-200 cursor-default pointer-events-none inline-block mx-auto p-4 rounded-lg" style="box-shadow: 0 2px 8px 0 #e0e7ef33;">
        @if ($user->profile_picture)
            <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-16 h-16 rounded-full mx-auto border-2 border-gray-300">
        @else
            <img src="https://ui-avatars.com/api/?length=1&background=random&name={{ $user->name }}" class="w-16 h-16 rounded-full mx-auto border-2 border-gray-300" alt="">
        @endif
        <p class="font-bold mt-2 text-blue-900">{{ $user->name }}</p>
        <p class="text-sm text-gray-500">{{ $user->department->name ?? '' }}</p>
    </div>
@else
    <a href="{{ route('users.show', $user) }}" class="user-bubble hover:bg-gray-50 transition p-4 rounded-lg">
        @if ($user->profile_picture)
            <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-16 h-16 rounded-full mx-auto border-2 border-gray-300">
        @else
            <img src="https://ui-avatars.com/api/?length=1&background=random&name={{ $user->name }}" class="w-16 h-16 rounded-full mx-auto border-2 border-gray-300" alt="">
        @endif
        <p class="font-bold mt-2">{{ $user->name }}</p>
        <p class="text-sm text-gray-500">{{ $user->department->name ?? '' }}</p>
    </a>
@endif 