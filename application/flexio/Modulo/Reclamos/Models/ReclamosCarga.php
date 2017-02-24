<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class ReclamosCarga extends Model
{
	
	protected $table = 'rec_reclamos_carga'; 
	protected $fillable =["id","empresa_id","id_reclamo","numero","detalle","no_liquidacion","fecha_despacho","fecha_arribo","valor","tipo_empaque","condicion_envio","medio_transporte","origen","destino","observaciones","updated_at","created_at","tipo_id","tipo_obligacion","acreedor","estado","acreedor_opcional","tipo_obligacion_opcional"]; 
	public $timestamps = false;
	
}