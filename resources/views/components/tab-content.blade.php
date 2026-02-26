@props(['tab'])

<div x-show="tab === '{{ $tab }}'" >
    {{ $slot }}
</div>
