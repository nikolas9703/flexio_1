<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Planilla',
	'descripcion'	=> 'Modulo para la Administracion de Planilla.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Planilla',
	//'agrupador'		=> 'Nomina',
    'agrupador'		=> array(
         'Nomina' => array(
            "grupo_orden" => 2
        )
    ),
	'prefijo'		=> 'pln',
 	'menu' => array(
						'nombre' =>'Planilla' ,
						'url' => 'planilla/listar',
						'orden'		=> 1
	),
    'permisos'		=> array(
            'acceso'    => 'Acceso',
        	'listar__exportarPlanilla' => 'Exportar Planilla',
        	'listar__anularPlanilla' => 'Anular Planilla',
        	'ver__editarPlanilla' => 'Editar Planilla',
        	'ver__detalleColaborador' => 'Detalles Colaborador',
         	'ver__eliminarColaborador' => 'Eliminar Colaborador',
        	'regitro-tiempo__adminHoras' => 'Administrador',
        	/*'configuracion__tabGenerales' => 'Generales',
        	'configuracion__tabDeducciones' => 'Deducciones',
        	'configuracion__tabBeneficios' => 'Beneficios',
        	'configuracion__tabAcumulados' => 'Acumulados',
        	'configuracion__tabDiasFeriados' => 'Dias Feriados',
        	'configuracion__tabRecargos' => 'Recargos'*/
 
	)
);
 
