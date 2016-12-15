<?php

defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Pago',
    'descripcion' => 'Modulo para Administracion de Pagos desde contratos.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-shopping-cart',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    //'grupo' => 'Contratos',
    'agrupador' => 'Contratos',
    'prefijo' => 'pag',
    'menu' => array(
        'nombre' => 'Pagos',
        'url' => 'pagos_contratos/listar'
    //'orden'     => 3
    ),
    'permisos' => array(
        'acceso' => 'Acceso',
        'listar__ver' => 'Listar'
    )
);
