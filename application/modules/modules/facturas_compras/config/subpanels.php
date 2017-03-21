<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['subpanels'] = array(
    'facturas_compras.pedidos' => [
        'modulo'    => 'pedidos',
        'view'      => 'ocultotablaFacturasCompras',
        'nombre'    => 'Pedidos',
        'icono'     => ''
    ],
    'facturas_compras.ordenes_compras' => [
        'modulo'    => 'ordenes',
        'view'      => 'ocultotablaFacturasCompras',
        'nombre'    => 'O/C',
        'icono'     => ''
    ],
    'facturas_compras.pagos' => [
        'modulo'    => 'pagos',
        'view'      => 'ocultotablaFacturasCompras',
        'nombre'    => 'Pagos',
        'icono'     => ''
    ],
    'facturas_compras.entradas' => [
        'modulo'    => 'entradas',
        'view'      => 'ocultotablaFacturasCompras',
        'nombre'    => 'Entradas',
        'icono'     => ''
    ],
    'facturas_compras.documentos' => [
        'modulo'    => 'documentos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Documentos',
        'icono'     => ''
    ]
);
