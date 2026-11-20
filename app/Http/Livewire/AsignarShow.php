<?php

namespace App\Http\Livewire;

use App\Models\Ciclo;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CursoDivision;
use App\Models\AsignarDivision;
use Illuminate\Support\Facades\Cache;

class AsignarShow extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    //definiendo las propiedades
    public $search;
    public $año;
    public $cursos;
    public $ciclos;
   

    public function updatingSearch()
    {   //cuando se actualiza la busqueda no queda desfasada la paginacion
        $this->resetPage();
    }

    public function mount(){
        //Buscando la seccion y los ciclos en cache   
        if (Cache::has('cursos_divisiones')) {
            $this->cursos=Cache::get('cursos_divisiones');
        } else { 
            $this->cursos=CursoDivision::all();
            Cache::put('cursos_divisiones',$this->cursos,14400); 
        }
  
        if (Cache::has('ciclos')) {
            $this->ciclos=Cache::get('ciclos');
        } else { 
            $this->ciclos=Ciclo::orderBy('ciclo','DESC')->get();
            Cache::put('ciclos',$this->ciclos,14400); 
        }
                
    }

    public function render()
    {
        //buscando el ciclo activo en cache
        if (Cache::has('ciclo_activo')) {
            $ciclo=Cache::get('ciclo_activo');
        } else { 
            $ciclo=Ciclo::all()->where('estado','ACTIVO')->last();
            Cache::put('ciclo_activo',$ciclo,14400); 
        }

        

      // Busqueda original $asignaciones=AsignarDivision::where('grupo_id',$this->search)->where('ciclo_id',$this->año)->where('grupo_id','!=',null)->Paginate(10);

      //buscando los alumnos que tienen una seccion asignada. Mejorada para que muestre por orden de apellidos
      $asignaciones = AsignarDivision::query()
    ->join('inscripcions', 'asignar_divisions.inscripcion_id', '=', 'inscripcions.id')
    ->join('alumnos', 'inscripcions.alumno_id', '=', 'alumnos.id')

    ->where('asignar_divisions.grupo_id', $this->search)
    ->where('asignar_divisions.ciclo_id', $this->año)
    ->whereNotNull('asignar_divisions.grupo_id')

    // ORDEN: primero sexo (M antes que F), luego apellido y nombre
    ->orderByRaw("FIELD(alumnos.sexo, 'M', 'F', 'OTRO')")
    ->orderBy('alumnos.apellidos', 'asc')
    ->orderBy('alumnos.nombres', 'asc')

    ->select('asignar_divisions.*') // importante para no romper el modelo
    ->paginate(10);
   
        //retornando la vista
        return view('livewire.asignar-show',["cursos"=>$this->cursos,"ciclos"=>$this->ciclos,"asignaciones"=>$asignaciones]);
    }
}
