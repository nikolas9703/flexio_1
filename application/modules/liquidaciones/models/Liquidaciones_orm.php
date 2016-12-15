<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Liquidaciones_orm extends Model
{
	protected $table = 'liq_liquidaciones';
	protected $fillable = ['empresa_id', 'colaborador_id', 'tipo_liquidacion_id', 'fecha_apartir', 'firmado_por', 'estado_id', 'cuenta_pasivo_id', 'motivo', 'estado_id', 'archivo_ruta', 'solicitud', 'archivo_ruta', 'archivo_nombre', 'creado_por'];
	protected $guarded = ['id'];
	
	function acciones(){
		return $this->morphMany('Accion_personal_orm', 'accionable');
	}
	
	public function colaborador(){
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	} 
	
	public function estado(){
		return $this->hasOne('Estado_liquidaciones_orm', 'id_cat', 'estado_id');
	}
        
        public function contrato(){
                return $this->hasOne('Colaboradores_contratos_orm', 'colaborador_id', 'colaborador_id');            
        }
}
