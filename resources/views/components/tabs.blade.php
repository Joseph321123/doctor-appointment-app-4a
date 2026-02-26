@props(['active' => 'datos-personales'])

<div x-data="{ tab: @js($active) }">

    @isset($header) <!-- Si se ha definido un header, se muestra el menú de navegación -->
    <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            {{ $header }}
        </ul>
    </div>
    @endisset

    <div class="px-4 mt-4">
        {{ $slot }}
    </div>

</div>
