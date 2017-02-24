<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasCliente extends Model
{
	
	protected $table = 'pol_poliza_cliente'; 
	protected $fillable =["id","id_poliza","nombre_cliente","identificacion","n_identificacion","grupo","telefono","correo_electronico","direccion","exonerado_impuesto","updated_at","created_at"]; 
	public $timestamps = false;
}