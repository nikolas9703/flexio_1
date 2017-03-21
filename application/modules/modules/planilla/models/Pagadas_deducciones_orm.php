<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Pagadas_deducciones_orm extends Model
{
	protected $table = 'pln_pagadas_deducciones';
	protected $fillable = ['planilla_pagada_id', 'deduccion_id','nombre','descuento','saldo','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
}
