<?php
namespace Flexio\Modulo\SubContratos\Repository;
use Flexio\Modulo\SubContratos\Models\SubContrato;

class RepositorioSubContrato{

    public $builder;

    function __construct(){
      $this->builder = (new SubContrato)->newQuery();
    }

    function getContratos($empresa_id){

       $this->builder->where('empresa_id',$empresa_id);
       return $this;
    }

    function contratoVigente(){
        $this->builder->where('estado','vigente');
        return $this;
    }

    function conId($id){
        $this->builder->where('id',$id);
        return $this;
    }

    function conProveedorActivo(){
        $this->builder->whereHas('proveedor',function($query){
          $query->where('estado','activo');
        });
        return $this;
    }

    function fetch(){
      return $this->builder->get();
    }
}
