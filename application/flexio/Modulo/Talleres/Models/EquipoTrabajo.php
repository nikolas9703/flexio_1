<?php
namespace Flexio\Modulo\Talleres\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Talleres\Models\EquipoCentrosContables;
use Flexio\Modulo\Talleres\Models\EquipoColaboradores;
use Flexio\Modulo\Talleres\Models\EquipoTrabajoCatalogo;
use Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\OrdenesTrabajo\Models\Servicios;
use Flexio\Modulo\Comentario\Models\Comentario;

class EquipoTrabajo extends Model
{
    protected  $table = 'tal_talleres_equipo_trabajo';
    protected  $fillable = ['codigo', 'nombre', 'empresa_id', 'created_at', 'ordenes_atender', 'estado_id'];
    protected  $guarded = ['id'];
    protected $appends   = ['icono','enlace'];

    //Constructor
    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_equipo' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }
    public function getUuidEquipoAttribute($value){
        return ucwords(bin2hex($value));
    }
//scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function ordenes_trabajo() {
        return $this->hasMany('Flexio\Modulo\OrdenesTrabajo\Models\Servicios', 'equipo_id');
    }

    public static function registrar() {
        return new static;
    }

    public function centros() {
    	return $this->hasMany(EquipoCentrosContables::class, 'equipo_id');
    }

    public function colaboradores() {
    	return $this->hasMany(EquipoColaboradores::class, 'equipo_trabajo_id');
    }

    public function estado() {
    	return $this->hasOne(EquipoTrabajoCatalogo::class, 'id', 'estado_id');
    }

    function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    //functiones para el landing_page
    public function getEnlaceAttribute()
    {
        return base_url("talleres/ver/".$this->uuid_equipo);
    }
    public function getIconoAttribute(){
        return 'fa fa-wrench';
    }
}
