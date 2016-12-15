<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Pagadas_acumulados_orm extends Model
{
	protected $table = 'pln_pagadas_acumulados';
	protected $fillable = ['planilla_pagada_id', 'nombre','acumulado','saldo','fecha_creacion','acumulado_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
}