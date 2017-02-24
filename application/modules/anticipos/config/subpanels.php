<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
//Utilizala la nueva clase de subpanels
$config['subpanels'] = array(
    'pago' => [
        'modulo'    => 'pagos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Pagos',
        'icono'     => ''
    ],
    'documento'=>[
        'modulo'    => 'documentos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Documentos',
        'icono'     => ''
    ]
);
