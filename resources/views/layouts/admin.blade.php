@props([
    'title'=> config('app.name', 'Laravel'),
    'breadcrumbs' => []])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title}}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/6f39a30c18.js" crossorigin="anonymous"></script>

    {{-- Sweet Alert 2--}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{--WireUI--}}
    <wireui:scripts />
    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">

@include('layouts.includes.admin.navigation')


@include('layouts.includes.admin.sidebar')

<div class="p-4 sm:ml-64">
    <!-- Margin top 14px-->
    <div class="mt-14 flex items-center justify-between w-full">
        @include('layouts.includes.admin.breadcrumb')
    </div>
    <main>
        <div class="max-w-7xl mx-auto">

            {{-- Título y botón de acción --}}
            <div class="flex justify-between items-center mb-6">
                @if (isset($title))
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                        {{ $title }}
                    </h1>
                @endif

                @if (isset($action))
                    <div>
                        {{ $action }}
                    </div>
                @endif
            </div>

        </div>
    {{$slot}}
</div>


@stack('modals')

@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

{{--Mostrar Sweet Alert--}}
@if (@session('swal'))

    <script>
        Swal.fire(@json(session('swal')));
    </script>

@endif

<script>
    //Buscar todos los elementos de una clase especifica
    forms = document.querySelectorAll('.delete-form');
    forms.forEach(form => {
        //Se pone al pendiente de cualquier accion submit (Activa el modo chismoso)
        form.addEventListener('submit', function (e){
            //evita que se envie
            e.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed){
                    //Borrar el registro
                    form.submit();
                }
            });
        })
    });
</script>

</body>
</html>
