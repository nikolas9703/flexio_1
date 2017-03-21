<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['subpanels'] = array(
    'subcontratos.adendas'=> array(
        'modulo' => 'subcontratos',
        'view'   => 'ocultoTablaAdendas',
        'nombre' => 'Adendas',
        'icono'  => ''
    ),
    'subcontratos.facturas_compras'=> array(
        'modulo' => 'facturas_compras',
        'view'   => 'ocultoTablaSubcontratos',
        'nombre' => 'Facturas de compra',
        'icono'  => ''
    ),
    'anticipos' => [
        'modulo' => 'anticipos',
        'view'   => 'ocultotabla',
        'nombre' => 'Anticipo',
        'icono'  => ''
        ],
    'pagos' => [
        'modulo'    => 'pagos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Pagos',
        'icono'     => ''
    ],
    'documentos' => [
        'modulo'    => 'documentos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Documentos',
        'icono'     => ''
    ]
);
