<?php 

namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class ReclamosDeduccion extends Model
{
	
	protected $table = 'rec_reclamos_deduccion'; 
	protected $fillable =["deduccion","valor_deduccion","id_reclamo", "id_poliza_deduccion"]; 
	protected $guarded = ['id'];
	public $timestamps = false;
}