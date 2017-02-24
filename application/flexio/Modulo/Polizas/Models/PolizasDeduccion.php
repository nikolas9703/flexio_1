<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasDeduccion extends Model
{
	
	protected $table = 'pol_poliza_deduccion'; 
	protected $fillable =["deduccion","valor_deduccion","id_poliza"]; 
	public $timestamps = false;
}