<?php 
namespace Flexio\Modulo\Contratos\Api;
use Flexio\Transformers\TransformerObject;

class ContratoDetalle extends TransformerObject{

     public function transform($contrato)
	{
	    return [
	        'id' => (int) $contrato->id,
            'nombre'=> $contrato->codigo .' - '.$contrato->cliente_nombre,
	        'cliente_id'   =>(int) $contrato->cliente_id,
            'centro_contable_id' => $contrato->centro_id,
	    ];
	}
}