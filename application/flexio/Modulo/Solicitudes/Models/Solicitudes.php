<?php
namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cliente\Models\Cliente;

class Solicitudes extends Model
{
    protected $table        = 'seg_solicitudes';    
    protected $fillable     = ['uuid_solicitudes', 'numero', 'cliente_id', 'aseguradora_id', 'ramo', 'id_tipo_poliza', 'usuario_id', 'estado', 'updated_at', 'created_at', 'empresa_id', 'fecha_creacion'];
    protected $guarded      = ['id'];
    
    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }
    
    public function cliente() {
    	return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }
    public function aseguradora() {
        return $this->hasOne('Aseguradoras_orm', 'id', 'aseguradora_id');
    }
    public function tipo() {
        return $this->hasOne('Catalogo_tipo_poliza_orm', 'id', 'id_tipo_poliza');
    }
    public function usuario() {
        return $this->hasOne(Usuarios::class, 'id', 'usuario_id');
    }
    
}