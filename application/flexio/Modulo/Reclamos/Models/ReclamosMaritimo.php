<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Proveedores\Models\Proveedores;

class ReclamosMaritimo extends Model
{
	
	protected $table = 'pol_poliza_maritimo'; 
	protected $fillable =["id","uuid_casco_maritimo","empresa_id","id_poliza","numero","serie","nombre_embarcacion","tipo","marca","valor","pasajeros","acreedor","porcentaje_acreedor","observaciones","updated_at","created_at","tipo_id","estado"]; 
	public $timestamps = false;
	

}