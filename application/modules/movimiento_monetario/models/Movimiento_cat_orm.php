<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Movimiento_cat_orm extends Model
{
    
    protected $table = 'mov_movimiento_monetario_cat';
    protected $fillable = ['nombre', 'estado_id'];
    protected $guarded = ['id_cat'];
  
public static function lista(){
		
		return self::where('estado_id', '1')->get()->toArray();
	}  
  

}