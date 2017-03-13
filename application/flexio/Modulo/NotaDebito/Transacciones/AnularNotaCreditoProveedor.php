<?php

namespace Flexio\Modulo\NotaDebito\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class AnularNotaCreditoProveedor{

	protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }



    public function haceTransaccion($nota_debito)
    {
        $clause      = [
            "empresa_id"    => $nota_debito->empresa_id,
            "nombre"        => 'AnularNotaDebito'.'-'.$nota_debito->codigo.'-'.$nota_debito->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$nota_debito->empresa_id,'linkable_id'=>$nota_debito->id,'linkable_type'=> get_class($nota_debito));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $nota_debito){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($nota_debito));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }
    }




    public function transacciones($nota_debito)
    {
        return array_merge($this->_debito($nota_debito),$this->_credito($nota_debito));
    }


    public function _debito($nota_debito){

    	$asientos   = [];

        foreach($nota_debito->items as $item)
        {
        	
        	$cuenta_id  = $item->cuenta_id;

        	if(empty($cuenta_id)){throw new \Exception('No se logr&oacute; determinar la cuenta para realizar el cr&eacute;dito.');}
            
            // total del items
            $asientos[] = new AsientoContable([
                'codigo' => $nota_debito->codigo,
                'nombre' => 'Anulacion '.$nota_debito->codigo.' - '.$nota_debito->nombre_proveedor,
                'debito' => $item->monto,
                'centro_id' => $nota_debito->centro_contable_id,
                'cuenta_id' => $cuenta_id,
                'created_at' => $nota_debito->fecha_nota_credito,
                'empresa_id' => $nota_debito->empresa_id
            ]);

        	// impuesto del item
        	$asientos[] = new AsientoContable([
                'codigo' => $nota_debito->codigo,
                'nombre' => 'Anulacion '.$nota_debito->codigo.' - '.$nota_debito->nombre_proveedor,
                'debito' => $item->impuesto_total,
                'centro_id' => $nota_debito->centro_contable_id,
                'cuenta_id' => $item->impuesto->cuenta_id,
                'created_at' => $nota_debito->fecha_nota_credito,
                'empresa_id' => $nota_debito->empresa_id
            ]);
        	

        }


         return $asientos;
    }


    public function _credito($nota_debito){

    	    $cuenta_id  = $this->_getCuentaIdDebito($nota_debito);


            if(empty($cuenta_id)){throw new \Exception('No se logr&oacute; determinar la cuenta para realizar el debito.');}


            $asientos[] = new AsientoContable([
                'codigo' => $nota_debito->codigo,
                'nombre' => 'Anulacion '.$nota_debito->codigo.' - '.$nota_debito->nombre_proveedor,
                'credito' =>  $nota_debito->total - $nota_debito->retenido,
                'cuenta_id' => $cuenta_id,
                'centro_id' => $nota_debito->centro_contable_id,
                'created_at' => $nota_debito->fecha_nota_credito,
                'empresa_id' => $nota_debito->empresa_id
            ]);


            ///si el proveedor no retiene impuesto
            foreach($nota_debito->items as $item)
            {
                if($nota_debito->proveedor->retiene_impuesto == "no" && $item->impuesto->retiene_impuesto == "si"){    
                    $asientos[] = new AsientoContable([
                        'codigo' => $nota_debito->codigo,
                        'nombre' => 'Anulacion '.$nota_debito->codigo.' - '.$nota_debito->nombre_proveedor,
                        'credito' => round(  $item->impuesto_total * ($item->impuesto->porcentaje_retenido / 100), 2, PHP_ROUND_HALF_UP),
                        'cuenta_id' => $item->impuesto->cuenta_retenida_id,
                        'centro_id' => $nota_debito->centro_contable_id,
                        'created_at' => $nota_debito->fecha_nota_credito,
                        'empresa_id' => $nota_debito->empresa_id
                    ]);
                }
            }
            return $asientos;
    }



    private function _getCuentaIdDebito($nota_debito)
    {
        
        return $nota_debito->empresa->cuenta_por_pagar_proveedores->first()->cuenta_id;
    }




}