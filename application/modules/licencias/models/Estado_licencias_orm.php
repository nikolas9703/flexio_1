<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Estado_licencias_orm extends Model
{
	protected $table = 'lic_licencias_cat';
	protected $fillable = ['id_campo', 'valor', 'etiqueta'];
	protected $guarded = ['id'];
	public $timestamps = false;
}