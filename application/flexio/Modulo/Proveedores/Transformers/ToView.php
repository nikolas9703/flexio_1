<?php

namespace Flexio\Modulo\Proveedores\Transformers;

class ToView
{
    protected $identificacion_catalogo = [
        'juridico' => 'ruc',
        'ruc_nt' => 'ruc_nt',
        'natural' => 'cedula',
        'cedula_nt' => 'cedula_nt',
        'pasaporte' => 'pasaporte'
    ];

    public function proveedor($proveedor)
    {
        return Collect(array_merge(
            $proveedor->toArray(),
            [
                'uuid_tipo' => '',
                'tipo_identificacion' => (isset($this->identificacion_catalogo[$proveedor->identificacion])) ? $this->identificacion_catalogo[$proveedor->identificacion] : '',
                'detalle_identificacion' => [
                    'tomo' => $proveedor->tomo_rollo,
                    'folio' => $proveedor->folio_imagen_doc,
                    'asiento' => $proveedor->asiento_ficha,
                    'dv' => $proveedor->digito_verificador,
                    'provincia' => $proveedor->provincia,
                    'letra' => $proveedor->letra,
                    'pasaporte' => $proveedor->pasaporte
                ]
            ]
        ));
    }
}
