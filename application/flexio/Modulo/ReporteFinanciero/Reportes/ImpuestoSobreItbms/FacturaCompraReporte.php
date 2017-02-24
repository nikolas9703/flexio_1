<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreItbms;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompraItems;
use Flexio\Modulo\Contabilidad\Models\Impuestos;
class FacturaCompraReporte {

  private $empresa_id;
  private $proveedor_id;
  private $fecha_inicio;
  private $fecha_final;
  protected $factura;
  protected $proveedor;
  protected $factura_items;
  protected $impuesto;

  function __construct($datos, $proveedor){
    $this->empresa_id = $datos['empresa_id'];
    $this->proveedor_id = $datos['proveedor'];
    $this->fecha_inicio = $datos['fecha_inicio'];
    $this->fecha_final = $datos['fecha_final'];
    $this->factura = new FacturaCompra;
    $this->factura_items = new FacturaCompraItems;
    $this->proveedor = $proveedor;
    $this->impuesto = new Impuestos;
  }

  function total_facturado(){   
  $facturas_id = $this->factura->where(function($query){
      $query->where('proveedor_id',$this->proveedor_id);
      $query->where('empresa_id',$this->empresa_id);
      $query->whereIn('estado_id',[14,15,16]);
      $query->whereBetween('fecha_desde',[$this->fecha_inicio,$this->fecha_final]);
    })->pluck('id');
  $impuesto_id =  $this->impuesto->where(function($query){
      $query->where('nombre', 'LIKE', '%ITBMS%');
      $query->where('empresa_id', $this->empresa_id);
  })->first();  // El impuesto seleccionado es cualquiera con el nombre ITBMS que segun Roberto Boyd no debera ser duplicado y mas adelante se habilitara un select con todos los impuestos
  return $this->factura_items->where(function($query) use($facturas_id, $impuesto_id){
      $query->whereIn('factura_id', $facturas_id);
      $query->where('impuesto_id', $impuesto_id->id);
  })->sum('subtotal');  
  }
  function total_itbms(){
  $facturas_id = $this->factura->where(function($query){
      $query->where('proveedor_id',$this->proveedor_id);
      $query->where('empresa_id',$this->empresa_id);      
      $query->whereIn('estado_id',[14,15,16]);
      $query->whereBetween('fecha_desde',[$this->fecha_inicio,$this->fecha_final]);
    })->pluck('id');
 
  $impuesto_id =  $this->impuesto->where(function($query){
      $query->where('nombre', 'LIKE', '%ITBMS%');
      $query->where('empresa_id', $this->empresa_id);
  })->first();  // El impuesto seleccionado es cualquiera con el nombre ITBMS que segun Roberto Boyd no debera ser duplicado y mas adelante se habilitara un select con todos los impuestos
  return $this->factura_items->where(function($query) use($facturas_id, $impuesto_id){
      $query->whereIn('factura_id', $facturas_id);
      $query->where('impuesto_id', $impuesto_id->id);
  })->sum('impuestos');  
  }
  function total_retenido(){
  $facturas_id = $this->factura->where(function($query){
      $query->where('proveedor_id',$this->proveedor_id);
      $query->where('empresa_id',$this->empresa_id);      
      $query->whereIn('estado_id',[14,15,16]);
      $query->whereBetween('fecha_desde',[$this->fecha_inicio,$this->fecha_final]);
    })->pluck('id');
 
  $impuesto_id =  $this->impuesto->where(function($query){
      $query->where('nombre', 'LIKE', '%ITBMS%');
      $query->where('empresa_id', $this->empresa_id);
  })->first();  // El impuesto seleccionado es cualquiera con el nombre ITBMS que segun Roberto Boyd no debera ser duplicado y mas adelante se habilitara un select con todos los impuestos
  return $this->factura_items->where(function($query) use($facturas_id, $impuesto_id){
      $query->whereIn('factura_id', $facturas_id);
      $query->where('impuesto_id', $impuesto_id->id);
  })->sum('retenido');  
  }

}
