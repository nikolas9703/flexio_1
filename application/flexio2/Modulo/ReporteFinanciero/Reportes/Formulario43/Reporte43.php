<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\Formulario43;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;

class Reporte43{

  private $empresa_id;
  private $proveedor_id;
  private $fecha;
  private $fecha_final;
  protected $factura;
  protected $proveedor;
  // 13 ->por aprobar
  // 14 -> por pagar
  // 15 -> pagada parcial
  // 16 -> pagada completa

  function __construct($datos){
    $this->empresa_id = $datos['empresa_id'];
    $this->fecha = $datos['fecha'];
    $this->factura = new FacturaCompra;
  }


  function generar(){

    $facturas = $this->factura->where(function($query){
      $query->where("empresa_id", $this->empresa_id);
      $query->whereBetween('fecha_desde',[$this->fecha->copy()->startOfMonth(), $this->fecha->endOfMonth()]);
      $query->whereIn("estado_id", [14,15,16]);
    })->get();

    $facturas->load('proveedor');

    $datos = $facturas->map(function($item){
      unset($item->uuid_factura);

      return $item;
    });
    return $facturas;
  }



}
