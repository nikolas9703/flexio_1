<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Configuración Contrato',
    'descripcion'   => 'Modulo para Administración de Configuración de Contratos.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-gear',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Configuracion',
    'agrupador'		=> array(
        'Contratos' => array(
            "grupo_orden" => 20
        ),
    ),
    'prefijo'       => 'cnfcntr',
    'menu' => array(
        'nombre'    => 'Configuracion' ,
        'url'       => 'configuracion_contratos/configuracion'
    ),
    'permisos'      => array(
        'acceso' => 'Acceso',
    )
);
