<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Estudios_orm extends Model
{
	protected $table = 'col_estudios';
	protected $fillable = ['colaborador_id', 'grado_academico_id', 'titulo', 'institucion', 'fecha_creacion'];
	protected $guarded = ['id'];
}