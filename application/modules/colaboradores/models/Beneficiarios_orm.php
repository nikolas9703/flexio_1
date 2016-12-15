<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Beneficiarios_orm extends Model
{
	protected $table = 'col_colaboradores_beneficiarios';
	protected $fillable = ['colaborador_id', 'tipo', 'nombre', 'parentesco_id', 'cedula', 'provincia_id', 'letra_id', 'tomo', 'asiento', 'no_pasaporte', 'porcentaje ', 'creado_por'];
	protected $guarded = ['id'];
        
      
 public function beneficiario_agente_catalogo(){ 
		return $this->hasOne('Catalogos_orm', 'id_cat', 'parentesco_id');
	}

        
 public static function beneficiario_principal($clause=array()){ 
     
     $query = self::with(array('beneficiario_agente_catalogo' => function($query){}));
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
        
 public static function beneficiario_contingente($clause=array()){ 
     
     $query = self::with(array('beneficiario_agente_catalogo' => function($query){}));
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
              
        
}