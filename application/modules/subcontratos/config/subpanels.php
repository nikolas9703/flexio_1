<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['subpanels'] = array(
    'subcontratos.adendas'=> array(
        'modulo' => 'subcontratos',
        'view'   => 'ocultoTablaAdendas',
        'nombre' => 'Adendas',
        'icono'  => ''
    ),
    'subcontratos.facturas_compras'=> array(
        'modulo' => 'facturas_compras',
        'view'   => 'ocultoTablaSubcontratos',
        'nombre' => 'Facturas de compra',
        'icono'  => ''
    )
);
