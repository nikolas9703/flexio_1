<?php
namespace Flexio\Modulo\FacturasSeguros\Services;

class FacturaSeguroEstadoAnulada extends FacturaSeguroEstadoTipo
{
    
    public function getValorSpan(FacturaSeguroEstado $estado)
    {
        $valor      = $estado->getValor();
        $background = "#D1DADE";
     
        //llamar a una clase encardada de imprimir html
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$valor.'</span>';
    }
}
