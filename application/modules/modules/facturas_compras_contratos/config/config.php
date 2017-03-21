<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Factura',
	'descripcion'	=> 'Modulo para Administracion de facturas de compras en contratos.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-shopping-cart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Compras',
	//'agrupador'		=> 'Contratos',
	//'agrupador_orden'     => 5,
      'agrupador'		=> array(
         'Contratos' => array(
             "grupo_orden" =>8
	),
     ),
    
	'prefijo'		=> 'faccom',
	'menu' => array(
            'nombre'    => 'Facturas' ,
            'url'       => 'facturas_compras_contratos/listar',
            'orden'     => 8
	),
        'permisos'		=> array(
            'acceso'        => 'Acceso',
            'listar__ver'   => 'Listar'
	)
);
