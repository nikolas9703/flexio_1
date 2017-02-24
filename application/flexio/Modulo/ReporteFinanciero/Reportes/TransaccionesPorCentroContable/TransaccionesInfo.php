<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\TransaccionesPorCentroContable;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;

class TransaccionesInfo {

  private $empresa_id;
  private $centro_contable_id;
  private $categoria_id;
  private $cuenta_id;
  private $fecha_inicio;
  private $fecha_final;
  protected $AsientoContable;
  protected $proveedor;

  function __construct($datos) {
    $this->empresa_id         = $datos['empresa_id'];
    $this->centro_contable_id = !empty($datos['centro_contable_id']) ? $datos['centro_contable_id'] : "";
    $this->fecha_inicio       = $datos['fecha_inicio'];
    $this->fecha_final        = $datos['fecha_final'];
    $this->AsientoContable    = new AsientoContable;
  }

  // Armar array de data de transacciones
  // pa mostrar en la tabla.
  function listar() {

    $transacciones = $this->transacciones();

    //Verificar si existen facturas
    if(empty(collect($transacciones)->toArray())){
      return [];
    }

    $i=0;
    $lista = array();
    foreach ($transacciones AS $transaccion) {
        $lista[$i] = array(
          "id" => $transaccion->id,
          "cuenta" => $transaccion->cuenta_contable,
          "no_transaccion" => $transaccion->codigo,
          "fecha" => $transaccion->created_at,
          "centro_contable" => $transaccion->nombre_centro_contable,
          "transaccion" => $transaccion->nombre,
          "debito" => $transaccion->debito,
          "credito" => $transaccion->credito,
        );
        $i++;
    }

    return $lista;
  }

  function transacciones(){

    $cuenta_id = $this->cuenta_id;
    $categoria_id = $this->categoria_id;

    $transacciones = $this->AsientoContable->where(function($query){

      //Filtrar por centr contable
      if($this->centro_contable_id != "todos"){
        $query->where('centro_id',$this->centro_contable_id);
      }

      //filtra empresa actual
      $query->where('empresa_id', $this->empresa_id);

      //filtra rango de fecha
      $query->whereBetween('created_at', [$this->fecha_inicio,$this->fecha_final]);
    });

    return $transacciones->get();
  }

  function esTodosCentrosContables() {
    return $this->centro_contable_id=="todos" ? true : false;
  }

  function infoParametros() {

    $info = array();
    $info["rango_fechas"] = Carbon::parse($this->fecha_inicio)->format("d/m/Y") ." - ". Carbon::parse($this->fecha_final)->format("d/m/Y");

    //Info Centro Contable
    if($this->centro_contable_id){
      $centro = $this->esTodosCentrosContables() ? "Todos" : CentrosContables::where("id", $this->centro_contable_id)->first();
      $info["centro"] = is_string($centro) ? $centro : (!empty($centro->toArray()) ? $centro->nombre : "");
    }

    return $info;
  }

  function sumaDebito() {
    return $this->transacciones()->sum("debito");
  }

  function sumaCredito() {
    return $this->transacciones()->sum("credito");
  }
}
