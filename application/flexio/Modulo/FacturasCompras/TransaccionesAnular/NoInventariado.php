<?php

namespace Flexio\Modulo\FacturasCompras\TransaccionesAnular;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;

class NoInventariado {

    protected $SysTransaccionRepository;
    protected $ImpuestosRepository;
    protected $CuentasRepository;

    public function __construct()
    {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
        $this->ImpuestosRepository      = new ImpuestosRepository();
        $this->CuentasRepository        = new CuentasRepository();
    }

    public function debito($item, $factura_compra)
    {
        $cuenta = $this->_getCuentaDebito($item);

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

    private function _getCuentaDebito($item)
    {
        $cuenta = $this->CuentasRepository->find($item->pivot->cuenta_id);
        if(!count($cuenta))
        {
            throw new \Exception('No se logr&oacute; determinar la cuenta de costo del articulo.');
        }
        return $cuenta;
    }

}
