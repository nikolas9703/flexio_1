<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
$config['subpanels'] = array(
    'movimiento_monetario.documentos' => [
        'modulo' => 'documentos',
        'view' => 'ocultotabla',
        'nombre' => 'Documentos',
        'icono' => ''
    ]
);
