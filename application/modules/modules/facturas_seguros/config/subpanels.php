<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
/*$config['subpanel'] = array(
	'ordenes_ventas', 'cotizaciones','salidas','cobros', 'documentos'
); */

$config['subpanels'] = [
	'documentos' => [
        'modulo' => 'documentos',
        'view' => 'documentos/ocultotablaseguros',
        'nombre' => 'Documentos',
        'icono' => ''
    ],
];
