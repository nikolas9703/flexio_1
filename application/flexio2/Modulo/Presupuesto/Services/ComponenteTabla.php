<?php

namespace Flexio\Modulo\Presupuesto\Services;

use Carbon\Carbon;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
class ComponenteTabla{

  protected $cuenta;

  function __construct(){
    $this->cuenta =  new CuentasRepository;
  }

  function generarComponente($datos){
    if(!method_exists($this, $datos['tipo'])){
       throw new \Exception("El key para este reporte no existe");
    }

    return call_user_func_array([$this, $datos['tipo'] ], [$datos]);
  }

  function periodo($datos){

    $inicio = $datos['inicio'];
    $periodo = $datos['cantidad_meses']; //cantidad de meses

    list($mes, $year) = explode("-",$inicio);
    $colNames = array("",'Codigo','Cuentas');
    $colModel = array(array('name'=>'id_cuenta','index'=>'id_cuenta','hidelg' => true,'key' =>true, 'hidden' => true),
                array('name'=>'codigo','index'=>'codigo','width' => '100'),array('name'=>'nombre','index'=>'nombre','width' => '240'));

    $total = count($colNames) - 3;
    for($j=0;$j<=($periodo-1);$j++){
      $fecha_nueva =  Carbon::createFromDate($year, $mes, 1, 'America/Panama');
      $fechaObj = $fecha_nueva->addMonths($j);
      $nombre_columna = str_replace(".","",$fechaObj->formatLocalized('%b-%y'));
      $nombre_columna1 = str_replace(".","",$fechaObj->formatLocalized('%b_%y'));
      array_push($colNames, ucfirst($nombre_columna));
      array_push($colModel,array('name' => $nombre_columna1,'index' => $nombre_columna1,'width' => '150'));
    }
    $total_campos = count($colNames) - 3;

    $modelos = $colModel;
    array_shift($modelos);
    array_shift($modelos);
    array_shift($modelos);

    $rows = array();
    $condicion = array('empresa_id'=>$datos['empresa_id'],'tipo_cuenta_id'=>[5,6],'transaccionales'=>true);
    $cuentas = $this->cuenta->get($condicion);
    $cuentas = $cuentas->sortBy('codigo');
    $j=0;
    foreach($cuentas as $cuenta){
        array_push($rows,array('id_cuenta'=>'<input data-tipo="id" type="hidden" name="presupuesto_cuentas[cuenta_id][]" id="cuentas'.$j.'" value="'.$cuenta->id.'"/>', 'codigo'=>$cuenta->codigo,'nombre'=>$cuenta->nombre. '<a data-my="'.$j.'" class="cog pull-right btn-default btn-xs btn-outline" href="javascript:"><i class="fa icono-funcion"></i></a>'));
        $j++;
    }
    $new_row = array();
    for($i=0;$i<count($rows);$i++){

      foreach($modelos as $modelo){
        $my_input = '<input data-tipo="mes" name="presupuesto_cuentas[meses]['.$modelo["name"].'][]" id="'.$modelo["name"]."_".$i.'" type="text" class="mes form-control moneda"  data-index="'.$i.'" >';
        $rows[$i] = array_merge($rows[$i], array($modelo['name']=>$my_input));
      }
      $rows[$i] = array_merge($rows[$i],array('totales'=>'<div class="input-group input-group-sm"><span class="input-group-addon" id="sizing-addon4">$</span><input data-tipo="total" id="totales'.$i.'" name="presupuesto_cuentas[montos][]" text="text" readonly class="form-control moneda" aria-describedby="sizing-addon4" /></div>'));
    }
    array_push($colNames,'Totales');
    array_push($colModel,array('name' => 'totales','index' => 'totales','width' => '150'));
    return $response = array('colName'=>$colNames,'colModel'=>$colModel,'total'=>count($rows),'page'=>1,'record'=>count($rows), 'rows'=>$rows);

  }

  function avance($datos){
    $colNames = array("",'Codigo','Cuentas','Presupuesto','% de avance actual','&Uacute;ltima actualizaci&oacute;n');
    $colModel = array(array('name'=>'cuenta_id','index'=>'cuenta_id','hidelg' => true,'key' =>true, 'hidden' => true),
                array('name'=>'codigo','index'=>'codigo','width' => '100'),
                array('name'=>'nombre','index'=>'nombre','width' => '240'),
                array('name'=>'presupuesto','index'=>'presupuesto','width' => '200'),
                array('name'=>'porcentaje_avance','index'=>'porcentaje_avance','width' => '200'),
                array('name'=>'ultima_actualizacion','index'=>'ultima_actualizacion','width' => '200'),
              );

    $rows = array();
    $condicion = array('empresa_id'=>$datos['empresa_id'],'tipo_cuenta_id'=>[5,6], 'transaccionales'=>true);
    //dd($condicion);
    $cuentas = $this->cuenta->get($condicion);
    $cuentas = $cuentas->sortBy('codigo');
    $j=0;
    foreach($cuentas as $cuenta){
      array_push($rows,array('cuenta_id'=>'<input data-tipo="id" type="hidden" name="presupuesto_cuentas[cuenta_id][]" id="cuentas'.$j.'" value="'.$cuenta->id.'"/>', 'codigo'=>$cuenta->codigo,'nombre'=>$cuenta->nombre,
      'presupuesto'=>'<div class="input-group input-group-sm"><span class="input-group-addon" id="sizing-addon4">$</span><input data-tipo="presupuesto" id="presupuesto'.$j.'" name="presupuesto_cuentas[montos][]" text="text"  class="form-control moneda" aria-describedby="sizing-addon4" /></div>',
      'porcentaje_avance'=>'<div class="input-group input-group-sm"><input data-tipo="porcentaje" id="porcentaje'.$j.'" name="presupuesto_cuentas[porcentaje][]" text="text" class="form-control porcentaje" /><span class="input-group-addon">%</span></div>',
      'ultima_actualizacion'=>'<div class="input-group input-group-sm"><span class="input-group-addon" id="sizing-addon4"><i class="fa fa-calendar"></i></span><input data-tipo="ultima_actualizacion" id="ultima_actualizacion'.$j.'" name="presupuesto_cuentas[ultima_actualizacion][]" text="text"  class="form-control" aria-describedby="sizing-addon4" readonly /></div>'));
      $j++;

    }
    return $response = array('colName'=>$colNames,'colModel'=>$colModel,'total'=>count($rows),'page'=>1,'record'=>count($rows), 'rows'=>$rows);
  }
}
