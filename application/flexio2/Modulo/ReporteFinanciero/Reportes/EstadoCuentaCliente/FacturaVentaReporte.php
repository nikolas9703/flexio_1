<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaCliente;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;

class FacturaVentaReporte {

  private $empresa_id;
  private $cliente_id;
  private $fecha_inicio;
  private $fecha_final;
  protected $factura;
  protected $cliente;
  protected $centro_facturacion_id;

  function __construct($datos, $cliente){
    $this->empresa_id = $datos['empresa_id'];
    $this->cliente_id = $datos['cliente'];
    $this->fecha_inicio = $datos['fecha_inicio'];
    $this->fecha_final = $datos['fecha_final'];
    $this->centro_facturacion_id = isset($datos['centro_facturacion_id'])?$datos['centro_facturacion_id']:null;
    $this->factura = new FacturaVenta;
    $this->cliente = $cliente;
  }

  function balance_inicial(){
  return  $this->factura->where(function($query){
      $query->where('cliente_id',$this->cliente_id);
      $query->where('empresa_id',$this->empresa_id);
      $query->where('created_at','<=',$this->fecha_inicio);
      if(!is_null($this->centro_facturacion_id))$query->where('centro_facturacion_id',$this->centro_facturacion_id);
      $query->whereIn('estado',['por_aprobar','por_cobrar','cobrado_parcial','cobrado_completo']);
    })->sum('total');
  }



  function facturado(){
    return $this->factura->where(function($query){
      $query->where('cliente_id',$this->cliente_id);
      $query->where('empresa_id',$this->empresa_id);
      $query->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_final]);
      if(!is_null($this->centro_facturacion_id))$query->where('centro_facturacion_id',$this->centro_facturacion_id);
      $query->whereIn('estado',['por_aprobar','por_cobrar','cobrado_parcial','cobrado_completo']);
    })->sum('total');
  }

  function cobrado(){
    $facturas = $this->factura->where(function($query){
      $query->where('cliente_id',$this->cliente_id);
      $query->where('empresa_id',$this->empresa_id);
      $query->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_final]);
      if(!is_null($this->centro_facturacion_id))$query->where('centro_facturacion_id',$this->centro_facturacion_id);
      $query->whereIn('estado',['cobrado_parcial','cobrado_completo']);
    })->get();
    $facturas->load('factura_cobros_aplicados');

     $var= $facturas->map(function($factura){
       return $factura->factura_cobros_aplicados()->sum('cob_cobro_facturas.monto_pagado');
    });
     return $var->sum();

  }

  function notas_credito(){

    return $this->cliente->nota_credito()->where(function($query){
                      $query->where('estado','aprobado');
                      if(!is_null($this->centro_facturacion_id))$query->where('centro_facturacion_id',$this->centro_facturacion_id);
                      $query->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_final]);
                   })->sum('total');
  }

  function balance_final(){
    return  $this->balance_inicial() + $this->facturado() + $this->notas_credito() - $this->cobrado();
  }

  function detalle(){
    return $this->factura->where(function($query){
             $query->where('cliente_id',$this->cliente_id);
             $query->where('empresa_id',$this->empresa_id);
             $query->whereBetween('created_at',$this->fecha_inicio,$this->fecha_final);
             if(!is_null($this->centro_facturacion_id))$query->where('centro_facturacion_id',$this->centro_facturacion_id);
             $query->whereIn('estado',['por_cobrar']);
           })->get();
  }



}
