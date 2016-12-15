<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Catalogo_requisitos_orm extends Model
{
	protected $table = 'col_requisitos';
	protected $fillable = ['empresa_id', 'nombre', 'estado_id', 'creado_por'];
	protected $guarded = ['id'];
} 