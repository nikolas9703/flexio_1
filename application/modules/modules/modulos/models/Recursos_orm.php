<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Recursos_orm extends Model
{
	protected $table = 'recursos';
	protected $fillable = ['nombre', 'modulo_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	public function permisos(){
		return $this->belongsToMany('Permisos_orm','permisos','id','recurso_id');
	}
}