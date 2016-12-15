<?php
namespace Flexio\Modulo\FacturasVentas\Services;

class FacturaVentaEstadoPorpagar extends FacturaVentaEstadoTipo
{
    
    public function getValorSpan(FacturaVentaEstado $estado)
    {
        $valor      = $estado->getValor();
        $background = "#1C84C6";
     
        //llamar a una clase encardada de imprimir html
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$valor.'</span>';
    }
}
