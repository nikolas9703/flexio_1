<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\Formulario433;
use Illuminate\Database\Capsule\Manager as DB;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Modulo\Proveedores\Models\Proveedores;
use Flexio\Modulo\NotaDebito\Models\NotaDebito;
//verificar luego
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\Inventarios\Models\Categoria;
use Flexio\Modulo\Contabilidad\Models\Cuentas;
use Flexio\Modulo\Inventarios\Models\Unidades;


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

  private $centro_contable_id;
  private $categoria_id;
  private $cuenta_id;
  private $fecha_inicio;


  function __construct($datos){
    $this->empresa_id = $datos['empresa_id'];
    $this->fecha = $datos['fecha'];
    $this->factura = new FacturaCompra;
    $this->proveedor = new Proveedores;
  }


  function generar() {


    $facturas = $this->facturas();


    if(empty(collect($facturas)->toArray())){

      return [];
    }

    $i=0;

    $detalle =[];

    foreach ($facturas AS $factura) {

      //Recorrer Items
    //  foreach ($factura->facturas_items AS $item) {

        //Informacion de la cuenta
        //$cuenta = !empty($item->cuenta_id) ? Cuentas::where("id", $item->cuenta_id)->select('nombre')->first() : [];
        //$codigo = !empty($item->cuenta_id) ? Cuentas::where("id", $item->cuenta_id)->select('codigo')->first() : [];
        //$cuenta = !empty(collect($cuenta)->toArray()) ? $codigo->codigo . " " . $cuenta->nombre : "";

        //Información Unidad
        //$unidad = !empty($item->unidad_id) ? Unidades::where("id", $item->unidad_id)->select('nombre')->first() : [];
        //$unidad = !empty(collect($unidad)->toArray()) ? $unidad->nombre : "";


        if ($factura->proveedor->retiene_impuesto == "no" &&  $factura->facturas_items->sum("impuestos") > 0){

        $detalle[] = array(
          "nombre" => is_null($factura->proveedor->nombre)?"":$factura->proveedor->nombre,
          "identificacion" => is_null($factura->proveedor->identificacion)?"":$factura->proveedor->identificacion,
          "tomo_rollo" => is_null($factura->proveedor->tomo_rollo)?"":$factura->proveedor->tomo_rollo,
          "folio_imagen_doc" => is_null($factura->proveedor->folio_imagen_doc)?"":$factura->proveedor->folio_imagen_doc,
          "asiento_ficha" => is_null($factura->proveedor->asiento_ficha)?"":$factura->proveedor->asiento_ficha,
          "digito_verificador" => is_null($factura->proveedor->digito_verificador)?" ":$factura->proveedor->digito_verificador,
          "pasaporte" => is_null($factura->proveedor->pasaporte)?"":$factura->proveedor->pasaporte,
          "provincia" => is_null($factura->proveedor->provincia)?"":$factura->proveedor->provincia,
          "letra" => is_null($factura->proveedor->letra)?"":$factura->proveedor->letra,
          "itbms" => is_null($factura->facturas_items)?"":$factura->facturas_items->sum("impuestos"),
          "retenido" => is_null($factura->facturas_items)?"":$factura->facturas_items->sum("retenido"),
          "monto" => is_null($factura->facturas_items)?"":$factura->facturas_items->sum("total"),
          "codigo" => is_null($factura->factura_proveedor)?"":$factura->factura_proveedor
        );


        /*$nuevasNOta = $factura->nota_debito->filter(function($notas){
          return $notas->estado != 'anulado';
        });

        foreach($nuevasNOta as $nota){
          $detalle[] = array(
            "nombre" => $factura->proveedor->nombre,
            "identificacion" => $factura->proveedor->identificacion,
            "tomo_rollo" => $factura->proveedor->tomo_rollo,
            "folio_imagen_doc" => $factura->proveedor->folio_imagen_doc,
            "asiento_ficha" => $factura->proveedor->asiento_ficha,
            "digito_verificador" => $factura->proveedor->digito_verificador,
            "pasaporte" => $factura->proveedor->pasaporte,
            "provincia" => $factura->proveedor->provincia,
            "letra" => $factura->proveedor->letra,
            "itbms" => "-".$nota->impuesto,
            "retenido" => "-".$nota->retenido ,
            "monto" =>  "-".$nota->total,

            "codigo" => $nota->no_nota_credito
          );
        }*/

        $i++;
      }
      //}

    }
    $notas = $this->getNotaCredito();

if(!empty($notas)){

    return array_merge($detalle,$notas);
  } else {
    
    return array_merge($detalle);

  }
  }

  function facturas(){



    $facturas = $this->factura->with(array("facturas_items", "facturas_items.item", "facturas_items.item.categoria", "facturas_items.item.cuenta_ingreso","facturas_items.item.cuenta_costo","facturas_items.item.cuenta_activo","nota_debito"))->where(function($query){


      //filtra empresa actual
      $query->where('empresa_id', $this->empresa_id);
      $query->whereBetween('fecha_desde',[$this->fecha->copy()->startOfMonth(), $this->fecha->endOfMonth()]);
      $query->whereIn("estado_id", [13,14,15,16,20]);



    });

    return $facturas->get();
  }

  function getNotaCredito(){
    $empresa = $this->empresa_id;
    $notas_creditos = NotaDebito::with('proveedor')->where(function($query) use($empresa){
      $query->where('empresa_id', $empresa)
            ->whereBetween('fecha',[$this->fecha->copy()->startOfMonth(), $this->fecha->endOfMonth()])
            ->whereIn('estado',['aprobado', 'por_aprobar']);
    })->get();

$detalle = array();

    foreach($notas_creditos as $nota){
    if ($nota->proveedor->retiene_impuesto == "no" &&  $nota->impuesto > 0){
      $detalle[] = array(
        "nombre" => is_null($nota->proveedor)?"":$nota->proveedor->nombre,
        "identificacion" => is_null($nota->proveedor)?"":$nota->proveedor->identificacion,
        "tomo_rollo" =>  is_null($nota->proveedor)?"":$nota->proveedor->tomo_rollo,
        "folio_imagen_doc" => is_null($nota->proveedor)?"":$nota->proveedor->folio_imagen_doc,
        "asiento_ficha" => is_null($nota->proveedor)?"":$nota->proveedor->asiento_ficha,
        "digito_verificador" => is_null($nota->proveedor)?"":$nota->proveedor->digito_verificador,
        "pasaporte" => is_null($nota->proveedor)?"":$nota->proveedor->pasaporte,
        "provincia" => is_null($nota->proveedor)?"":$nota->proveedor->provincia,
        "letra" => is_null($nota->proveedor)?"":$nota->proveedor->letra,
        "itbms" => "-".$nota->impuesto,
        "retenido" => "-".$nota->retenido ,
        "monto" =>  "-".$nota->total,

        "codigo" => $nota->no_nota_credito
      );
    }
    }

    return $detalle;
  }


  function generar1(){






$arreglo = array(
              "retiene_impuesto" => "no"
              );

    //$proveedores = $this->proveedor->where("estado", "activo")->where("estado", "activo")->whereHas("facturas",function($query){
      $proveedores = $this->proveedor->where($arreglo)->whereHas("facturas",function($query){
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
                  "total" =>$proveedor->facturas->sum("monto"),
                 "retenido" =>$proveedor->facturas->sum("retenido")
              ];
    });


    return $datos;
  }



}
