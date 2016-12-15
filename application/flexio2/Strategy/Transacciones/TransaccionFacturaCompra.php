<?php
namespace Flexio\Strategy\Transacciones;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as Transaccion_orm;
use Illuminate\Database\Capsule\Manager as Capsule;
// cargar el modelo de codeigniter
class TransaccionFacturaCompra implements InterfaceTransaccion{

  function hacerTransaccion($modelo){


    $encontrar = SysTransaccionRepository::findByNombre('TransaccionFacturaCompra'.'-'.$modelo->codigo.'-'.$modelo->empresa_id);

    if($encontrar == 0){
      $sysTransaccion = new SysTransaccionRepository;
        $modeloSysTransaccion="";
        $infoSysTransaccion = array('codigo'=>'Sys', 'nombre'=>'TransaccionFacturaCompra'.'-'.$modelo->codigo.'-'.$modelo->empresa_id,'empresa_id'=>$modelo->empresa_id);
      Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $modelo){

          $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
          $modeloSysTransaccion->transaccion()->saveMany($this->iteams_transacciones($modelo));
          if(is_null($modeloSysTransaccion)){
            throw new \Exception('No se pudo hacer la transacción');
          }
      });
    }
  }

  function iteams_transacciones($modelo){
    $asientos = array();
    $cuenta_id = $modelo->empresa->cuenta_por_pagar->cuenta_id;
    
    $asientos[] = new Transaccion_orm (array('codigo'=>$modelo->codigo, 'nombre'=>$modelo->codigo. ' '. $modelo->proveedor->nombre, 'credito'=>$modelo->total, 'cuenta_id'=>$cuenta_id, 'empresa_id'=>$modelo->empresa_id));
    foreach ($modelo->items as  $value) {
      $valuearr = $value->toArray();
      $itemInfo = $value->inventario_item;

      $atributesItem = array('nombre'=>$modelo->codigo,'debito'=> $valuearr["pivot"]["total"] ,'centro_id'=>$modelo->centro_contable_id,'cuenta_id'=>$valuearr["pivot"]["cuenta_id"],'empresa_id'=>$modelo->empresa_id);
        $asientos[] = new Transaccion_orm($atributesItem);
      $impuesto = $value;
      $impuestoarr = $impuesto->toArray();
        $atributesImpuesto = array('nombre'=>$impuesto->impuesto->nombre, 'debito'=> $value->pivot->impuestos,'centro_id'=> $modelo->centro_contable_id,'cuenta_id'=>$value->impuesto->cuenta_id,'empresa_id'=>$modelo->empresa_id);
      $asientos[] = new Transaccion_orm($atributesImpuesto);
    }
    return $asientos;
  }

  function items($articulos, $modelo){
    $asientos = null;
        $itemInfo = $articulos->inventario_item;

        $atributesItem = array('nombre'=>$modelo->codigo.' '.$itemInfo->codigo,'debito'=> $articulos->total ,'centro_id'=>$modelo->centro_contable_id,'cuenta_id'=>$articulos->cuenta_id,'empresa_id'=>$modelo->empresa_id);
        $asientos = new Transaccion_orm($atributesItem);

    return $asientos;
  }

  function impuesto($item, $modelo){
    $asientos = null;
    $impuesto = $item->impuesto->toArray();
      $atributesImpuesto = array('nombre'=>$impuesto["nombre"], 'debito'=> $item->impuestos ,'centro_id'=> $modelo->centro_contable_id,'cuenta_id'=>$impuesto["cuenta_id"],'empresa_id'=>$modelo->empresa_id);
    $asientos  = new Transaccion_orm($atributesImpuesto);
  }

}
