<?php

namespace Flexio\Modulo\Abonos\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class AbonosProveedor {

    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function haceTransaccion($abono)
    {
        $clause      = [
            "empresa_id"    => $abono->empresa_id,
            "nombre"        => 'TransaccionAbono'.'-'.$abono->codigo.'-'.$abono->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$abono->empresa_id,'linkable_id'=>$abono->id,'linkable_type'=> get_class($abono));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $abono){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($abono));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }

    }

    public function deshaceTransaccion($abono)
    {
        //...
    }

    public function transacciones($abono)
    {
        return array_merge($this->_debito($abono),$this->_credito($abono));
    }


    private function _debito($abono)
    {

        $cuenta_id  = $this->_getCuentaIdDebito($abono);
        $asientos   = [];

        $asientos[] = new AsientoContable([
            'codigo'        => $abono->codigo,
            'nombre'        => $abono->codigo. ' - '.$abono->proveedor->nombre,
            'debito'        => $abono->monto_abonado,
            'cuenta_id'     => $cuenta_id,
            'empresa_id'    => $abono->empresa_id
        ]);

        return $asientos;
    }

    private function _credito($abono){

        $cuenta_id  = $this->_getCuentaIdCredito($abono);
        $asientos   = [];

        $asientos[] = new AsientoContable([
            'codigo'        => $abono->codigo,
            'nombre'        => $abono->codigo. " - " .$abono->proveedor->nombre,
            'credito'       => $abono->monto_abonado,
            //'cuenta_id'     => $cuenta_id,
            'cuenta_id'     => "",
            'empresa_id'    => $abono->empresa_id
        ]);

        return $asientos;
    }

    private function _getCuentaIdDebito($abono)
    {
        return  $abono->empresa->cuenta_abonar_proveedores->first()->cuenta_id;
    }

    private function _getCuentaIdCredito($abono)
    {
        return $abono->cuenta_id;
    }

}
