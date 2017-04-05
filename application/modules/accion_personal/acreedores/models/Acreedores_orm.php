<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Acreedores_orm extends Model
{
	protected $table = 'acr_acreedores';
	protected $fillable = ['nombre'];
	protected $guarded = ['id'];
             
        public static function lista($empresa_id=NULL){
            
            if($empresa_id==NULL){
			return false;
		}
            
           return self::where('empresa_id', $empresa_id)->get(array('id', 'nombre'))->toArray();
           
		
	}
        

}
