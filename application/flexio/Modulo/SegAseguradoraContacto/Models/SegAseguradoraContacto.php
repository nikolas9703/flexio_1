<?php
namespace Flexio\Modulo\SegAseguradoraContacto\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;

class SegAseguradoraContacto extends Model
{
    protected $table        = 'seg_aseguradoras_contacto';    
    protected $fillable     = ['uuid_contacto','aseguradora_id', 'nombre', 'email','celular', 'telefono', 'cargo', 'direccion', 'comentarios', 'creado_por', 'created_at', 'updated_at','estado'];
    protected $guarded      = ['id'];
	
	public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }
	
	public function nombreAseguradora() {
        return $this->hasOne(Aseguradoras::class, 'id', 'aseguradora_id');
    }
	
	public function scopeDeAseguradora($query, $aseguradora_id) {
        return $query->where("aseguradora_id", $aseguradora_id);
    } 
}