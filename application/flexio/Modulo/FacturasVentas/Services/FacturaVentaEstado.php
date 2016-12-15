<?php
namespace Flexio\Modulo\FacturasVentas\Services;

class FacturaVentaEstado
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
            '13'    => 'FacturaVentaEstadoPoraprobar',
            '14'    => 'FacturaVentaEstadoPorpagar',
            '15'    => 'FacturaVentaEstadoPagadaparcial',
            '16'    => 'FacturaVentaEstadoPagadacompleta',
            '17'    => 'FacturaVentaEstadoAnulada'
        ];

        if( ! array_key_exists($this->type, $lookupArray)) {
            throw new \RuntimeException('Enlace roto, factura sin estado');
        }

        $className = "Flexio\\Modulo\\FacturasVentas\\Services\\" . $lookupArray[$this->type];

        return ( new $className )->getValorSpan($this);
    }
}
