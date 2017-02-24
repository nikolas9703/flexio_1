<?php

namespace Flexio\Modulo\Proveedores\Transformers;

class ToDatabase
{
    protected $identificacion_catalogo = [
        'ruc' => 'juridico',
        'ruc_nt' => 'ruc_nt',
        'cedula' => 'natural',
        'cedula_nt' => 'cedula_nt',
        'pasaporte' => 'pasaporte'
    ];

    public function campo($campo)
    {
        $aux = isset($campo['detalle_identificacion']) && !empty($campo['detalle_identificacion']) ? $campo['detalle_identificacion'] : [];
        return array_merge($campo,[
            'id_banco' => $campo['banco'],
            'id_tipo_cuenta' => $campo['tipo_cuenta'],
            //identification
            'identificacion' => $this->identificacion_catalogo[$campo['tipo_identificacion']],
            'tomo_rollo' => !empty($aux) && isset($aux['tomo']) ? $aux['tomo'] : '',
            'folio_imagen_doc' => !empty($aux) && isset($aux['folio']) ? $aux['folio'] : '',
            'asiento_ficha' => !empty($aux) && isset($aux['asiento']) ? $aux['asiento'] : '',
            'digito_verificador' => !empty($aux) && isset($aux['dv']) ? $aux['dv'] : '',
            'ruc' => $campo['identificacion'],
            'provincia' => !empty($aux) && isset($aux['provincia']) ? $aux['provincia'] : '',
            'letra' => !empty($aux) && isset($aux['letra']) ? $aux['letra'] : '',
            'pasaporte' => !empty($aux) && isset($aux['pasaporte']) ? $aux['pasaporte'] : ''
        ]);
    }
}
