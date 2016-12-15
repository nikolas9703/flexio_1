<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\CuentaPorCobrarAntiguedad;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Cliente\Models\Cliente;

class ClienteFacturas{

  protected $fecha_inicial;
  protected $cliente;

  function __construct($fecha){
    $this->fecha_inicial = $fecha;
    $this->cliente = new Cliente;
  }

  function consulta($empresa_id){
    $rangos = $this->fechas();

    $clienteSql = $this->sqlCliente($rangos);

    $clientes = $this->cliente->whereHas('facturas',function($query) use($empresa_id,$rangos){
      $query->where('empresa_id', $empresa_id)
            ->whereIn('estado', ['por_cobrar','cobrado_parcial']);
            //->whereBetween('created_at',[$rangos['120_dias'][0],$rangos['corriente'][1]]);

    })
    ->where('empresa_id', $empresa_id)
    ->select('cli_clientes.id','nombre',Capsule::raw($clienteSql))
    ->get();

    foreach($clientes as $key=>$cliente){
      $facturaSql = $this->facturaSql($rangos);
      $clientes[$key]->{'facturas'} = Capsule::table('fac_facturas as fac')->select('id','codigo',Capsule::raw($facturaSql))
      ->where('cliente_id', $cliente->id)
      ->whereIn('estado', ['por_cobrar','cobrado_parcial'])
      ->get();
    }
    return $clientes;
  }
  /*
    setea los columnas de corriente, 30_dias,60_dias,90_dias,120 con valor 0
    a clientes
   */
  function sqlCliente($dates){
      $totalfilas = count($dates);
      $i=1;
    foreach($dates as $key=>$fecha){

      if($i == $totalfilas){
          $sql[] = "IFNULL((SELECT SUM(0)), 0) AS '".$key . "' ";
      }else{
          $sql[] = "IFNULL((SELECT SUM(0)), 0) AS '".$key . "' ";
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
        $sql[] = "CASE WHEN created_at <= '". $fecha[1]. "' THEN fac_ventas_monto(id, total) END AS '".$key . "'";
      }else{
        $sql[] = "CASE WHEN created_at BETWEEN '". $fecha[0]. "' AND '". $fecha[1]. "' THEN fac_ventas_monto(id, total) END AS '".$key . "'";
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
}
