<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['subpanels'] = [
    
    'cotizaciones_alquiler' => [
      'modulo' => 'cotizaciones_alquiler',
        'view' => 'ocultotabla',
      'nombre' => 'Cotizaciones',
       'icono' => ''
    ],
    
    'entregas' => [
        'modulo' => 'entregas_alquiler',
        'view'   => 'ocultotabla',
        'nombre' => 'Entregas',
        'icono'  => ''
    ],
    
    'devoluciones' => [
        'modulo' => 'devoluciones_alquiler',
        'view'   => 'ocultotabla',
        'nombre' => 'Retornos',
        'icono'  => ''
    ],
    
    'ordenes_alquiler' => [
        'modulo' => 'ordenes_alquiler',
        'view'   => 'ocultotabla',
        'nombre' => 'Ordenes de Ventas',
        'icono'  => ''
    ],
    
    'facturas2' => [
        'modulo' => 'facturas',
        'view'   => 'ocultotabla',
        'nombre' => 'Facturas',
        'icono'  => ''
    ],
    
    'items' => [
        'modulo' => 'inventarios',
        'view'   => 'ocultotabla',
        'nombre' => 'Items',
        'icono'  => '',
        'html_id' => 'inventario1'
    ],
    
    'series' => [
        'modulo' => 'inventarios',
        'view'   => 'ocultotabla_series',
        'nombre' => 'Series',
        'icono'  => '',
        'html_id' => 'inventario2'
    ],
    
    'documentos' => [
        'modulo' => 'documentos',
        'view'   => 'ocultotabla',
        'nombre' => 'Documentos',
        'icono'  => ''
    ]
    
]; 