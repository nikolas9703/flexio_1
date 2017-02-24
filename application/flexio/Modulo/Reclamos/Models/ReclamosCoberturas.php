<?php 

namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class ReclamosCoberturas extends Model
{
	
	protected $table = 'rec_reclamos_coberturas'; 
	protected $fillable =["cobertura","valor_cobertura","id_reclamo", "id_poliza_cobertura"];
	protected $guarded = ['id'];
	public $timestamps = false; 
}