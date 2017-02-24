<?php
namespace Flexio\Modulo\FacturasSeguros\Services;

class FacturaSeguroEstadoPorpagar extends FacturaSeguroEstadoTipo
{
    
    public function getValorSpan(FacturaSeguroEstado $estado)
    {
        $valor      = $estado->getValor();
        $background = "#1C84C6";
     
        //llamar a una clase encardada de imprimir html
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$valor.'</span>';
    }
}
