<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
$config['subpanel'] = array(
//	'ordenes_ventas',
//        'documentos'
);

$config['subpanels'] = array(
    'entrega_alquiler.retornos' => [
        'modulo'    => 'devoluciones_alquiler',
        'view'      => 'ocultotabla',
        'nombre'    => 'Retornos',
        'icono'     => ''
    ]
);
