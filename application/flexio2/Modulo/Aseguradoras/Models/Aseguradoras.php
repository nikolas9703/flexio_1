<?php
namespace Flexio\Modulo\aseguradoras\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class Aseguradoras extends Model
{
    protected $table        = 'seg_aseguradoras';    
    protected $fillable     = ['uuid_aseguradora', 'nombre', 'ruc', 'telefono', 'email', 'direccion', 'descuenta_comision', 'imagen_archivo', 'creado_por', 'created_at', 'update_at', 'uuid_cuenta_pagar','uuid_cuenta_cobrar','empresa_id','estado'];
    protected $guarded      = ['id'];
    
    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }   
	
	public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }
}