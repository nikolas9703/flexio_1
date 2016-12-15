<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['planilla/listar'] = 'planilla/listar';
$route['planilla/crear'] = 'planilla/crear';
$route['planilla/ver/(:any)'] = 'planilla/crear/$1';

$route['planilla/ver-reporte2/(:any)'] = 'planilla/ver_reporte2/$1';
$route['planilla/ver-reporte/(:any)'] = 'planilla/ver_reporte/$1';
$route['planilla/ver-reporte-decimo/(:any)'] = 'planilla/ver_reporte_decimo/$1';
$route['planilla/ver-reporte-cerradas/(:any)'] = 'planilla/ver_reporte_cerradas/$1';
$route['planilla/ver-reporte-decimo-cerrada/(:any)'] = 'planilla/ver_reporte_decimo_cerrada/$1';

$route['planilla/ajax-crear-pagos-planilla'] = 'planilla/ajax_crear_pagos_planilla';
$route['planilla/ajax-listar-planilla'] = 'planilla/ajax_listar_planilla';
$route['planilla/ajax-listar-decimo'] = 'planilla/ajax_listar_decimo';
$route['planilla/ajax-listar-ingresos'] = 'planilla/ajax_listar_ingresos';
$route['planilla/ajax-listar-ingresos-decimo'] = 'planilla/ajax_listar_ingresos_decimo';
$route['planilla/ajax-listar-calculos'] = 'planilla/ajax_listar_calculos';
$route['planilla/ajax-listar-deducciones'] = 'planilla/ajax_listar_deducciones';
$route['planilla/ajax-listar-descuentos-directos'] = 'planilla/ajax_listar_descuentos_directos';
$route['planilla/ajax-crear-planilla'] = 'planilla/ajax_crear_planilla';
$route['planilla/ajax-crear-planillaNoRegulares'] = 'planilla/ajax_crear_planillaNoRegulares';
$route['planilla/ajax-editar-planilla'] = 'planilla/ajax_editar_planilla';
$route['planilla/ajax-editar-planillaNoRegular'] = 'planilla/ajax_editar_planillaNoRegular';
$route['planilla/ajax-editar-planillaNoRegular-liquidacion'] = 'planilla/ajax_editar_planillaNoRegular_liquidacion';
$route['planilla/ajax-seleccionar-ingreso-horas'] = 'planilla/ajax_seleccionar_ingreso_horas';
$route['planilla/ajax-seleccionar-informacion-columnas'] = 'planilla/ajax_seleccionar_informacion_columnas';
$route['planilla/ajax-guardar-entrar-horas'] = 'planilla/ajax_guardar_entrar_horas';
$route['planilla/ajax-eliminar-ingreso-horas'] = 'planilla/ajax_eliminar_ingreso_horas';
$route['planilla/ajax-validar-colaborador-planilla'] = 'planilla/ajax_validar_colaborador_planilla';
$route['planilla/ajax-agregar-colaborador-planilla'] = 'planilla/ajax_agregar_colaborador_planilla';
$route['planilla/ajax-seleccionar-centro-colaborador'] = 'planilla/ajax_seleccionar_centro_colaborador';
$route['planilla/ajax-seleccionar-comentario'] = 'planilla/ajax_seleccionar_comentario';
$route['planilla/ajax-guardar-comentario'] = 'planilla/ajax_guardar_comentario';
$route['planilla/ajax-exportar-talonarios-multiples'] = 'planilla/ajax_exportar_talonarios_multiples';
$route['planilla/ajax-cerrar-planilla-especial'] = 'planilla/ajax_cerrar_planilla_especial';
$route['planilla/ajax-detalles-pago'] = 'planilla/ajax_detalles_pago2';
$route['planilla/ajax-detalles-pago-decimo'] = 'planilla/ajax_detalles_pago_decimo';
$route['planilla/ajax-anular-planilla'] = 'planilla/ajax_anular_planilla';
$route['planilla/ajax-listar-planilla-colaboradores'] = 'planilla/ajax_listar_planilla_colaboradores';
$route['planilla/ajax-eliminar-colaborador-planilla'] = 'planilla/ajax_eliminar_colaborador_planilla';
$route['planilla/ajax-pagar-planilla'] = 'planilla/ajax_pagar_planilla';
$route['planilla/ajax-imprimir-talonario'] = 'planilla/ajax_imprimir_talonario';
$route['planilla/ajax-imprimir-talonario-decimo'] = 'planilla/ajax_imprimir_talonario_decimo';
$route['planilla/ajax-listar-colaboradores-ciclo-pago'] = 'planilla/ajax_listar_colaboradores_ciclo_pago';
$route['planilla/ajax-guardar-comentario-planilla'] = 'planilla/ajax_guardar_comentario_planilla';
$route['planilla/ajax-informacion-total-horas'] = 'planilla/ajax_informacion_total_horas';

$route['planilla/ocultotablaingresos'] 				= 'planilla/ocultotablaingresos';
$route['planilla/ocultotablaplanilla'] 		= 'planilla/ocultotablaplanilla';
$route['planilla/ocultotabladecimo'] 		= 'planilla/ocultotabladecimo';
$route['planilla/regitro-tiempo/(:any)'] = 'planilla/entrar_horas/$1';


//NUEVIS
 $route['planilla/ajax-modal-liquidacion'] = 'planilla/ajax_modal_liquidacion';
 $route['planilla/ajax-cerrar-planilla-liquidacion'] = 'planilla/ajax_cerrar_planilla_liquidacion';
 $route['planilla/reporte-liquidacion/(:any)'] = 'planilla/reporte_liquidacion/$1';
