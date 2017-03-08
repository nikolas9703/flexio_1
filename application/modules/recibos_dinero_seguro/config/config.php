<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Recibos de dinero',
    'descripcion'   => 'Modulo para la administración de recibos de dinero',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-line-chart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Recibos de dinero',
    'agrupador'     => array(
        'Seguros' => array(
            "grupo_orden" => 5
        ),
    ),
    'prefijo'       => 'mov',
    'menu' => array(
        'nombre' =>'Recibos de dinero' ,
        'url' => 'movimiento_monetario/listar_recibos',
        'orden'=> 5
    ),
        'permisos'      => array(
        'acceso' => 'Acceso'
             //  'crear'  => 'Crear'
    )
);