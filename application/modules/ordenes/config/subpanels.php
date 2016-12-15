<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Caso.
/*$config['subpanel'] = array(
    //'ordenes'
);*/
$config['subpanels'] = array(
    'ordenes_compras.pedidos' => [
        'modulo'    => 'pedidos',
        'view'      => 'ocultotablaOrdenesCompras',
        'nombre'    => 'Pedidos',
        'icono'     => ''
    ],
    'ordenes_compras.facturas_compras' => [
        'modulo'    => 'facturas_compras',
        'view'      => 'ocultotablaOrdenesCompras',
        'nombre'    => 'Facturas',
        'icono'     => ''
    ],
    'ordenes_compras.pagos' => [
        'modulo'    => 'pagos',
        'view'      => 'ocultotablaOrdenesCompras',
        'nombre'    => 'Pagos',
        'icono'     => ''
    ],
    'ordenes_compras.documentos' => [
        'modulo'    => 'documentos',
        'view'      => 'ocultotabla',
       'nombre'    => 'Documentos',
       'icono'     => ''
    ],
    'ordenes_compras.anticipos' => [
        'modulo'    => 'anticipos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Anticipos',
        'icono'     => ''
    ]
);