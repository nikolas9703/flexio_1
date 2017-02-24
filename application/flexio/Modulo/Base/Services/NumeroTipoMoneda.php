<?php
namespace Flexio\Modulo\Base\Services;

class NumeroTipoMoneda extends NumeroTipo
{
    
    public function getSalida(Numero $numero) 
    {
        return "$".number_format($numero->getNumero(), 2, '.', ',');
    }
}