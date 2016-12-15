<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha\Trimestral;
use Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha\Semestral;
use Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha\Anual;

class ReporteBalanceSituacion {

public function getReporte($datos_reporte){

  $rango = ['trimestral'=> Trimestral::class,'anual'=>Anual::class,'semestral'=>Semestral::class];
  $data=[];
  $year = $datos_reporte['year'];
  $mes  = $datos_reporte['mes'];
  $cantidad = $datos_reporte['periodo'];
  $fecha = Carbon::createFromDate($year, $mes, $this->hoy());

  $fechas = (new $rango[$datos_reporte['rango']])->filtroBalance($cantidad, $fecha);
//dd($fechas);
  if(!method_exists($this,$datos_reporte['rango'])){
    throw new Execption("la funcion ".$datos_reporte['rango']." no existe");
  }

  $columnDinamica = call_user_func(array($this, $datos_reporte['rango']), $fechas, $datos_reporte['empresa_id']);

  foreach([1,2,3] as $tipo_cuenta){
    $data[$tipo_cuenta] = Capsule::table('contab_cuentas as cuentas')
                                  ->select('cuentas.id','cuentas.codigo', 'cuentas.nombre','cuentas.padre_id', Capsule::raw($columnDinamica))
                                  ->whereIn('tipo_cuenta_id',[$tipo_cuenta])
                                  ->where('empresa_id',$datos_reporte['empresa_id'])
                                  ->get();
  }
  //dd($data);
  return $data;

}


protected function hoy(){
   return Carbon::now()->day;
}

protected function anual($fechas,$empresa_id){
    setlocale(LC_TIME, 'es_ES');
    $sql =[];
    $i=0;

    foreach($fechas as $fecha)
    {
        $sql[] = "(SELECT IFNULL(ABS(SUM(debito) - SUM(credito)),0)
FROM contab_transacciones WHERE cuenta_id = cuentas.id AND created_at BETWEEN '". $fecha[0]. "' AND '".$fecha[1] ."' AND empresa_id=".$empresa_id.") AS '".Carbon::parse($fecha[1])->formatLocalized('%b-%Y') . "' ";
$i++;
    }
return implode(",",$sql);
}

protected function semanal($fechas,$empresa_id){
  setlocale(LC_TIME, 'es_PA.UTF-8');
  $sql =[];

  foreach($fechas as $fecha)
  {
    $sql[] = "(SELECT IFNULL(ABS(SUM(debito) - SUM(credito)),0)
FROM contab_transacciones WHERE cuenta_id = cuentas.id AND DATE_FORMAT(created_at,'%v-%x') = '". $fecha. "' AND empresa_id=".$empresa_id.") AS 'Semana-".$fecha."'";

  }
  return implode(",",$sql);
}

protected function trimestral($fechas, $empresa_id)
{
  setlocale(LC_TIME, 'es_ES');
  $sql=[];

  foreach($fechas as $fecha)
  {
    $sql[] = "(SELECT IFNULL(ABS(SUM(debito) - SUM(credito)),0) FROM contab_transacciones WHERE cuenta_id = cuentas.id AND created_at BETWEEN '". $fecha[0]. "' AND '".$fecha[1] ."' AND empresa_id=".$empresa_id.") AS '".Carbon::parse($fecha[1])->formatLocalized('%b-%y'). "' ";

  }
  return implode(",", $sql);
}

protected function semestral($fechas, $empresa_id)
{
  setlocale(LC_TIME, 'es_ES');
  $sql=[];
  foreach($fechas as $fecha)
  {
    $sql[] = "(SELECT IFNULL(ABS(SUM(debito) - SUM(credito)),0) FROM contab_transacciones WHERE cuenta_id = cuentas.id AND created_at BETWEEN '". $fecha[0]. "' AND '".$fecha[1] ."' AND empresa_id=".$empresa_id.") AS '".Carbon::parse($fecha[1])->formatLocalized('%b-%y'). "' ";
  }
  return implode(",", $sql);
}

}
