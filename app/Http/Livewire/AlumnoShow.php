<?php

namespace App\Http\Livewire;

use App\Models\Alumno;
use App\Models\Inscripcion;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;


class AlumnoShow extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    //definiendo las propiedades: search cuadro de busq , filtro alumnos en baja, etc
    public $search;
    public $filtro;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        //Si el filtro no se selecciono, se busca por el cuadro de busqueda
        if($this->filtro==null){
        
    // Codigo anterior $alumnos=Alumno::where('apellidos','like','%'.$this->search.'%')->orWhere('nombres','like','%'.$this->search.'%')->orWhere('dni','like','%'.$this->search.'%')->orWhere('legajo','like','%'.$this->search.'%')->Paginate(10);
           

    $alumnos = Alumno::query()

    // ÚLTIMA INSCRIPCIÓN POR ALUMNO
    ->joinSub(
        DB::table('inscripcions')
            ->select('alumno_id', DB::raw('MAX(id) as ultima_inscripcion'))
            ->groupBy('alumno_id'),
        'ult_insc',
        'alumnos.id',
        '=',
        'ult_insc.alumno_id'
    )

    ->join('inscripcions', 'inscripcions.id', '=', 'ult_insc.ultima_inscripcion')
    ->leftJoin('asignar_divisions', 'asignar_divisions.inscripcion_id', '=', 'inscripcions.id')
    ->leftJoin('curso_divisions', 'curso_divisions.id', '=', 'asignar_divisions.grupo_id')
    ->leftJoin('cursos', 'cursos.id', '=', 'curso_divisions.curso_id')
    ->leftJoin('divisions', 'divisions.id', '=', 'curso_divisions.division_id')
    ->leftJoin('ciclos', 'ciclos.id', '=', 'inscripcions.ciclo_id')

    // BUSCADOR (AGRUPADO PARA QUE NO ROMPA EL JOIN)
    ->where(function($q){
        $q->where('alumnos.apellidos','like','%'.$this->search.'%')
          ->orWhere('alumnos.nombres','like','%'.$this->search.'%')
          ->orWhere('alumnos.dni','like','%'.$this->search.'%')
          ->orWhere('alumnos.legajo','like','%'.$this->search.'%');
    })

    // SELECT CON DATOS EXTRA
    ->select(
        'alumnos.*',
        DB::raw("CONCAT(cursos.curso, divisions.division) as curso_division"),
        'ciclos.ciclo as ciclo_lectivo'
    )

    ->paginate(10);



        }else{  
            //Sino se filtra de acuerdo a lo seleccionado en el select

            $alumnos=Alumno::where('baja',$this->filtro)->Paginate(10);
        }
        //retorna a  la vista 
        return view('livewire.alumno-show',compact('alumnos'));
    }
}
