<?php
namespace Flexio\Modulo\OrdenesAlquiler\Repository;

use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler;

class RepositorioOrdenesAlquiler{

  public $builder;

  function __construct(){
    $this->builder = (new OrdenVentaAlquiler)->newQuery();
  }

  function getOrdenes($empresa_id){

     $this->builder->where('empresa_id',$empresa_id);
     return $this;
  }

  function porFacturar(){

    $this->builder->where('estado','por_facturar');
    return $this;
  }

  function conId($id){
        $this->builder->where('id',$id);
        return $this;
  }

  function fetch(){
    return $this->builder->get();
  }
}
