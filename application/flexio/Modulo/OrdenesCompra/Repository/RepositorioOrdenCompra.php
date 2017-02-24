<?php
namespace Flexio\Modulo\OrdenesCompra\Repository;

use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra;

class RepositorioOrdenCompra{

  public $builder;

  function __construct(){
    $this->builder = (new OrdenesCompra)->newQuery();
  }

  function conId($id){
        $this->builder->where('id',$id);
        return $this;
  }

  function getOrdenes($empresa_id){

     $this->builder->where('id_empresa',$empresa_id);
     return $this;
  }

  function conProveedorActivo(){
        $this->builder->whereHas('proveedor_relacion',function($query){
          $query->where('estado','activo');
        });
        return $this;
    }


  function porFacturar(){

    $this->builder->where('id_estado',2);
    return $this;
  }


  function fetch(){
    return $this->builder->get();
  }
}
