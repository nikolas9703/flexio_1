<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Incapacidades_orm extends Model
{
	protected $table = 'inp_incapacidades';
	protected $fillable = ['empresa_id', 'colaborador_id', 'tipo_incapacidad_id', 'dias_disponibles_id', 'fecha_desde', 'fecha_hasta', 'estado_id',
	 'cuenta_pasivo_id', 'observaciones', 'incapacidad_pagada_id', 'certificado_medico', 'carta_descuento', 'archivo_1_ruta', 'archivo_1_nombre',
	  'archivo_2_ruta', 'archivo_2_nombre', 'creado_por', 'constancia_institucion_medica', 'orden_medica_hospitalizacion', 'ord_med_hospt_nombre',
		 'ord_med_hospt_ruta', 'orden_css_pension', 'ord_css_pens_nombre', 'ord_css_pens_ruta', 'desgloce_salario', 'desg_sal_nombre', 'desg_sal_ruta',
	 'reporte_accion_trabajo', 'report_acc_trab_nombre', 'report_acc_trab_ruta', 'certificado_incapacidad_accidente_trabajo', 'cert_incp_accid_trab_nombre',
 'cert_incp_accid_trab_ruta'];
	protected $guarded = ['id'];

	function acciones(){
		return $this->morphMany('Accion_personal_orm', 'accionable');
	}
	
	public function colaborador(){
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	}
	
	public function estado(){
		return $this->hasOne('Estado_incapacidades_orm', 'id_cat', 'estado_id');
	}
}
