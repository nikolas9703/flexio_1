<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Pólizas',
    'descripcion'	=> 'Modulo para administrar las pólizas de seguro.',
    'autor'			=> 'Pensanomica Team',
    'icono'			=> 'fa-book',
    'version'		=> '1.0',
    'tipo'			=> 'addon', //core, addons
    'grupo'			=> 'Producción',
    'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 1
        ),
    ),
    'prefijo'		=> 'pol',
    'menu' => array(
                        
                             'nombre' =>'Pólizas' ,
                             'url' => 'polizas/listar',
                             'orden'=> 2
                           
    ),
    'permisos'	=> array(
        'acceso'                                    => 'Acceso',
        'listar-usuarios__desactivarActivarUsuario' => 'Desactivar/Activar cuenta de Usuarios',
    )
);