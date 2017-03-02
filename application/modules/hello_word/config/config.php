<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Hello Word',
    'descripcion'	=> 'Módulo para Administracion de Hello Word.',
    'autor'			=> 'Pensanomica Team',
    'icono'			=> 'fa-book',
    'version'		=> '1.0',
    'tipo'			=> 'addon', //core, addons
    'grupo'			=> 'Hello Word',
    'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 14
        ),
    ),
    
    'prefijo'		=> 'int',
    'menu' => array(
                        
        'nombre' =>'Hello Word' ,
        'url' => 'hello_word/listar',
        'orden'=> 5
                           
    ),
    'permisos'	=> array(
        'acceso'                                    => 'Acceso',
        'listar-usuarios__desactivarActivarUsuario' => 'Desactivar/Activar cuenta de Usuarios',
        'editar__cambiarEstado' => 'Cambiar Estado',
		'listar__convertirCliente' => 'Convertir a cliente'
    )
);