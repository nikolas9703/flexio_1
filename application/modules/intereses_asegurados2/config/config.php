<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Intereses Asegurados',
    'descripcion'	=> 'Módulo para Administracion de Intereses Asegurados.',
    'autor'			=> 'Pensanomica Team',
    'icono'			=> 'fa-book',
    'version'		=> '1.0',
    'tipo'			=> 'addon', //core, addons
    'grupo'			=> 'Producción',
    'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 2
        ),
    ),
    
    'prefijo'		=> 'int',
    'menu' => array(
                        
        'nombre' =>'Intereses Asegurados' ,
        'url' => 'intereses_asegurados/listar',
        'orden'=> 3
                           
    ),
    'permisos'	=> array(
        'acceso'                                    => 'Acceso',
        'listar-usuarios__desactivarActivarUsuario' => 'Desactivar/Activar cuenta de Usuarios',
    )
);