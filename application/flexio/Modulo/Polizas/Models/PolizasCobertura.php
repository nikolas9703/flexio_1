<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasCobertura extends Model
{
	
	protected $table = 'pol_poliza_coberturas'; 
	protected $fillable =["cobertura","valor_cobertura","id_poliza", "id_poliza_interes", "detalle_unico"];
	public $timestamps = false; 
}