<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['subpanels'] = array(
    'inventarios.bodegas' => [
        'modulo' => 'inventarios',
        'view' => 'ocultotablaEnInventario',
        'nombre' => 'Bodegas',
        'icono' => ''
    ],
    'inventarios.compras' => [
        'modulo' => 'facturas_compras',
        'view' => 'ocultotabla_de_item',
        'nombre' => 'Compras',
        'icono' => ''
    ],
    'inventarios.ventas' => [
        'modulo' => 'facturas',
        'view' => 'ocultotabla_de_item',
        'nombre' => 'Ventas',
        'icono' => ''
    ],
    'inventarios.ajustes' => [
        'modulo' => 'inventarios',
        'view' => 'ocultotablaHistorialAjustes',
        'nombre' => 'Ajustes',
        'icono' => '',
        'html_id' => 'inventarios2'
    ],
    'inventarios.traslados' => [
        'modulo' => 'inventarios',
        'view' => 'ocultotablaBitacoraTraslados',
        'nombre' => 'Traslados',
        'icono' => '',
        'html_id' => 'inventarios3'
    ],
    'inventarios.entradas' => [
        'modulo' => 'entradas',
        'view' => 'ocultotablaV2',//usa columnas distintas a la tabla principal de entradas
        'nombre' => 'Entradas',
        'icono' => ''
    ],
    'inventarios.salidas' => [
        'modulo' => 'salidas',
        'view' => 'ocultotablaV2',//usa columnas distintas a la tabla principal de entradas
        'nombre' => 'Salidas',
        'icono' => ''
    ],
    'inventarios.entregas' => [
        'modulo' => 'entregas_alquiler',
        'view' => 'ocultotabla',
        'nombre' => 'Entregas',
        'icono' => '',
        'html_attr' => ['v-show' => 'detalle.item_alquiler']
    ],
    'inventarios.retornos' => [
        'modulo' => 'devoluciones_alquiler',
        'view' => 'ocultotabla',
        'nombre' => 'Retornos',
        'icono' => '',
        'html_attr' => ['v-show' => 'detalle.item_alquiler']
    ],
    'inventarios.series' => [
        'modulo' => 'inventarios',
        'view' => 'ocultotabla_series',
        'nombre' => 'Series',
        'icono' => '',
        'html_id' => 'inventarios4'
    ]
);
