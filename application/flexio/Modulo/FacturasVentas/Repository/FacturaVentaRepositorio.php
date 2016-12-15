<?php

namespace Flexio\Modulo\FacturasVentas\Repository;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;

class FacturaVentaRepositorio{

    public $builder;

    function __construct(){
      $this->builder = (new FacturaVenta)->newQuery();
    }

    function getFacturas($empresa_id){

       $this->builder->where('empresa_id',$empresa_id);
       return $this;
    }

    function conId($id){
        $this->builder->where('id',$id);
        return $this;
    }

    function conUUID($uuid){
        $this->builder->where('uuid_factura', hex2bin($uuid));
        return $this;
    }

    function paraCobrar(){

        $this->builder->where(function($query){
            $query->where('estado','por_cobrar')
                  ->orWhere('estado','cobrado_parcial');
        });
        return $this;

    }

    function conClienteActivo(){
      $this->builder->whereHas('cliente',function($query){
        $query->where('estado','activo');
      });
      return $this;
    }

    function porCobrar(){
        $this->builder->where('estado','por_cobrar');
        return $this;
    }

    function cobradoParcial(){
        $this->builder->where('estado','cobrado_parcial');
        return $this;
    }

    function fetch(){
      return $this->builder->get();
    }


}
