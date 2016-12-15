<?php

namespace Flexio\Modulo\Presupuesto\Services;

use Carbon\Carbon;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;

class ComponenteTablaVer{
  protected $presupuesto;

  function __construct($presupuesto){

    $this->presupuesto = $presupuesto;
  }


  function ArmarJqgrid(){
    if(!method_exists($this, $this->presupuesto->tipo)){
       throw new \Exception("La clase no tiene el tipo de elemento para armar la tabla");
    }

    return call_user_func_array([$this, $this->presupuesto->tipo],[]);
  }

  function periodo(){
    $perido = (int) $this->presupuesto->cantidad_meses; //cantidad de meses
    list($mes, $year) = explode("-",$this->presupuesto->inicio);

    $colNames = array("",'Codigo','Cuentas');
    $colModel = array(array('name'=>'id_cuenta','index'=>'id_cuenta','hidelg' => true,'key' =>true, 'hidden' => true),
                array('name'=>'codigo','index'=>'codigo','width' => '100'),array('name'=>'nombre','index'=>'nombre','width' => '240'));
    $fecha_cabecera = array();//este array se utiliza para capturar la fecha 01-2016
    $modificar = array();

    for($j=0;$j<=($perido-1);$j++){
      $fecha_nueva =  Carbon::createFromDate($year, $mes, 1, 'America/Panama');
      $fechaObj = $fecha_nueva->addMonths($j);
      $nombre_columna = str_replace(".","",$fechaObj->formatLocalized('%b-%y'));
      $nombre_columna1 = str_replace(".","",$fechaObj->formatLocalized('%b_%y'));

      array_push($fecha_cabecera ,array('fecha_1' => $fechaObj->formatLocalized('%m-%Y'),'fecha_2' => $nombre_columna1));
      array_push($colNames, "Real: ".ucfirst($nombre_columna));
      array_push($colNames, ucfirst($nombre_columna));
      array_push($colModel,array('name' => "real_".$nombre_columna1,'index' => "real_".$nombre_columna1,'width' => '150'));
      array_push($colModel,array('name' => $nombre_columna1,'index' => $nombre_columna1,'width' => '150'));
      array_push($modificar,array('name' => $nombre_columna1,'index' => $nombre_columna1,'width' => '150'));
    }

    $total_campos = count($colNames) - 3;
    $condicion = array('empresa_id'=>$this->presupuesto->empresa_id,'tipo_cuenta_id'=> 6);


    $listado_presupuesto = $this->presupuesto->lista_presupuesto;

    $j=0;
    $rows1 = array();
    foreach($listado_presupuesto as $lista){
      array_push($rows1, array(
        'id_cuenta'=> '<input data-tipo="id" type="hidden" name="presupuesto_cuentas[cuenta_id][]" id="cuentas'.$j.'" value="'.$lista->cuentas_id.'"/> <input type="hidden" id="editar'.$j.'" name="presupuesto_cuentas[][id]" value="'.$lista->id.'">',
        'codigo'=> $lista->cuentas->codigo,
        'nombre'=> $lista->cuentas->nombre. '<a data-my="'.$j.'" @click="abrirDialogo($event)" class="cog pull-right btn-default btn-xs btn-outline" href="javascript:"><i class="fa icono-funcion"></i></a>'
      ));
      $r = 0;
      $meses = json_decode($lista->info_presupuesto);
      $monto = 0;

        foreach($fecha_cabecera as $modelo){
          //$inputMask = ' data-inputmask="\'mask\': \'9{1,15}.99\', \'greedy\':true" ';
          $input_valor = isset($meses->meses->{$modelo['fecha_2']})?$meses->meses->{$modelo['fecha_2']}:0;
          $my_input = '<input data-tipo="mes" name="presupuesto_cuentas[meses]['.$modelo["fecha_2"].'][]" id="'.$modelo["fecha_2"]."_".$j.'" value="'. $input_valor .'" type="text" class="mes form-control moneda" placeholder="0.00" data-index="'.$j.'" >';

          $transanccion_total = (double)$lista->transacciones_gastos($lista->cuentas_id,$lista->centro_contable_id,$modelo['fecha_1']);
          $rows1[$j] = array_merge($rows1[$j], array("real_".$modelo["fecha_2"] => "<label class='".$this->totales_color($input_valor, $transanccion_total)."'>".number_format((float)$transanccion_total, 2, '.', '')."</label>"));
          $rows1[$j] = array_merge($rows1[$j], array($modelo['fecha_2']=>$my_input));
          $monto += number_format((float)$input_valor, 2, '.', '');
          $r++;
        }

      $rows1[$j] = array_merge($rows1[$j],array('totales'=>'<div class="input-group input-group-sm"><span class="input-group-addon" id="sizing-addon4">$</span><input data-tipo="total" id="totales'.$j.'" value="'.number_format((float)$monto, 2, '.', '').'" name="presupuesto_cuentas[montos][]" text="text" readonly class="form-control moneda" aria-describedby="sizing-addon4" /></div>'));
     $j++;
    }

    $rows = $rows1;
    array_push($colNames,'Totales');
    array_push($colModel,array('name' => 'totales','index' => 'totales','width' => '150'));
    $response = array('colName'=>$colNames,'colModel'=>$colModel,'total'=>count($rows),'page'=>1,'record'=>count($rows), 'rows'=>$rows);
    return $response;


  }

  function avance(){
      $colNames = array("",'Codigo','Cuentas','Presupuesto','% de avance actual','&Uacute;ltima actualizaci&oacute;n');
      $colModel = array(
                  array('name'=>'cuenta_id','index'=>'cuenta_id','hidelg' => true,'key' =>true, 'hidden' => true),      array('name'=>'codigo','index'=>'codigo','width' => '100'),
                  array('name'=>'nombre','index'=>'nombre','width' => '240'),
                  array('name'=>'presupuesto','index'=>'presupuesto','width' => '200'),
                  array('name'=>'porcentaje_avance','index'=>'porcentaje_avance','width' => '200'),
                  array('name'=>'ultima_actualizacion','index'=>'ultima_actualizacion','width' => '200'),
                );

        $lista_presupuesto = $this->presupuesto->lista_presupuesto;
        $j=0;
        $rows = array();
        foreach($lista_presupuesto as $jqgrid){
          array_push($rows,array(
              'cuenta_id'=>'<input data-tipo="id" type="hidden" name="presupuesto_cuentas[cuenta_id][]" id="cuentas'.$j.'" value="'.$jqgrid->cuentas_id.'"/><input data-tipo="id" type="hidden" name="presupuesto_cuentas[][id]" id="cuentas'.$j.'" value="'.$jqgrid->id.'"/>', 'codigo'=>$jqgrid->cuentas->codigo,'nombre'=>$jqgrid->cuentas->nombre,
              'presupuesto'=>'<div class="input-group input-group-sm"><span class="input-group-addon" id="sizing-addon4">$</span><input data-tipo="presupuesto" id="presupuesto'.$j.'" name="presupuesto_cuentas[montos][]" text="text" value="'.$jqgrid->montos.'" class="form-control moneda" aria-describedby="sizing-addon4" /></div>',
          'porcentaje_avance'=>'<div class="input-group input-group-sm"><input data-tipo="porcentaje" id="porcentaje'.$j.'" name="presupuesto_cuentas[porcentaje][]" text="text" value="'.$jqgrid->porcentaje.'" class="form-control porcentaje" /><span class="input-group-addon">%</span></div>',
          'ultima_actualizacion'=>'<div class="input-group input-group-sm"><span class="input-group-addon" id="sizing-addon4"><i class="fa fa-calendar"></i></span><input data-tipo="ultima_actualizacion" id="ultima_actualizacion'.$j.'" name="presupuesto_cuentas[ultima_actualizacion][]" text="text"  class="form-control"  value="'.$jqgrid->updated_at->toDateTimeString().'" aria-describedby="sizing-addon4" readonly /></div>'));
          $j++;

        }
    return $response = array('colName'=>$colNames,'colModel'=>$colModel,'total'=>count($rows),'page'=>1,'record'=>count($rows), 'rows'=>$rows);
  }

  private function totales_color($presupuesto, $total_gastado){
    if ($total_gastado == 0) return 'totales-success';
    if ($presupuesto == 0) return 'totales-danger';
    $total = $total_gastado / $presupuesto;

    if($total > 0 && $total <= 0.33){
      return 'totales-success';
    }elseif($total > 0.33 && $total <= 0.67){
      return 'totales-warning';
    }else{
      return 'totales-danger';
    }

  }



}
