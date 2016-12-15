<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\CuentaPorPagarAntiguedad;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Proveedores\Models\Proveedores as Proveedor;
class ProveedorFacturas{

  protected $fecha_inicial;
  protected $proveedor;

  function __construct($fecha){
    $this->fecha_inicial = $fecha;
    $this->proveedor = new Proveedor;
  }

  function consulta($empresa_id){
    $rangos = $this->fechas();

    $provedorSql = $this->sqlProveedor($rangos);

    $proveedores = $this->proveedor->whereHas('facturas',function($query) use($empresa_id,$rangos){
        $query->where('empresa_id', $empresa_id)
              ->whereIn('estado_id', [14, 15]);
              //->whereBetween('fecha_desde',[$rangos['120_dias'][0],$rangos['corriente'][1]]);
             })
        ->where('id_empresa', $empresa_id)
        ->select('pro_proveedores.id','nombre',Capsule::raw($provedorSql))
        ->get();

    foreach($proveedores as $key=>$proveedor){
      $facturaSql = $this->facturaSql($rangos);
      $proveedores[$key]->{'facturas'} = Capsule::table('faccom_facturas as faccom')
      ->select('id','factura_proveedor as codigo',Capsule::raw($facturaSql))
      ->where('proveedor_id', $proveedor->id)
      ->whereIn('estado_id', [14, 15])
      //->whereBetween('fecha_desde',[$rangos['120_dias'][0],$rangos['corriente'][1]])
      ->get();
    }
    return $proveedores;
  }


  /*
    setea los columnas de corriente, 30_dias,60_dias,90_dias,120 con valor 0
    a proveedores
   */
  function sqlProveedor($dates){
    $totalfilas = count($dates);
    $i=1;
    foreach($dates as $key=>$fecha){

      if($i==$totalfilas){
         $sql[] = "IFNULL((SELECT SUM(0)),0) AS '".$key . "'";
      }else{
          $sql[] = "IFNULL((SELECT SUM(0)),0) AS '".$key . "' ";
      }
     $i++;
    }
    return implode(",",$sql);
  }

  function facturaSql($dates){
    $totalfilas = count($dates);
    $i=1;
    foreach($dates as $key=>$fecha){

      if($i == $totalfilas){
        $sql[] = "CASE WHEN fecha_desde <= '". $fecha[1]. "' THEN  fac_compras_monto(proveedor_id, id, empresa_id, total) END AS '".$key . "'";
      }else{
        $sql[] = "CASE WHEN fecha_desde BETWEEN '". $fecha[0]. "' AND '". $fecha[1]. "' THEN fac_compras_monto(proveedor_id, id, empresa_id, total)  END AS '".$key . "'";
      }
    $i++;
   }
    return implode(",",$sql);
  }


  function fechas(){
    $dias = 30;
    $i = 1;
    $fechas=[];
    for($i;$i<=5;$i++){
      if($i == 1){
      $fechas[] = [$this->fecha_inicial->copy()->subDays($dias)->startOfDay()->toDateTimeString(),
      $this->fecha_inicial->endOfDay()->toDateTimeString()];
    }else{
      $fechas[] = [$this->fecha_inicial->copy()->subDays($dias)->startOfDay()->toDateTimeString(),
      $this->fecha_inicial->copy()->subDay()->endOfDay()->toDateTimeString()];
    }
      $this->fecha_inicial->subDays($dias);
    }
    return array_combine(['corriente','30_dias','60_dias','90_dias','120_dias'],$fechas);
  }

  function sumar_antiguedad(){

  }
}
