<?php

namespace Flexio\Modulo\Entradas\Transaccion;

use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Illuminate\Database\Capsule\Manager as Capsule;

class TransaccionFactura{

  public function __construct()
  {
    $this->SysTransaccionRepository = new SysTransaccionRepository();
  }


 	public function hacerTransaccion( $registro, $cantidad_recibida=NULL )
	{
    //throw new \Exception('Refactory');
		//Una operacion puede ser un traslado o una orden de compra
    //solo desde una orden de compra genera una afectacion contable
    $facturas_count = count($registro->operacion->facturas); //contar las validas
    $clause = ["empresa_id" => $registro->empresa_id];

    if($facturas_count == 0)
    {//no tiene facturas
      $clause["nombre"] = "TransaccionFacturaEntrada-sf-{$registro->id}-{$registro->empresa_id}";
   		$infoSysTransaccion = ['codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$registro->empresa_id,'linkable_id'=>$registro->id,'linkable_type'=> get_class($registro)];
    }
    else
    {//si tiene facturas
      $factura_compra = $registro->operacion->facturas->first();
      $clause["nombre"] = "TransaccionFacturaEntrada-{$factura_compra->codigo}-{$registro->empresa_id}";
   		$infoSysTransaccion = ['codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$registro->empresa_id,'linkable_id'=>$factura_compra->id,'linkable_type'=> get_class($factura_compra)];
   	}

   	$transaccion = $this->SysTransaccionRepository->findBy($clause);
    //Solo se realiza una vez la transaccion?
    if(!count($transaccion))
   	{
   			$sysTransaccion = new SysTransaccionRepository;
   			$modeloSysTransaccion = "";
   			Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $registro, $facturas_count, $cantidad_restante){
          $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
   				$asientos_array = $this->transaccionesItems($registro, $facturas_count, $cantidad_restante);
   				foreach($asientos_array as $asientos){
   					$modeloSysTransaccion->transaccion()->saveMany($asientos);
   				}
          if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
   			});
      }
   		else
      {
        return false;
   		}

  	}

    public function hacerTransaccionParcial( $registro, $cuenta, $cantidad_recibida=NULL, $cantidad_restante=NULL )
  	{
      //throw new \Exception('Refactory');
  		//Una operacion puede ser un traslado o una orden de compra
      //solo desde una orden de compra genera una afectacion contable
      $facturas_count = count($registro->operacion->facturas); //contar las validas
      $clause = ["empresa_id" => $registro->empresa_id];

      if($facturas_count == 0)
      {//no tiene facturas
        $clause["nombre"] = "TransaccionFacturaEntrada-sf-{$registro->id}-{$registro->empresa_id}";
     		$infoSysTransaccion = ['codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$registro->empresa_id,'linkable_id'=>$registro->id,'linkable_type'=> get_class($registro)];
      }
      else
      {//si tiene facturas
        $factura_compra = $registro->operacion->facturas->first();
        $clause["nombre"] = "TransaccionFacturaEntrada-{$factura_compra->codigo}-{$registro->empresa_id}";
     		$infoSysTransaccion = ['codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$registro->empresa_id,'linkable_id'=>$factura_compra->id,'linkable_type'=> get_class($factura_compra)];
     	}
     	$transaccion = $this->SysTransaccionRepository->findBy($clause);
      //Solo se realiza una vez la transaccion?
        
      if(!count($transaccion))
     	{
     			$sysTransaccion = new SysTransaccionRepository;
     			$modeloSysTransaccion = "";

     			Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $registro, $facturas_count, $cuenta, $cantidad_recibida, $cantidad_restante){
            $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
     				$asientos_array = $this->transaccionesItems($registro, $facturas_count, $cuenta, $cantidad_recibida, $cantidad_restante);
                               
     				foreach($asientos_array as $asientos){
     					$modeloSysTransaccion->transaccion()->saveMany($asientos);
     				}
           
            if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
     			});
        }        
     		else
        {
                    $sysTransaccion = new SysTransaccionRepository;
     			$modeloSysTransaccion = "";
                        
     			Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $registro, $facturas_count, $cuenta, $cantidad_recibida, $cantidad_restante){
            $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
     				$asientos_array = $this->transaccionesItems2($registro, $facturas_count, $cuenta, $cantidad_recibida, $cantidad_restante);
                               
     				foreach($asientos_array as $asientos){
     					$modeloSysTransaccion->transaccion()->saveMany($asientos);
     				}
           
            if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
     			});
                    
     		}

    	}


  	private function transaccionesItems($registro, $facturas_count = 0, $cuenta=NULL, $cantidad_recibida=NULL, $cantidad_restante=NULL){

  			$asiento = array();
  			foreach ($registro->operacion->items as $item){

  				  $item_id 	= $item->id; //Item de entrada
	  			  if($facturas_count > 0)
            {//tiene facturas
              foreach($registro->operacion->facturas as $factura){

	  			  			$_item = $factura->items2->find($item_id);

	  			  			if(count($_item))
                  {//Existe este item en la factura
	  			  				$asiento[] = $this->transaccionesFacturado($registro, $_item, $cuenta, $cantidad_recibida, $cantidad_restante);
 	  			  			}
	  			  			else
                  {//No Existe este item en la factura
                    $asiento[] = $this->transaccionesNoFacturado($registro,  $item, $cuenta, $cantidad_recibida, $cantidad_restante);
  	  			  		}

	  			  	}
	  			  }
            else
            {
	  			  	$asiento[] =   $this->transaccionesNoFacturado($registro,  $item, $cuenta, $cantidad_recibida, $cantidad_restante);
 	  			  }

  			}

   		return $asiento;
  	}
        
        private function transaccionesItems2($registro, $facturas_count = 0, $cuenta=NULL, $cantidad_recibida=NULL, $cantidad_restante=NULL){

  			$asiento = array();
  			foreach ($registro->operacion->items as $item){

  				  $item_id 	= $item->id; //Item de entrada
	  			  if($facturas_count > 0)
            {//tiene facturas
              foreach($registro->operacion->facturas as $factura){

	  			  			$_item = $factura->items2->find($item_id);

	  			  			if(count($_item))
                  {//Existe este item en la factura
	  			  				$asiento[] = $this->transaccionesFacturado2($registro, $_item, $cuenta, $cantidad_recibida, $cantidad_restante);
 	  			  			}
	  			  			else
                  {//No Existe este item en la factura
                    $asiento[] = $this->transaccionesNoFacturado2($registro,  $item, $cuenta, $cantidad_recibida, $cantidad_restante);
  	  			  		}

	  			  	}
	  			  }
            else
            {
	  			  	$asiento[] =   $this->transaccionesNoFacturado2($registro,  $item, $cuenta, $cantidad_recibida, $cantidad_restante);
 	  			  }

  			}

   		return $asiento;
  	}

    //estoy en este metodo
  	public function transaccionesFacturado($registro, $_item, $cuenta=NULL, $cantidad_recibida=NULL, $cantidad_restante=NULL)
 	  {
      return array_merge($this->_debito($registro, $_item, $cuenta, $cantidad_recibida),$this->_credito($registro, $_item, $cuenta, $cantidad_recibida, $cantidad_restante));
 	  }

 	public function transaccionesNoFacturado($registro, $itemNoEncontrado, $cuenta=NULL, $cantidad_recibida=NULL, $cantidad_restante=NULL)
 	{           
            return array_merge( $this->_debito2($registro, $itemNoEncontrado, $cuenta, $cantidad_recibida),$this->_credito2($registro, $itemNoEncontrado, $cuenta, $cantidad_recibida));
 	            
        }
        
        //completando
    public function transaccionesFacturado2($registro, $_item, $cuenta=NULL, $cantidad_recibida=NULL, $cantidad_restante=NULL)
 	  {
      return array_merge($this->_credito1($registro, $_item, $cuenta, $cantidad_restante),$this->_debitoParcial1($registro, $_item, $cuenta, $cantidad_restante));
 	  }

 	public function transaccionesNoFacturado2($registro, $itemNoEncontrado, $cuenta=NULL, $cantidad_recibida=NULL, $cantidad_restante=NULL)
 	{
      	return array_merge($this->_credito2($registro, $itemNoEncontrado, $cuenta, $cantidad_restante), $this->_debitoParcial2($registro, $itemNoEncontrado, $cuenta, $cantidad_restante));
 	}    

 	//Debito para facturados
 	private function _debito($registro, $item, $cuenta=NULL, $cantidad_recibida=NULL)
 	{
    $asientos   = [];
   
  	foreach($registro->operacion->items as $item_factura){
      if(empty($item->cuenta_activo_id)){throw new \Exception("{$item->nombre} no tiene cuenta de activo seleccionada <a href=\"{$item->enlace}\">(Item/Datos generales)</a>");}
   
      $item_factura->pivot->cantidad = $cantidad_recibida != 'NULL' ? $cantidad_recibida : $item_factura->pivot->cantidad;
      if($item->id == $item_factura->id)
      {
        $asientos[] = new AsientoContable([
          'codigo' => $registro->operacion->facturas->first()->codigo,
  				'nombre' => "{$registro->id}-{$item_factura->id}",
  				'debito' => $item_factura->pivot->cantidad * $item_factura->pivot->precio_unidad,
   				'cuenta_id' => $item->cuenta_activo_id,
  				'empresa_id' => $item->empresa_id
  			]);
   		}

   	}

    return $asientos;
 	}

  //credito para facturados
 	private function _credito($registro, $item, $cuenta=NULL, $cantidad_recibida=NULL, $cantidad_restante=NULL){
   
    if(!count($registro->empresa->cuenta_inventario_en_transito)){throw new \Exception("Favor configurar cuenta de Pasivo para \"Inventario recibido sin facturar\" (Contabilidad/Configuraci&oacute;n)");}
    $item->pivot->cantidad = !empty($cantidad_restante) ? $cantidad_restante : $item->pivot->cantidad;

    $asientos = [];
    $asientos[] = new AsientoContable([
      'codigo' => $registro->operacion->facturas->first()->codigo,
 			'nombre' => "{$registro->id}-{$item->id}",
 			'credito' => $item->pivot->cantidad * $item->pivot->precio_unidad,
 			'cuenta_id' => $registro->empresa->cuenta_inventario_en_transito->first()->cuenta->id,
 			'empresa_id' => $item->empresa_id
 		]);

    return $asientos;

 	}
        
        //credito para facturados
 	private function _creditoParcial1($registro, $item, $cuenta=NULL, $cantidad_recibida=NULL){
    
    if(!count($registro->empresa->cuenta_inventario_en_transito)){throw new \Exception("Favor configurar cuenta de Pasivo para \"Inventario recibido sin facturar\" (Contabilidad/Configuraci&oacute;n)");}
    $item->pivot->cantidad = !empty($cantidad_recibida) ? $cantidad_recibida : $item->pivot->cantidad;

    $asientos = [];
    $asientos[] = new AsientoContable([
      'codigo' => $registro->operacion->facturas->first()->codigo,
 			'nombre' => "{$registro->id}-{$item->id}",
 			'credito' => 1 * $item->pivot->precio_unidad,
 			'cuenta_id' => $registro->empresa->cuenta_inventario_en_transito->first()->cuenta->id,
 			'empresa_id' => $item->empresa_id
 		]);

    return $asientos;

 	}
        
    private function _creditoParcial2($registro, $itemNoEncontrado, $cuenta=NULL, $cantidad_restante=NULL){
    
    if(!count($registro->empresa->cuenta_inventario_por_pagar)){throw new \Exception("Favor configurar cuenta de Activo para \"Inventario recibido sin facturar\" (Contabilidad/Configuraci&oacute;n)");}
    $itemNoEncontrado->pivot->cantidad = $cantidad_restante != 'NULL' ? $cantidad_restante : $itemNoEncontrado->pivot->cantidad;
    $asientos   = [];
  	$asientos[] = new AsientoContable([
 			'codigo' => count($registro->operacion->facturas) ? $registro->operacion->facturas->first()->codigo : $registro->codigo,
 			'nombre' => "{$registro->id}-{$itemNoEncontrado->id}",
 			'credito' => $itemNoEncontrado->pivot->cantidad * $itemNoEncontrado->pivot->precio_unidad,
 			'cuenta_id' => $cuenta,
 			'empresa_id' => $registro['empresa_id']
 		]);

  	return $asientos;
 	}

 	//Casos en que no hallan sido facturados los items
 	private function _debito2($registro, $itemNoEncontrado, $cuenta=NULL, $cantidad_recibida=NULL)
 	{
   
    $item = $registro->operacion->items->find($itemNoEncontrado->id);
    if(empty($itemNoEncontrado->cuenta_activo_id)){throw new \Exception("{$itemNoEncontrado->nombre} no tiene cuenta de activo seleccionada <a href=\"{$itemNoEncontrado->enlace}\">(Item/Datos generales)</a>");}
    $itemNoEncontrado->pivot->cantidad = $cantidad_recibida != 'NULL' ? $cantidad_recibida : $itemNoEncontrado->pivot->cantidad;
 		$asientos = [];
 		$asientos[] = new AsientoContable([
      'codigo' => count($registro->operacion->facturas) ? $registro->operacion->facturas->first()->codigo : $registro->codigo,
 			'nombre' => "{$registro->id}-{$itemNoEncontrado->id}",
 			'debito' => $itemNoEncontrado->pivot->cantidad * $itemNoEncontrado->pivot->precio_unidad,
  		'cuenta_id' => $cuenta != NULL ? $cuenta : $itemNoEncontrado->cuenta_activo_id,
 			'empresa_id' => $registro['empresa_id']
 		]);

    return $asientos;
 	}
        
    	//Casos en que no hallan sido facturados los items
 	private function _debitoParcial2($registro, $itemNoEncontrado, $cuenta=NULL, $cantidad_recibida=NULL)
 	{
    
    $item = $registro->operacion->items->find($itemNoEncontrado->id);
    if(empty($itemNoEncontrado->cuenta_activo_id)){throw new \Exception("{$itemNoEncontrado->nombre} no tiene cuenta de activo seleccionada <a href=\"{$itemNoEncontrado->enlace}\">(Item/Datos generales)</a>");}
    $itemNoEncontrado->pivot->cantidad = $cantidad_recibida != 'NULL' ? $cantidad_recibida : $itemNoEncontrado->pivot->cantidad;
 		$asientos = [];
 		$asientos[] = new AsientoContable([
      'codigo' => count($registro->operacion->facturas) ? $registro->operacion->facturas->first()->codigo : $registro->codigo,
 			'nombre' => "{$registro->id}-{$itemNoEncontrado->id}",
 			'debito' => $itemNoEncontrado->pivot->cantidad * $itemNoEncontrado->pivot->precio_unidad,
  		'cuenta_id' => $cuenta != NULL ? $cuenta : $itemNoEncontrado->cuenta_activo_id,
 			'empresa_id' => $registro['empresa_id']
 		]);

    return $asientos;
 	}
        
        	//Casos en que no hallan sido facturados los items
 	private function _debitoParcial1($registro, $itemNoEncontrado, $cuenta=NULL, $cantidad_restante=NULL)
 	{
   
    $item = $registro->operacion->items->find($itemNoEncontrado->id);
    if(empty($itemNoEncontrado->cuenta_activo_id)){throw new \Exception("{$itemNoEncontrado->nombre} no tiene cuenta de activo seleccionada <a href=\"{$itemNoEncontrado->enlace}\">(Item/Datos generales)</a>");}
    $itemNoEncontrado->pivot->cantidad = $cantidad_restante != 'NULL' ? $cantidad_restante : $itemNoEncontrado->pivot->cantidad;
 		$asientos = [];
 		$asientos[] = new AsientoContable([
      'codigo' => count($registro->operacion->facturas) ? $registro->operacion->facturas->first()->codigo : $registro->codigo,
 			'nombre' => "{$registro->id}-{$itemNoEncontrado->id}",
 			'debito' => $itemNoEncontrado->pivot->cantidad * $itemNoEncontrado->pivot->precio_unidad,
  		'cuenta_id' => $itemNoEncontrado->cuenta_activo_id,
 			'empresa_id' => $registro['empresa_id']
 		]);

    return $asientos;
 	}    

 	private function _credito2($registro, $itemNoEncontrado, $cuenta=NULL, $cantidad_restante=NULL){
    
    if(!count($registro->empresa->cuenta_inventario_por_pagar)){throw new \Exception("Favor configurar cuenta de Activo para \"Inventario recibido sin facturar\" (Contabilidad/Configuraci&oacute;n)");}
    $itemNoEncontrado->pivot->cantidad = $cantidad_restante != 'NULL' ? $cantidad_restante : $itemNoEncontrado->pivot->cantidad;
    $asientos   = [];
  	$asientos[] = new AsientoContable([
 			'codigo' => count($registro->operacion->facturas) ? $registro->operacion->facturas->first()->codigo : $registro->codigo,
 			'nombre' => "{$registro->id}-{$itemNoEncontrado->id}",
 			'credito' => $itemNoEncontrado->pivot->cantidad * $itemNoEncontrado->pivot->precio_unidad,
 			'cuenta_id' => $registro->empresa->cuenta_inventario_por_pagar->first()->cuenta->id,
 			'empresa_id' => $registro['empresa_id']
 		]);

  	return $asientos;
 	}
        
        private function _credito1($registro, $itemNoEncontrado, $cuenta=NULL, $cantidad_restante=NULL){
    
    if(!count($registro->empresa->cuenta_inventario_por_pagar)){throw new \Exception("Favor configurar cuenta de Activo para \"Inventario recibido sin facturar\" (Contabilidad/Configuraci&oacute;n)");}
    $itemNoEncontrado->pivot->cantidad = $cantidad_restante != 'NULL' ? $cantidad_restante : $itemNoEncontrado->pivot->cantidad;
    $asientos   = [];
  	$asientos[] = new AsientoContable([
 			'codigo' => count($registro->operacion->facturas) ? $registro->operacion->facturas->first()->codigo : $registro->codigo,
 			'nombre' => "{$registro->id}-{$itemNoEncontrado->id}",
 			'credito' => $itemNoEncontrado->pivot->cantidad * $itemNoEncontrado->pivot->precio_unidad,
 			'cuenta_id' => $cuenta != NULL ? $cuenta : $registro->empresa->cuenta_inventario_por_pagar->first()->cuenta->id,
 			'empresa_id' => $registro['empresa_id']
 		]);

  	return $asientos;
 	}


}
