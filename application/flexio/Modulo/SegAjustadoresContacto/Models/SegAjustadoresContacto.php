<?php

namespace Flexio\Modulo\SegAjustadoresContacto\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Ajustadores\Models\Ajustadores;

class SegAjustadoresContacto extends Model {

    protected $table = 'seg_ajustadores_contacto';
    protected $fillable = ['uuid_contacto', 'nombre', 'apellido', 'cargo', 'telefono', 'ajustador_id', 'celular', 'email', 'update_at', 'created_at', 'principal', 'creado_por', 'estado','contacto_principal'];
    protected $guarded = ['id'];

    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }
    public function scopeDeAjustadores($query, $ajustadores_id) {
        return $query->where("ajustador_id", $ajustadores_id);
    }
    public function nombreAjustadores() {
        return $this->hasOne(Ajustadores::class, 'id', 'ajustador_id');
    }
     

}
