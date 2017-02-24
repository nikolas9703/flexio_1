<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\CostoPorCentroCompras;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\Inventarios\Models\Categoria;
use Flexio\Modulo\Contabilidad\Models\Cuentas;
use Flexio\Modulo\Inventarios\Models\Unidades;
class FacturaCompraReporte {

  private $empresa_id;
  private $centro_contable_id;
  private $categoria_id;
  private $cuenta_id;
  private $fecha_inicio;
  private $fecha_final;
  protected $factura;
  protected $proveedor;

  function __construct($datos) {
    $this->empresa_id         = $datos['empresa_id'];
    $this->centro_contable_id = !empty($datos['centro_contable_id']) ? $datos['centro_contable_id'] : "";
    $this->categoria_id       = !empty($datos['categoria_id']) ? $datos['categoria_id'] : "";
    $this->cuenta_id          = !empty($datos['cuenta_id']) ? $datos['cuenta_id'] : "";
    $this->fecha_inicio       = $datos['fecha_inicio'];
    $this->fecha_final        = $datos['fecha_final'];
    $this->factura            = new FacturaCompra;
  }

  // Armar array de data de facturas
  // pa mostrar en la tabla.
  function listar() {

    $facturas = $this->facturas();
//dd(collect($facturas)->count());
    //Verificar si existen facturas
    if(empty(collect($facturas)->toArray())){
      return [];
    }

    $i=0;
    $detalle = array();
    foreach ($facturas AS $factura) {

      //Recorrer Items
      foreach ($factura->facturas_items AS $item) {
        //Informacion de la cuenta
        $cuenta = !empty($item->cuenta_id) ? Cuentas::where("id", $item->cuenta_id)->select('nombre')->first() : [];
        $codigo = !empty($item->cuenta_id) ? Cuentas::where("id", $item->cuenta_id)->select('codigo')->first() : [];
        $cuenta = !empty(collect($cuenta)->toArray()) ? $codigo->codigo . " " . $cuenta->nombre : "";

        //Información Unidad
        $unidad = !empty($item->unidad_id) ? Unidades::where("id", $item->unidad_id)->select('nombre')->first() : [];
        $unidad = !empty(collect($unidad)->toArray()) ? $unidad->nombre : "";

        $detalle[$i] = array(
          "id" => $factura->id,
          "fecha" => $factura->created_at,
          "codigo" => $factura->codigo,
          "proveedor" => !empty($factura->proveedor) ? $factura->proveedor->nombre : "",
          "categoria" => !empty($item->item) && !empty($item->item->categoria[0]) ? $item->item->categoria[0]->nombre : "",
          "item" => !empty($item->item) ? $item->item->nombre : "",
          "cuenta" => $cuenta,
          "cantidad" => $item->cantidad,
          "unidad" => $unidad,
          "subtotal" => $item->subtotal,
          "descuento" => $item->descuentos,
          "impuesto" => $item->impuestos,
          "total" => $item->total,
          "retenido" => $item->retenido,
        );
        $i++;
      }
    }

    return $detalle;
  }

  function facturas(){

    $cuenta_id = $this->cuenta_id;
    $categoria_id = $this->categoria_id;

    $facturas = $this->factura->with(array("facturas_items", "facturas_items.item", "facturas_items.item.categoria", "facturas_items.item.cuenta_ingreso","facturas_items.item.cuenta_costo","facturas_items.item.cuenta_activo"))->where(function($query){

      //Filtrar factura por centr contable
      if($this->centro_contable_id != "todos"){
        $query->where('centro_contable_id',$this->centro_contable_id);
      }

      //filtra empresa actual
      $query->where('empresa_id', $this->empresa_id);

      //filtra rango de fecha
      $query->whereBetween('created_at', [$this->fecha_inicio,$this->fecha_final]);

      //filtra estados aprobado
      $query->whereNotIn('estado_id',[13,17]); //#1128 Omitir anulada y por aprobar

    })->whereHas('facturas_items',function($query) use($cuenta_id, $categoria_id){

      //Filtrar items por cuenta si existe valor
      if(!empty($cuenta_id) && $cuenta_id != "todos"){
        $query->where("cuenta_id", $cuenta_id);
      }

      //Filtrar items por categoria si existe valor
      if(!empty($categoria_id) && $categoria_id != "todos"){
        $query->where("categoria_id", $categoria_id);
      }
    });

    return $facturas->get();
  }

  function esTodosCentrosContables() {
    return $this->centro_contable_id=="todos" ? true : false;
  }

  function esTodasCategorias() {
    return $this->categoria_id=="todos" ? true : false;
  }

  function infoParametros() {

    $info = array();
    $info["rango_fechas"] = Carbon::parse($this->fecha_inicio)->format("d/m/Y") ." - ". Carbon::parse($this->fecha_final)->format("d/m/Y");

    //Info Centro Contable
    if($this->centro_contable_id){
      $centro = $this->esTodosCentrosContables() ? "Todos" : CentrosContables::where("id", $this->centro_contable_id)->first();
      $info["centro"] = is_string($centro) ? $centro : (!empty($centro->toArray()) ? $centro->nombre : "");
    }

    //Info Centro Contable
    if($this->categoria_id){
      $categoria = $this->esTodasCategorias() ? "Todos" : Categoria::where("id", $this->categoria_id)->first();
      $info["categoria"] = is_string($categoria) ? $categoria : (!empty($categoria->toArray()) ? $categoria->nombre : "");
    }

    return $info;
  }

  function sumaSubtotales() {
	return $this->facturas()->sum(function($factura){
       return $factura->facturas_items->sum('subtotal');
     });
  }

  function sumaDescuentos() {
    //return $this->facturas()->sum("descuentos");
	return $this->facturas()->sum(function($factura){
       return $factura->facturas_items->sum('descuentos');
     });
  }

  function sumaImpuestos() {
    //return $this->facturas()->sum("impuestos");
	return $this->facturas()->sum(function($factura){
       return $factura->facturas_items->sum('impuestos');
     });
  }

  function sumaTotales() {
    //return $this->facturas()->sum("total");
	return $this->facturas()->sum(function($factura){
       return $factura->facturas_items->sum('total');
     });
  }

  function sumaRetenido() {
      return $this->facturas()->sum(function($factura){
       return $factura->facturas_items->sum('retenido');
     });
    //return $this->facturas()->facturas_items->sum('retenido');
  }

}
