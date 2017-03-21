<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
$config['subpanels'] = [
	'planes' => [
        'modulo' => 'planes',
        'view' => 'planes/detalles_planes',
        'nombre' => 'Planes',
        'icono' => ''
    ],
	'solicitudes' => [
        'modulo' => 'solicitudes',
        'view' => 'solicitudes/tablatabsolicitudes',
        'nombre' => 'Solicitudes',
        'icono' => ''
    ],
	'polizas' => [
        'modulo' => 'polizas',
        'view' => 'polizas/tablapolizas_agt',
        'nombre' => 'Polizas',
        'icono' => ''
    ],
	'contactos' => [
        'modulo' => 'contactos',
        'view' => 'aseguradoras/tabladetallescontactos',
        'nombre' => 'Contactos',
        'icono' => ''
    ],
	'remesas_entrantes' => [
        'modulo' => 'remesas_entrantes',
        'view' => 'remesas_entrantes/tablatabremesasentrantes',
        'nombre' => 'Remesas Entrantes',
        'icono' => ''
    ]
    //'reportes'
    //'actividades',
    //'casos'
];
