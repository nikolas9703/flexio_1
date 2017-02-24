<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class ReclamosArticulo extends Model
{
	
	protected $table = 'rec_reclamos_articulo'; 
	protected $fillable =[
		"uuid_articulo",
		"id_reclamo",
		"numero",
		"empresa_id",
		"nombre",
		"clase_equipo",
		"marca",
		"modelo",
		"anio",
		"numero_serie",
		"id_condicion",
		"valor",
		"observaciones",
		"estado",		
		"updated_at",
		"created_at"
		]; 
	public $timestamps = false;
	protected $guarded      = ['id'];

}