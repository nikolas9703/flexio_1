<?php

namespace Flexio\Modulo\FacturasCompras\TransaccionesAnular;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;

class Recibido {

    protected $SysTransaccionRepository;
    protected $ImpuestosRepositoy;

    public function __construct()
    {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
        $this->ImpuestosRepositoy       = new ImpuestosRepository();
    }

    public function debito($item, $factura_compra)
    {
        $cuenta = $this->_getCuentaDebito($factura_compra);

        return new AsientoContable([
            'codigo'        => $factura_compra->codigo,
            'nombre'        => "Anulacion ".$factura_compra->codigo. ' - '.str_replace('"'," ",str_replace("'"," ",$item->nombre)),
            'credito'        => $item->pivot->subtotal - $item->pivot->descuentos,
            'cuenta_id'     => $cuenta->id,
            'centro_id'     => $factura_compra->centro_contable_id,
            'empresa_id'    => $factura_compra->empresa_id,
            'created_at'    => date('Y-m-d H:i:s', strtotime($factura_compra->fecha_desde))
        ]);

    }

    private function _getCuentaDebito($factura_compra)
    {
        $cuentas = $factura_compra->empresa->cuenta_inventario_por_pagar;
        if(!(count($cuentas) && count($cuentas->first()->cuenta)))
        {
            throw new \Exception('No se logr&oacute; determinar la cuenta de inventario por pagar.');
        }
        return $cuentas->first()->cuenta;
    }

}
