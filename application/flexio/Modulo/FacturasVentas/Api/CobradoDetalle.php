<?php
namespace Flexio\Modulo\FacturasVentas\Api;
use Flexio\Transformers\TransformerObject;

class CobradoDetalle extends TransformerObject{

    public function transform($factura)
	{
	    return [
	        'id'      => (int) $factura->id,
            'nombre'=> $factura->codigo .' - '.$factura->cliente_nombre,
	        'cliente_id'   =>(int) $factura->cliente_id,
	        'termino_pago'    =>  $factura->termino_pago,
            'fecha_desde'   => $factura->fecha_desde,
            'fecha_hasta' => $factura->fecha_hasta,
            'created_by' => $factura->created_by,
            'item_precio_id' => $factura->item_precio_id,
            'centro_contable_id' => $factura->centro_contable_id,
            'centro_facturacion_id' => $factura->centro_facturacion_id,
            'estado' => $factura->estado,
	    ];
	}



}
