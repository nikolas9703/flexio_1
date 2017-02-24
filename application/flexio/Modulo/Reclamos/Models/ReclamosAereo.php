<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class ReclamosAereo extends Model
{
	
	protected $table = 'rec_reclamos_aereo'; 
	protected $fillable =["id","empresa_id","id_reclamo","serie","marca","modelo","matricula","valor","pasajeros","tripulacion","observaciones","updated_at","created_at","numero","tipo_id","estado"]; 
	public $timestamps = false;
	
}