<?php

namespace Flexio\Modulo\OrdenesTrabajo\Repository;
use Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo;

class OrdenTrabajoRepositorio{

    public $builder;

    function __construct(){
      $this->builder = (new OrdenTrabajo)->newQuery();
    }

    function getOrdenesTrabajos($empresa_id){

       $this->builder->where('empresa_id',$empresa_id);
       return $this;
    }

    function conId($id){
        $this->builder->where('id',$id);
        return $this;
    }

    function conUUID($uuid){
        $this->builder->where('uuid_orden_trabajo', hex2bin($uuid));
        return $this;
    }

    function conClienteActivo(){
      $this->builder->whereHas('cliente',function($query){
        $query->where('estado','activo');
      });
      return $this;
    }

    function facturado(){
        $this->builder->where('estado_id',15);
        return $this;
    }

    function debeTenerFacturasParaCobrar(){
        $this->builder->whereHas('facturas',function($query){
          $query->where('estado','cobrado_parcial')
                ->orWhere('estado','por_cobrar');
        });
        return $this;
    }

    function conFacturasParaCobrar(){
        $this->builder->with(['facturas' =>function($query){
          $query->where('estado','cobrado_parcial')
                ->orWhere('estado','por_cobrar');
        }]);
        return $this;
    }

    function fetch(){
      return $this->builder->get();
    }


}
