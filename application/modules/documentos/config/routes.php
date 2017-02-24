<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['documentos/listar'] = 'documentos/index';
$route['documentos/ajax-listar'] = 'documentos/ajax_listar';
$route['documentos/ajax-listar-seguros'] = 'documentos/ajax_listar_seguros';
$route['documentos/ajax-listar-main'] = 'documentos/ajax_listar_main';
$route['documentos/subir-documento/(:any)'] = 'documentos/subir_documento/$1';
$route['documentos/ajax-cambiar-estado'] = 'documentos/ajax_cambiar_estado';
$route['documentos/ajax-cambiar-en-expediente'] = 'documentos/ajax_cambiar_en_expediente';
$route['documentos/actualizar-documento'] = 'documentos/actualizar_documento';
$route['documentos/borrar'] = 'documentos/borrar';
$route['documentos/document-deleting'] = 'documentos/borrar';
$route['documentos/ajax-descargar-documento'] = 'documentos/ajax_descargar_documento';
$route['documentos/ajax-descargar-documento-detalle'] = 'documentos/ajax_descargar_documento_detalle';
