<div class="position-relative">

    <input
        type="text"
        wire:model.live="busqueda"
        class="form-control"
        placeholder="Ingrese apellido o nombre"
        autocomplete="off"
    >

    @if(count($alumnos) > 0)

        <div class="list-group position-absolute w-100" style="z-index: 1000;">

            @foreach($alumnos as $alumno)

                <button
                    type="button"
                    class="list-group-item list-group-item-action"
                    wire:click="seleccionarAlumno({{ $alumno->id }})"
                >
                    <strong>
                        {{ $alumno->apellidos }}, {{ $alumno->nombres }}
                    </strong>

                    <br>

                    <small class="text-muted">
                        DNI: {{ $alumno->dni }}
                    </small>
                </button>

            @endforeach

        </div>

    @endif

    {{-- Este es el DNI que realmente recibe el formulario --}}
    <input
        type="hidden"
        name="{{ $nombreCampo }}"
        value="{{ $dni }}"
    >

</div>