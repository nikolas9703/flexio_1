<?php
namespace Flexio\Modulo\ReporteFinanciero\Formato;

class FormatoArray{
  protected static $listaArray=[];

  public function OrdenarArray($cuentas){

    self::$listaArray = [];
    foreach($cuentas as $cuenta){
      if($cuenta->padre_id == 0){
        array_push(self::$listaArray,$cuenta);
        $this->pushToArray($cuenta->id, $cuentas);
      }
    }
    return self::$listaArray;
  }

  public function pushToArray($padre_id,$cuentas){
    foreach($cuentas as $cuenta){
      if($cuenta->padre_id == $padre_id){
          array_push(self::$listaArray,$cuenta);
          $this->pushToArray($cuenta->id, $cuentas);
      }
    }
  }

  public function estadoBalance($detalle,$cod_factura ="FT",$cod_pago = "PGO"){

    $sorted = $detalle;
    $balance = 0;
    $lista_balance = $sorted->toArray();

    foreach($lista_balance as $key=>$item){
      if(starts_with($item['detalle'],'balance')){
        $balance = (float)$item['balance'];
      }
       if(starts_with($item['codigo'],$cod_factura)){
          $balance += (float)$item['total'] ;
          $lista_balance[$key]['balance'] = $balance;
        }

        if(starts_with($item['codigo'],$cod_pago)){
          $balance += -(float)$item['total'];
          $lista_balance[$key]['balance'] = $balance;
        }
    }

   return $lista_balance;
  }


  function formatoAntiguedad($datos,$key = 'facturas',$tipo='proveedor'){
    $detalles = [];  
    foreach($datos as $obj){
      //proveedor
       array_push($detalles,['id'=>$obj->id,'nombre'=>$obj->nombre,
       'corriente'=>collect($obj->facturas)->sum('corriente'),
       '30_dias'=> collect($obj->facturas)->sum('30_dias'),
       '60_dias'=> collect($obj->facturas)->sum('60_dias'),
       '90_dias'=>collect($obj->facturas)->sum('90_dias'),
       '120_dias'=>collect($obj->facturas)->sum('120_dias'),
       'tipo'=>$tipo,
       'padre_id'=> 0]);
      if(!empty($obj->$key)){
        ///facturas
        foreach($obj->$key as $fac){
          array_push($detalles,['id'=>$fac->id,'nombre'=>$fac->codigo,
          'corriente'=>$fac->corriente,
          '30_dias'=>$fac->{'30_dias'},
          '60_dias'=>$fac->{'60_dias'},
          '90_dias'=>$fac->{'90_dias'},
          '120_dias'=>$fac->{'120_dias'},
          'tipo'=>'factura',
          'padre_id'=>$obj->id]);
        }
      }
    }
    return $detalles;
  }
}
