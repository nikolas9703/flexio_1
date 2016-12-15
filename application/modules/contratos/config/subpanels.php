<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
/*$config['subpanel'] = array(
	'contratos.adendas'
);*/

$config['subpanels'] = array(
	'contratos.adendas'=> array('modulo'=>'contratos','view'=>'ocultoTablaAdendas','nombre'=>'Adendas','icono'=>''),
	'contratos.facturas'=> array('modulo'=>'facturas','view'=>'ocultotabla','nombre'=>'Facturas','icono'=>'')
);
