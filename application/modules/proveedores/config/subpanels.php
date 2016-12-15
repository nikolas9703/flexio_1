<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Caso.
//$config['subpanel'] = array(
//    'ordenes'
//);
$config['subpanels'] = array(
    'proveedores.ordenes' => [
        'modulo'    => 'ordenes',
        'view'      => 'ocultotablaProveedores',
        'nombre'    => '&Oacute;rdenes de Compras',
        'icono'     => ''
    ],
    'proveedores.facturas_compras' => [
        'modulo'    => 'facturas_compras',
        'view'      => 'ocultotablaProveedores',
        'nombre'    => 'Facturas',
        'icono'     => ''
    ],
    'proveedores.pagos' => [
        'modulo'    => 'pagos',
        'view'      => 'ocultotablaProveedores',
        'nombre'    => 'Pagos',
        'icono'     => ''
    ],
    'proveedores.notas_debito' => [
        'modulo'    => 'notas_debitos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Notas de d&eacute;bito',
        'icono'     => ''
    ],
    'proveedores.anticipos' => [
        'modulo'    => 'anticipos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Anticipos',
        'icono'     => ''
    ]
    //comentado porque no lo vi en prototipo a fecha 19 de octubre de 2016
    /*'proveedores.documentos' => [
        'modulo'    => 'documentos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Documentos',
        'icono'     => ''
    ]*/
);
