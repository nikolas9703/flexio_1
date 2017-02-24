<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Reclamos',
    'descripcion'	=> 'Modulo para administrar los reclamos de seguro.',
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
    'prefijo'		=> 'rec',
    'menu' => array(
                        
                             'nombre' =>'Reclamos' ,
                             'url' => 'reclamos/listar',
                             'orden'=> 4
                           
    ),
    'permisos'	=> array(
        'acceso'                                    => 'Acceso',
        'editar__asignarA' => 'Asignar a otro Usuario',
        'editar__cambiarEstado' => 'Cambiar Estado'
    )
);