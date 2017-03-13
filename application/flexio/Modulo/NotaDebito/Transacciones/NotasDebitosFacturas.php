<?php

namespace Flexio\Modulo\NotaDebito\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class NotasDebitosFacturas {

    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function haceTransaccion($nota_debito)
    {
        $clause      = [
            "empresa_id"    => $nota_debito->empresa_id,
            "nombre"        => 'TransaccionNotaDebito'.'-'.$nota_debito->codigo.'-'.$nota_debito->empresa_id,
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


    private function _debito($nota_debito)
    {

        if($nota_debito->a_proveedor){
            return $this->debitoCuandoEsProveedor($nota_debito);
        }
        return $this->debitoCuandoEsfactura($nota_debito);
    }

    private function _credito($nota_debito){

        $asientos   = [];

        foreach($nota_debito->items as $item)
        {
            $cuenta_id  = $this->_getCuentaIdCredito($nota_debito, $item);
            if(empty($cuenta_id)){throw new \Exception('No se logr&oacute; determinar la cuenta para realizar el cr&eacute;dito.');}
            $asientos[] = new AsientoContable([
                'codigo' => $nota_debito->codigo,
                'nombre' => $nota_debito->codigo .' - '.$nota_debito->nombre_proveedor,
                'credito' => $item->monto,
                'cuenta_id' => $cuenta_id,
                'empresa_id' => $nota_debito->empresa_id
            ]);
            if(!$nota_debito->a_proveedor)
            {
                $asientos[] = new AsientoContable([
                    'codigo' => $nota_debito->codigo,
                    'nombre' => $nota_debito->codigo,
                    'credito' => $item->impuesto_total,
                    'cuenta_id' => $item->impuesto->cuenta_id,
                    'empresa_id' => $nota_debito->empresa_id
                ]);

                $asientos[] = new AsientoContable([
                    'codigo' => $nota_debito->codigo,
                    'nombre' => $nota_debito->codigo,
                    'credito' => round(  $item->impuesto_total * ($item->impuesto->porcentaje_retenido / 100), 2, PHP_ROUND_HALF_UP),
                    'cuenta_id' => $item->impuesto->cuenta_retenida_id,
                    'empresa_id' => $nota_debito->empresa_id
                ]);


            }

        }



        return $asientos;
    }

    private function _getCuentaIdDebito($nota_debito, $item)
    {
        if($nota_debito->a_proveedor)
        {
            return $item->cuenta_id;
        }
        return $nota_debito->empresa->cuenta_por_pagar_proveedores->first()->cuenta_id;
    }

    private function _getCuentaIdCredito($nota_debito, $item)
    {
        if(!$nota_debito->a_proveedor)
        {
            return $item->cuenta_id;
        }
        return $nota_debito->empresa->cuenta_por_pagar_proveedores->first()->cuenta_id;
    }

    function debitoCuandoEsProveedor($nota_debito){
        $asientos   = [];

        foreach($nota_debito->items as $item)
        {
            $cuenta_id  = $this->_getCuentaIdDebito($nota_debito, $item);
            if(empty($cuenta_id)){throw new \Exception('No se logr&oacute; determinar la cuenta para realizar el debito.');}
            $asientos[] = new AsientoContable([
                'codigo' => $nota_debito->codigo,
                'nombre' => $nota_debito->codigo,
                'debito' =>  $item->monto,
                'cuenta_id' => $cuenta_id,
                'empresa_id' => $nota_debito->empresa_id
            ]);
        }

        return $asientos;
    }

    function debitoCuandoEsfactura($nota_debito){
        $asientos   = [];

        $cuenta_id  = $this->_getCuentaIdDebito($nota_debito, null);
        if(empty($cuenta_id)){throw new \Exception('No se logr&oacute; determinar la cuenta para realizar el debito.');}
        $asientos[] = new AsientoContable([
            'codigo' => $nota_debito->codigo,
            'nombre' => $nota_debito->codigo,
            'debito' =>  $nota_debito->total,
            'cuenta_id' => $cuenta_id,
            'empresa_id' => $nota_debito->empresa_id
        ]);

        $asientos[] = new AsientoContable([
            'codigo' => $nota_debito->codigo,
            'nombre' => $nota_debito->codigo,
            'debito' =>  $nota_debito->retenido,
            'cuenta_id' => $cuenta_id,
            'empresa_id' => $nota_debito->empresa_id
        ]);

        return $asientos;
    }

}
