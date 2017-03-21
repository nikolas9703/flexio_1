<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Permisos_orm extends Model
{
	protected $table = 'permisos';
	protected $fillable = ['nombre', 'recurso_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
}