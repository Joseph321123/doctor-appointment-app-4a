@props(['tab', 'error' => false])

<li class="me-2">
    <a href="#"

       x-on:click.prevent="tab = '{{ $tab }}'"
       class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
       :class="{
            'text-blue-600 border-blue-600': tab === '{{ $tab }}' && !@js($error),
            'text-red-600 border-red-600': @js($error),
            'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== '{{ $tab }}' && !@js($error)
       }">

        {{ $slot }}

        @if($error)
            <i class="fa-solid fa-circle-exclamation text-sm text-red-600 ms-2 animate-pulse"></i>
        @endif
    </a>
</li>
