<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\Formulario433;
use Illuminate\Database\Capsule\Manager as DB;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Modulo\Proveedores\Models\Proveedores;


class Reporte433 {

  private $empresa_id;
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
    $this->proveedor = new Proveedores;
  }


  function generar(){

    $proveedores = $this->proveedor->whereHas("facturas",function($query){
      $query->where("empresa_id", $this->empresa_id);
      $query->whereBetween('fecha_desde',[$this->fecha->copy()->startOfMonth(), $this->fecha->endOfMonth()]);
      $query->whereIn("estado_id", [14,15,16]);
    })->with(["facturas"=>function($query){
      $query->whereBetween('fecha_desde',[$this->fecha->copy()->startOfMonth(), $this->fecha->endOfMonth()]);
      $query->whereIn("estado_id", [14,15,16]);
    }])->get();


    $datos = $proveedores->map(function($proveedor){
      return [
                 "nombre" => $proveedor->nombre,
                 "identificacion" =>$proveedor->identificacion,
                 "tomo_rollo" => $proveedor->tomo_rollo,
                 "folio_imagen_doc" => $proveedor->folio_imagen_doc,
                 "asiento_ficha" => $proveedor->asiento_ficha,
                 "digito_verificador" => $proveedor->digito_verificador,
                 "pasaporte" => $proveedor->pasaporte,
                 "provincia" => $proveedor->provincia,
                 "letra" => $proveedor->letra,
                 "monto" =>$proveedor->facturas->sum("monto"),
                 "retenido" =>$proveedor->facturas->sum("retenido")
              ];
    });
    return $datos;
  }



}
