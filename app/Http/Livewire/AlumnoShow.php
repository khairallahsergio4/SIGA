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
        
            $alumnos=Alumno::where('apellidos','like','%'.$this->search.'%')->orWhere('nombres','like','%'.$this->search.'%')->orWhere('dni','like','%'.$this->search.'%')->orWhere('legajo','like','%'.$this->search.'%')->Paginate(10);
           
            $ultimaInscripcion = DB::table('inscripcions')
         ->select('alumno_id', DB::raw('MAX(id) as ultima_id'))
            ->groupBy('alumno_id');

            $alumnos = DB::table('alumnos')     ->join('inscripcions', 'alumnos.id', '=', 'inscripcions.alumno_id')

                ->where('apellidos', 'like', '%' . $this->search . '%')
                ->orWhere('nombres', 'like', '%' . $this->search . '%')
                ->orWhere('dni', 'like', '%' . $this->search . '%')
                ->orWhere('legajo', 'like', '%' . $this->search . '%')
                ->paginate(10);



     
        }else{  
            //Sino se filtra de acuerdo a lo seleccionado en el select

            $alumnos=DB::table('alumnos')->where('baja',$this->filtro)->Paginate(20);
        }
        //retorna a  la vista 
        return view('livewire.alumno-show',compact('alumnos'));
    }
}
