<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Licencias_orm extends Model
{
	protected $table = 'lic_licencias';
	protected $fillable = ['empresa_id', 'colaborador_id', 'tipo_licencia_id', 'fecha_desde', 'fecha_hasta', 'cuenta_pasivo_id', 'licencia_pagada_id', 'estado_id', 'observaciones', 'carta_sindical', 'archivo_ruta', 'archivo_nombre', 'creado_por'];
	protected $guarded = ['id'];
	
	function acciones(){
		return $this->morphMany('Accion_personal_orm', 'accionable');
	}
	
	public function colaborador(){
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	}
	
	public function estado(){
		return $this->hasOne('Estado_licencias_orm', 'id_cat', 'estado_id');
	}
}
