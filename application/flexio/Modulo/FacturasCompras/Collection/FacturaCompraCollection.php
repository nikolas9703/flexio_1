<?php

namespace Flexio\Modulo\FacturasCompras\Collection;

use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;

class FacturaCompraCollection
{
    protected $scope;

    public function __construct(FacturaCompra $scope)
    {
        $this->scope = $scope;
    }

    public function __get($property)
	{

		if (method_exists($this, $property)){
			return call_user_func([$this,$property]);
		}
		$message = '%s does not respond to the "%s" property or method.';
		throw new \Exception(sprintf($message, static::class, $property));
	}

    public function nota_credito_aplicable_factura()
    {
        return Collect([
            'factura_id' => $this->scope->id,
            'credito_favor' => count($this->scope->proveedor) ? $this->scope->proveedor->credito : 0,
            'notas_debito' => count($this->scope->proveedor) ? $this->scope->proveedor->notaDebito->filter(function($nota_debito){
                return $nota_debito->saldo > 0;
            })->map(function($nota_debito){
                return array_merge($nota_debito->toArray(), ['saldo' => $nota_debito->saldo, 'aplicable_type' => get_class($nota_debito)]);
            }) : []
        ]);
    }
}
