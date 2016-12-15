<?php
namespace Flexio\Modulo\FacturasVentas\Services;

class FacturaVentaEstadoPagadaparcial extends FacturaVentaEstadoTipo
{
    
    public function getValorSpan(FacturaVentaEstado $estado)
    {
        $valor      = $estado->getValor();
        $background = "#23C6C8";
     
        //llamar a una clase encardada de imprimir html
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$valor.'</span>';
    }
}
