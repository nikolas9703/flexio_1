<?php 

namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class ReclamosAccidentes extends Model
{
	
	protected $table = 'rec_reclamos_accidentes'; 
	protected $fillable =["id_reclamo", "id_tipo_accidente"];
	protected $guarded = ['id'];
	public $timestamps = false; 
}