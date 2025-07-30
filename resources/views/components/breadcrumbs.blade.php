@if (isset($breadcrumbs))
    <div>
        <nav class="text-black font-bold" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                @foreach ($breadcrumbs as $url => $name)
                    @if (!is_array($name))
                        <li class="flex items-center">
                            <a href="{{ $url }}" class="text-primary-600">{{ (string) $name }}</a>
                            @if (!$loop->last)
                                <i class="fas fa-chevron-left mx-2 text-xs"></i>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
@endif
