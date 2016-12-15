<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Presupuestos',
    'descripcion'   => 'Modulo para Administracion de Presupuestos.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-calculator',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Admin Contable',
    'agrupador'     => 'Contabilidad',
    'prefijo'       => 'pres',
    'menu' => array(
        'nombre'    => 'Presupuesto' ,
        'url'       => 'presupuesto/listar',
        'orden'  => 2
    ),
    'permisos'      => array(
        'acceso'                        => 'Acceso',
    	'listar__exportarPresupuestos'  => 'Exportar',
    	'ver__editarPresupuesto'        => 'Editar',
    )
);
