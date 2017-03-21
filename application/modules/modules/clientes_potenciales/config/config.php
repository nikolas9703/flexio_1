<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Clientes Potenciales',
	'descripcion'	=> 'Modulo para Administracion de clientes potenciales.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-briefcase',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Ventas',
	'agrupador'		=> [
                'Contratos',
		'Ventas',
		'Alquileres',
		'Servicios',
		'Seguros'
     ],
	'menu' => array(
		'nombre' => 'Clientes Potenciales' ,
		'url' => 'clientes_potenciales/listar',
		'orden' => 2
	),
	'prefijo'		=> 'cp',
    'permisos'		=> array(
		'acceso' => 'Acceso',
    	'listar__exportarCP' => 'Exportar Cliente Potencial',
    	'listar__eliminarCP' => 'Eliminar Cliente Potencial',
    	'listar__convertirJuridicoCP' => 'Convertir a Cliente Juridico',
    	'listar__convertirNaturalCP' => 'Convertir a Cliente Natural',
    	'listar-clientes-potenciales__campanaMercadeoCP' => 'Agregar a Campa&ntilde;a de Mercadeo',
    	'ver__editarClientePotencial' => 'Editar Cliente Potencial',
    	'crear__crearClientePotencial' => 'Crear Cliente Potencial'
	)
);
