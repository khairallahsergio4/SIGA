<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Alumno;

class BuscarAlumno extends Component
{
    public $nombreCampo;
    public $busqueda = '';
    public $dni = '';
    public $alumnoSeleccionado = null;

    public function mount($nombreCampo)
    {
        $this->nombreCampo = $nombreCampo;
    }

    public function updatedBusqueda()
    {
        $this->alumnoSeleccionado = null;
        $this->dni = '';
    }

    public function seleccionarAlumno($id)
    {
        $alumno = Alumno::find($id);

        if ($alumno) {
            $this->busqueda = $alumno->apellidos . ' ' . $alumno->nombres;
            $this->dni = $alumno->dni;
            $this->alumnoSeleccionado = $alumno;
        }
    }

    public function render()
    {
        $alumnos = collect();

        if (strlen($this->busqueda) >= 2 && !$this->alumnoSeleccionado) {

            $alumnos = Alumno::where(function ($query) {
                $query->where('apellidos', 'like', '%' . $this->busqueda . '%')
                    ->orWhere('nombres', 'like', '%' . $this->busqueda . '%')->orWhere('dni', 'like', '%' . $this->busqueda . '%');
            })
            ->orderBy('apellidos')
            ->limit(10)
            ->get();
        }

        return view('livewire.buscar-alumno', [
            'alumnos' => $alumnos
        ]);
    }
}