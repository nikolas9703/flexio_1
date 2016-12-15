<?php
namespace Flexio\Modulo\Ajustadores\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Flexio\Modulo\Empresa\Models\Empresa as EmpresaModel;

//utilities
use Carbon\Carbon as Carbon;

class Ajustadores extends Model
{
    protected $prefijo      = 'seg';
    protected $table        = 'seg_ajustadores';
    protected $fillable     = ['uuid_ajustadores', 'nombre', 'ruc', 'telefono', 'email', 'direccion', 'empresa_id', 'estado_id', 'identificacion'];
    protected $guarded      = ['id'];
    public $timestamps      = true;
    
    public function empresa() {
		return $this->hasOne(EmpresaModel::class, 'id', 'empresa_id');
    }
    public function getUuidAjustadoresAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    public function getUuidAttribute() {
        return $this->uuid_ajustadores;
    }
}
