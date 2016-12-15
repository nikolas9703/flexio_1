<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha\Trimestral;
use Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha\Semestral;
use Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha\Anual;
use Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha\Mensual;
use Flexio\Modulo\Contabilidad\Models\Cuentas;

class GananciasPerdidas{

  public function getReporte($datos_reporte){

    $rango = ['trimestral'=> Trimestral::class,'mensual'=> Mensual::class,'anual'=>Anual::class,'semestral'=>Semestral::class];
    $data=[];
    //$builderCuentas1 = (new Cuentas)->newQuery();
    //$builderCuentas2 = (new Cuentas)->newQuery();
    //$builderCuentas3 = (new Cuentas)->newQuery();
    $year = $datos_reporte['year'];
    $mes  = $datos_reporte['mes'];
    $cantidad = $datos_reporte['periodo'];
    $fecha = Carbon::createFromDate($year, $mes, $this->hoy());

    $fechas = (new $rango[$datos_reporte['rango']])->filtro($cantidad, $fecha);
    if(!method_exists($this,$datos_reporte['rango'])){
      throw new \Execption("la funcion ".$datos_reporte['rango']." no existe");
    }
    $columnDinamica = call_user_func(array($this, $datos_reporte['rango']), $fechas, $datos_reporte);

    /*foreach([4,5,6] as $tipo_cuenta){
      $data[$tipo_cuenta] = Capsule::table('contab_cuentas as cuentas')
                                    ->select('cuentas.id','cuentas.codigo', 'cuentas.nombre','cuentas.padre_id', Capsule::raw($columnDinamica))
                                    ->whereIn('tipo_cuenta_id',[$tipo_cuenta])
                                    ->where('empresa_id',$datos_reporte['empresa_id'])
                                    ->get();

    }*/
    $data[4] = Capsule::table('contab_cuentas as cuentas')
                                  ->select('cuentas.id','cuentas.codigo', 'cuentas.nombre','cuentas.padre_id', Capsule::raw($columnDinamica))
                                  ->where('tipo_cuenta_id',4)
                                  ->where('empresa_id',$datos_reporte['empresa_id'])
                                  ->get();

    $data[5] = Capsule::table('contab_cuentas as cuentas')
                                  ->select('cuentas.id','cuentas.codigo', 'cuentas.nombre','cuentas.padre_id', Capsule::raw($columnDinamica))
                                  ->where('tipo_cuenta_id',5)
                                  ->where('empresa_id',$datos_reporte['empresa_id'])
                                  ->get();


    $data[6] =  Capsule::table('contab_cuentas as cuentas')
                                  ->select('cuentas.id','cuentas.codigo', 'cuentas.nombre','cuentas.padre_id', Capsule::raw($columnDinamica))
                                  ->where('tipo_cuenta_id',6)
                                  ->where('empresa_id',$datos_reporte['empresa_id'])
                                  ->get();


    return $data;

  }

  protected function hoy(){
    return Carbon::now()->day;
  }

  function anual($fechas, $datos){
    setlocale(LC_TIME, 'es_ES');
    $sql =[];
    $sql_centros="";
    if($datos['centro_contable'] !='todos'){
      $sql_centros=" AND centro_id IN(".$datos['centro_contable'].") ";
    }
    $i=0;
    foreach($fechas as $fecha)
    {
        $sql[] = "(SELECT IFNULL(ABS(SUM(debito) - SUM(credito)),0) FROM contab_transacciones WHERE cuenta_id = cuentas.id AND created_at BETWEEN '". $fecha[0]. "' AND '".$fecha[1] ."' AND empresa_id=".$datos['empresa_id']." ".$sql_centros.") AS '".Carbon::parse($fecha[1])->formatLocalized('%b-%Y') . "' ";
        $i++;
    }
    return implode(",",$sql);
  }

  function trimestral($fechas, $datos){
    setlocale(LC_TIME, 'es_ES');
    $sql=[];
    $sql_centros="";
    if($datos['centro_contable'] !='todos'){
      $sql_centros=" AND centro_id IN(".$datos['centro_contable'].") ";
    }
    foreach($fechas as $fecha)
    {
      $sql[] = "(SELECT IFNULL(ABS(SUM(debito) - SUM(credito)),0) FROM contab_transacciones WHERE cuenta_id = cuentas.id AND created_at BETWEEN '". $fecha[0]. "' AND '".$fecha[1] ."' AND empresa_id=".$datos['empresa_id']." ".$sql_centros.") AS '".Carbon::parse($fecha[0])->formatLocalized('%b-%y')." ".Carbon::parse($fecha[1])->formatLocalized('%b-%y'). "' ";

    }
    return implode(",", $sql);
  }

  function mensual($fechas, $datos){
      setlocale(LC_TIME, 'es_ES');
    $sql=[];
    $sql_centros="";
    if($datos['centro_contable'] !='todos'){
      $sql_centros=" AND centro_id IN(".$datos['centro_contable'].") ";
    }
    foreach($fechas as $fecha)
    {
      $sql[] = "(SELECT IFNULL(ABS(SUM(debito) - SUM(credito)),0) FROM contab_transacciones WHERE cuenta_id = cuentas.id AND created_at BETWEEN '". $fecha[0]. "' AND '".$fecha[1] ."' AND empresa_id=".$datos['empresa_id']." ".$sql_centros.") AS '".Carbon::parse($fecha[0])->formatLocalized('%b-%Y') . "' ";

    }
    return implode(",", $sql);
  }

  function semestral($fechas, $datos){
    setlocale(LC_TIME, 'es_ES');
    $sql=[];
    $sql_centros="";
    if($datos['centro_contable'] !='todos'){
      $sql_centros=" AND centro_id IN(".$datos['centro_contable'].") ";
    }

    foreach($fechas as $fecha)
    {
      $sql[] = "(SELECT IFNULL(ABS(SUM(debito) - SUM(credito)),0) FROM contab_transacciones WHERE cuenta_id = cuentas.id AND created_at BETWEEN '". $fecha[0]. "' AND '".$fecha[1] ."' AND empresa_id=".$datos['empresa_id']." ".$sql_centros.") AS '".Carbon::parse($fecha[0])->formatLocalized('%b-%y')." ".Carbon::parse($fecha[1])->formatLocalized('%b-%y'). "' ";

    }
    return implode(",", $sql);
  }
}
