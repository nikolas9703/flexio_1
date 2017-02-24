<?php
namespace Flexio\Modulo\FacturasSeguros\Services;

class FacturaSeguroEstado
{
    
    private $type;
    private $valor;
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getValor()
    {
        return $this->valor;
    }
    
    public function setType($type)
    {
        return $this->type = $type;
    }
    
    public function setValor($valor)
    {
        return $this->valor = $valor;
    }
    
    public function getValorSpan()
    {
        $lookupArray = [
            '13'    => 'FacturaSeguroEstadoPoraprobar',
            '14'    => 'FacturaSeguroEstadoPorpagar',
            '15'    => 'FacturaSeguroEstadoPagadaparcial',
            '16'    => 'FacturaSeguroEstadoPagadacompleta',
            '17'    => 'FacturaSeguroEstadoAnulada'
        ];

        if( ! array_key_exists($this->type, $lookupArray)) {
            throw new \RuntimeException('Enlace roto, factura sin estado');
        }

        $className = "Flexio\\Modulo\\FacturasSeguros\\Services\\" . $lookupArray[$this->type];

        return ( new $className )->getValorSpan($this);
    }
}
