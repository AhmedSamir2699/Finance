@props([
    'name'
])



<div x-show="{{$name}}" 
id="modal-backdrop-{{$name}}"
class="fixed inset-0 z-[99] grid h-screen w-screen place-items-center bg-black bg-opacity-60 backdrop-blur-sm transition-opacity duration-300"
style="display:none">
<div @mousedown.outside="{{$name}} = false"
    class="relative m-4 p-4 w-3/4 md:w-2/5 min-w-[40%] md:max-w-[40%] rounded-lg bg-white shadow-sm z-[999]">

    {{ $slot }}
</div>
</div>