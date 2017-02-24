<?php
namespace Flexio\Modulo\OrdenesVentas\Repository;

use Flexio\Modulo\OrdenesVentas\Models\OrdenVenta;

class RepositoryOrdenVenta{

  public $builder;

  function __construct(){
    $this->builder = (new OrdenVenta)->newQuery();
  }

  function getOrdenes($empresa_id){

     $this->builder->where('empresa_id',$empresa_id);
     return $this;
  }

  function sinOrdenTrabajo(){
      $this->builder->has('orden_trabajo','<','1');
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
