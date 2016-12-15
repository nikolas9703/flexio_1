<?php
namespace Flexio\Modulo\Cliente\Repository;

use Flexio\Modulo\Cliente\Models\Cliente;

class ClienteRepositorio {

  public $builder;

  function __construct(){
    $this->builder = (new Cliente)->newQuery();
  }

  function getClientes($empresa_id){
    $this->builder->where('empresa_id',$empresa_id);
    return $this;
  }

  function conId($id){
    $this->builder->where('id',$id);
    return $this;
  }

  function activos(){
    $this->builder->where('estado', '=', 'activo');
    return $this;
  }

  function conFacturasPorCobrar(){
    $this->builder->whereHas('facturas',function($query){
      $query->where('estado','por_cobrar');
    });
    return $this;
  }
  function conFacturasCobradoParcial(){
    $this->builder->whereHas('facturas',function($query){
      $query->where('estado','cobrado_parcial');
    });
    return $this;
  }
  function conFacturas(){
      $this->builder->whereHas('facturas',function($query){
        $query->whereIn('estado',['por_cobrar','cobrado_parcial']);
      });
      return $this;
  }
  function paraCrearCobros(){
    $this->builder->with(['facturas' =>function($query){
      $query->whereIn('estado',['por_cobrar','cobrado_parcial']);
    }]);
    return $this;
  }
  function fetch(){
    return $this->builder->get();
  }

}
