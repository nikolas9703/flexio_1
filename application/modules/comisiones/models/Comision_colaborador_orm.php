<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Comision_colaborador_orm extends Model
{
	protected $table = 'com_colaboradores';
	protected $fillable = ['comision_id', 'colaborador_id','descripcion','monto_total','descuento', 'estado' ];
	protected $guarded = ['id'];
	

  
	
	public static function contador_colaboradores($comision_id = NULL){
		return Capsule::table('com_colaboradores AS col')
		->where('col.comision_id', '=', $comision_id)
		->selectRaw('id, count(*) as count')
 		->groupBy('comision_id')
 		->get();
		
	}
	public static function estado_comision($comision_id = NULL){
		return Capsule::table('com_comisiones AS com')
		->leftJoin('com_comisiones_cat as cat', 'cat.id_cat','=','com.estado_id' )
		->where('com.id', '=', $comision_id)
 		->get(array('cat.*'));
	
	}
 
	public function departamento_informacion(){
		return $this->belongsToMany('Departamentos_orm', 'col_colaboradores', 'departamento_id', 'id');
	}
	public function departamento()
	{
		return $this->belongsToMany('Departamentos_orm', 'col_colaboradores', 'id', 'departamento_id');
	}
	 
	public function colaborador(){
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	}
	 
	public function comision(){
		return $this->belongsTo('Comisiones_orm', 'comision_id');
	} 
	
	
	public static function suma_monto_comisiones($comision_id = NULL){
		return Capsule::table('com_colaboradores AS col')
		->where('col.comision_id', '=', $comision_id)
		->selectRaw('id, sum(monto_total) as monto_total')
		->groupBy('comision_id')
		->get();
	
	}
	 
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
 		$query = self::with(array('departamento','colaborador','comision' ));
 		//Si existen variables de limit,''e
 		if($clause!=NULL && !empty($clause) && is_array($clause))
 		{
 			foreach($clause AS $field => $value)
 			{
 				if($field == "colaborador_id"){
 					continue;
 				}
 				//Verificar si el campo tiene el simbolo @ y removerselo.
 				if(preg_match('/@/i', $field)){
 					$field = str_replace("@", "", $field);
 				}
 		
 				//verificar si valor es array
 				if(is_array($value)){
 					$query->where($field, $value[0], $value[1]);
 				}else{
 					$query->where($field, '=', $value);
 				}
 			}
 		}
 			if($limit!=NULL) $query->skip($start)->take($limit);
 				return $query->get();
	}
}

