<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class ReclamosPersonas extends Model
{
	
	protected $table = 'rec_reclamos_personas'; 
	protected $fillable =["id", "id_interes", "id_reclamo","numero","nombrePersona" ,"identificacion","fecha_nacimiento","estado_civil","nacionalidad","sexo","estatura","peso","telefono_residencial","telefono_oficina","direccion_residencial", "direccion_laboral","observaciones","updated_at", "created_at","empresa_id","telefono_principal","direccion_principal","estado","correo"]; 
	public $timestamps = false;

}