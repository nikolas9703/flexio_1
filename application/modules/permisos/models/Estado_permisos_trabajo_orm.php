<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Estado_permisos_trabajo_orm extends Model
{
	protected $table = 'perm_permisos_cat';
	protected $fillable = ['id_campo', 'valor', 'etiqueta'];
	protected $guarded = ['id'];
	public $timestamps = false;
}