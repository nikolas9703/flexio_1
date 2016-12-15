<?php
namespace Flexio\Modulo\Base\Services;

class Numero
{
    
    private $tipo;
    private $numero;
    
    public function __construct($tipo, $numero)
    {
        $this->tipo     = $tipo;
        $this->numero   = $numero;
    }
    
    public function getNumero()
    {
        return $this->numero;
    }
    
    public function getSalida()
    {
        $lookupArray = [
            'moneda'    => 'NumeroTipoMoneda'
        ];

        if( ! array_key_exists($this->tipo, $lookupArray)) {
            throw new \RuntimeException('tipo de numero incorrecto');
        }

        $className = "Flexio\\Modulo\\Base\\Services\\" . $lookupArray[$this->tipo];

        return ( new $className )->getSalida($this);
    }
}
