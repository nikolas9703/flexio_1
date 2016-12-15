<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaProveedor;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
class FacturaCompraReporte {

  private $empresa_id;
  private $proveedor_id;
  private $fecha_inicio;
  private $fecha_final;
  protected $factura;
  protected $proveedor;

  function __construct($datos, $proveedor){
    $this->empresa_id = $datos['empresa_id'];
    $this->proveedor_id = $datos['proveedor'];
    $this->fecha_inicio = $datos['fecha_inicio'];
    $this->fecha_final = $datos['fecha_final'];
    $this->factura = new FacturaCompra;
    $this->proveedor = $proveedor;
  }

  function balance_inicial(){
  return  $this->factura->where(function($query){
      $query->where('proveedor_id',$this->proveedor_id);
      $query->where('empresa_id',$this->empresa_id);
      $query->where('created_at','<=',$this->fecha_inicio);
      $query->whereIn('estado_id',[13,14,15,16]);
    })->sum('total');
  }



  function facturado(){
    return $this->factura->where(function($query){
      $query->where('proveedor_id',$this->proveedor_id);
      $query->where('empresa_id',$this->empresa_id);
      $query->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_final]);
      $query->whereIn('estado_id',[13,14,15,16]);
    })->sum('total');
  }

  function pagado(){
  return  $this->factura->where(function($query){
      $query->where('proveedor_id',$this->proveedor_id);
      $query->where('empresa_id',$this->empresa_id);
      $query->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_final]);
      $query->whereIn('estado_id',[15,16]);
    })->sum('total');
  }

  function notas_debito(){
    
    return $this->proveedor->notaDebito()->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_final])->sum('total');
  }

  function balance_final(){
    return  $this->balance_inicial() + $this->facturado() + $this->notas_debito() - $this->pagado();
  }

  function detalle(){
  return $this->factura->where(function($query){
      $query->where('proveedor_id',$this->proveedor_id);
      $query->where('empresa_id',$this->empresa_id);
      $query->whereBetween('created_at',$this->fecha_inicio,$this->fecha_final);
      $query->whereIn('estado_id',[14]);
    })->get();
  }



}
