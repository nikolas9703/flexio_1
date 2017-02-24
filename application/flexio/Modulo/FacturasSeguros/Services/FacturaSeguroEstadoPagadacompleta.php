<?php
namespace Flexio\Modulo\FacturasSeguros\Services;

class FacturaSeguroEstadoPagadacompleta extends FacturaSeguroEstadoTipo
{
    
    public function getValorSpan(FacturaSeguroEstado $estado)
    {
        $valor      = $estado->getValor();
        $background = "#1AB394";
     
        //llamar a una clase encardada de imprimir html
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$valor.'</span>';
    }
}
