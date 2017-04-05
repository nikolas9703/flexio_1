<?php
//namespace Repository\Cobros;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Support\Collection as Collection;
use Carbon\Carbon as Carbon;


class Guardar_cobro
{

  function guardar(Cobro_orm $cobro,$posts) {
    $array_cobro = Util::set_fieldset("campo");
    $array_cobro['fecha_pago'] = $posts['campo']['fecha_pago'];
    $array_cobro['fecha_desde'] = Carbon::createFromFormat('m/d/Y',$array_cobro['fecha_pago'],'America/Panama');
    $array_cobro['empresa_id'] = $this->empresa_id;
    $total = Cobro_orm::where('empresa_id','=',$this->empresa_id)->count();
    $year = Carbon::now()->format('y');
    $codigo = Util::generar_codigo('PAY'.$year,$total + 1);
    $array_cobro['codigo'] = $codigo;

    $cobro = Cobro_orm::create($array_factura);
    $cobro = $cobro->fresh();
    return $cobro;
  }

  function condicion_cobro($total_cobrado, $total_factura) {

  /*  if($total_cobrado == $total_factura)
    {
      return 'aplicado';
  }elseif($total_cobrado > $total_factura){*/
      //return 'pago_parcial';
  //}elseif($total_cobrado < $total_factura){
      return 'aplicado';
  //}

  }

  function tipo_pago($metodo_pago, array $item) {
    $tipo = array();
    if($metodo_pago == 'cheque')
    {
      $tipo = array('numero_cheque' => $item['numero_cheque'],'nombre_banco_cheque'=> $item['nombre_banco_cheque']);
    }elseif($metodo_pago == 'ach')
    {
      $tipo = array('nombre_banco_ach' => $item['nombre_banco_ach'],'cuenta_cliente'=> $item['cuenta_cliente']);
    }elseif($metodo_pago == 'tarjeta_de_credito')
    {
      $tipo = array('numero_tarjeta' => $item['numero_tarjeta'],'numero_recibo'=> $item['numero_recibo']);
    }

    return  empty($tipo)? '' : json_encode($tipo);
  }

  function actualizar_credito_cliente(Cliente_orm $cliente, $metodo_pago_post) {
    $metodo_pago_post = new Collection($metodo_pago_post);
      $filtered = $metodo_pago_post->where('tipo_pago', 'aplicar_credito');
      $filtered->all();
      $total = $filtered->sum('total_pagado');
      if($total > 0){
        $cliente->credito = $cliente->credito - $total;
        $cliente->save();
      }
  }

  function actualizar_estado_factura(Factura_orm $factura,$estado_cobro) {
    if($estado_cobro =='pago_parcial'){
      $factura->estado = 'facturada';
      $factura->save();
    }elseif($estado_cobro =='aplicado'){
        $factura->estado = 'pagada';
        $factura->save();
    }
  }

  function actualizar_estados(Cobro_orm $cobro,$facturaId) {
    $factura_estado= array();
    $estado="";
    $me_estado="";
  foreach($facturaId as $id){
    $factura = Factura_orm::find($id);
        if($factura->total_facturado() == $factura->total){
          $estado = 'pagada';
          array_push($factura_estado,$estado);
        }else{
          $estado = 'facturada';
          array_push($factura_estado,$estado);
        }
        $factura->estado = $estado;
        $factura->save();
  }

  foreach($factura_estado as $cobro_stado){
      if($cobro_stado=='pagada'){
          $me_estado='aplicado';
      }elseif($cobro_stado=='facturada'){
          $me_estado='pago_parcial';
      }
  }
  $cobro->estado = $me_estado;
  $cobro->save();

  }


}
