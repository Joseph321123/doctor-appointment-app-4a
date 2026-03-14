<div class="flex items-center space-x-2">

    <x-wire-button href="{{ route('admin.appointments.consultation', $appointment) }}" positive xs>
        <i class="fa-solid fa-stethoscope"></i>
    </x-wire-button>

    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    <form id="delete-appointment-{{ $appointment->id }}" action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST">
        @csrf
        @method('DELETE')
        <x-wire-button type="button" negative xs
            onclick="Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará la cita permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-appointment-{{ $appointment->id }}').submit();
                }
            })">
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>

</div>
