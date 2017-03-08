<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Catalogos',
    'descripcion'   => 'Modulo para administrar los catalogos.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-book',
    'version'       => '1.0',
    'tipo'          => 'addon', //core, addons
    //'grupo'         => 'Planes ',
    'grupo'         => 'Configuración',
    'agrupador'     => array(
        'Seguros' => array(
            "grupo_orden" => 10
        ),
    ),
    'prefijo'       => 'pla',
    /*'menu' => array([                        
                             'nombre' =>'Planes' ,
                             'url' => 'planes/listar',
                             'orden'=> 1],[
                             'nombre' =>'Configuracion' ,
                             'url' => 'planes/crear',
                             'orden'=> 2
                             ]
                           
    ),*/
    'menu' => array(          
                             'nombre' =>'Catálogos' ,
                             'url' => 'catalogos/ver',
                             'orden'=> 1
                             
                           
    ),
    'permisos'  => array(
        'acceso'                                    => 'Acceso',
        'listar-usuarios__desactivarActivarUsuario' => 'Desactivar/Activar cuenta de Usuarios',
    )
);