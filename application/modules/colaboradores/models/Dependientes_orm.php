<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Dependientes_orm extends Model
{
	protected $table = 'col_dependientes';
	protected $fillable = ['colaborador_id', 'nombre', 'segundo_nombre', 'parentesco_id', 'cedula', 'provincia_id', 'letra_id', 'tomo', 'asiento', 'no_pasaporte', 'fecha_nacimiento', 'fecha_creacion'];
	protected $guarded = ['id'];
}