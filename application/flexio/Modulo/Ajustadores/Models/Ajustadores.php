<?php

namespace Flexio\Modulo\Ajustadores\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class Ajustadores extends Model {

    protected $table = 'seg_ajustadores';
    protected $fillable = ['identificacion', 'uuid_ajustadores', 'nombre', 'ruc', 'telefono', 'email', 'direccion', 'empresa_id', 'estado', 'creado_por', 'tomo', 'folio', 'rollo', 'asiento', 'digverificador', 'provincia', 'letras', 'pasaporte','asiento_j','tomo_j'];
    protected $guarded = ['id'];

    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }
     public function scopeRuc($ruc) {
        return $query->where("ruc =".$ruc);
    }

    function present() {
        return new \Flexio\Modulo\Ajustadores\Presenter\AjustadoresPresenter($this);
    }

}