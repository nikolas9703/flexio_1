<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Comision_deducciones_orm extends Model
{
	protected $table = 'com_deducciones';
	protected $fillable = ['deduccion_id', 'comision_id','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
}