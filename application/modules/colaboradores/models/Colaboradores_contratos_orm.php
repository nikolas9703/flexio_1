<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Colaboradores_contratos_orm extends Model
{
	protected $table = 'col_colaboradores_contrato';
	protected $fillable = ['colaborador_id', 'fecha_ingreso', 'fecha_salida', 'empresa_id', 'estado', 'numero_contrato', 'centro_contable', 'fecha_creacion'];
	protected $guarded = ['id'];

public function colaboradores(){ 
return $this->hasMany('Colaboradores_orm', 'id');
} 
           
public static function colaboradores_contratos($clause=array()){ 
    $query = self::with(array('colaboradores', 'centro_contable' => function($query){}));
     if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
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
     
     
     return $query->get();
    
    
	}
        
public function centro_contable(){
    
    return $this->hasOne('Centros_orm', 'id', 'centro_contable');
    
}        
        
}