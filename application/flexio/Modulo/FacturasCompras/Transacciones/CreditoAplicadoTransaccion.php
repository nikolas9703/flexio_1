<?php

namespace Flexio\Modulo\FacturasCompras\Transacciones;

use Flexio\Modulo\Transaccion\Models\SysTransaccion;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Modulo\CreditosAplicados\Models\CreditoAplicado;
use Illuminate\Database\Capsule\Manager as Capsule;

class CreditoAplicadoTransaccion
{
    public function hacerTransaccion(CreditoAplicado $modelo)
    {
        //datos de SysTransaccion
        $infoSysTransaccion = ['codigo' => 'Sys', 'nombre' => 'transaccionCreditoAplicado'.'-'.$modelo->acreditable->codigo.'-'.$modelo->empresa_id,
        'empresa_id' => $modelo->empresa_id, 'linkable_id' => $modelo->acreditable->id, 'linkable_type' => get_class($modelo->acreditable)];
        //realiza la transaccion del sistema
        Capsule::transaction(function () use ($modelo, $infoSysTransaccion) {
            $modeloSysTransaccion = SysTransaccion::create($infoSysTransaccion);
            $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($modelo));

            if (is_null($modeloSysTransaccion)) {
                throw new \Exception('No se pudo hacer la transacci&oacute;n contable');
            }
        });
    }

    public function transacciones($modelo)
    {
        return array_merge($this->debito($modelo), $this->acredito($modelo));
    }

    public function debito($modelo)
    {
        $cuenta_id = $this->_getCuentaIdDebito($modelo);
        $asientos = [];

        $asientos[] = new AsientoContable([
            'codigo' => $modelo->acreditable->codigo,
            'nombre' => $modelo->acreditable->codigo.' - '.$modelo->acreditable->proveedor->nombre,
            'debito' => $modelo->total,
            'cuenta_id' => $cuenta_id,
            'empresa_id' => $modelo->empresa_id,
        ]);

        return $asientos;
    }

    public function acredito($modelo)
    {
        $cuenta_id = $this->_getCuentaIdCredito($modelo);
        $asientos = [];

        $asientos[] = new AsientoContable([
            'codigo' => $modelo->acreditable->codigo,
            'nombre' => $modelo->acreditable->codigo.' - '.$modelo->acreditable->proveedor->nombre,
            'credito' => $modelo->total,
            'cuenta_id' => $cuenta_id,
            'empresa_id' => $modelo->empresa_id,
        ]);

        return $asientos;
    }

    private function _getCuentaIdDebito($modelo)
    {
        if(!count($modelo->empresa->cuenta_por_pagar_proveedores))throw new \Exception('No se ha asignado la cuenta por pagar a proveedores en el sistema.');
        return  $modelo->empresa->cuenta_por_pagar_proveedores->first()->cuenta_id;
    }

    private function _getCuentaIdCredito($modelo)
    {
        if(!count($modelo->empresa->cuenta_abonar_proveedores))throw new \Exception('No se ha asignado la cuenta para anticipos a proveedores en el sistema.');
        return  $modelo->empresa->cuenta_abonar_proveedores->first()->cuenta_id;
    }
}
