<?php
namespace Flexio\Modulo\FacturasVentas\Services;

class FacturaVentaEstadoPoraprobar extends FacturaVentaEstadoTipo
{
    
    public function getValorSpan(FacturaVentaEstado $estado)
    {
        $valor      = $estado->getValor();
        $background = "#EBAD50";
     
        //llamar a una clase encardada de imprimir html
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$valor.'</span>';
    }
}
