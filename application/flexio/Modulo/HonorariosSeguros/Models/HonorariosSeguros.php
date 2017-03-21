<?php 

namespace Flexio\Modulo\HonorariosSeguros\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Pagos\Models\Pagos;
use Carbon\Carbon as Carbon;

class HonorariosSeguros extends Model
{
    protected $table = 'seg_honorarios';
    protected $fillable = ['id','uuid_honorario','no_honorario','id_pago','monto_total','comisiones_pagadas','estado', 'agente_id','fecha_desde', 'fecha_hasta', 'usuario_id','created_at','updated_at','empresa_id'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_honorario' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
	
	public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("seg_honorarios.empresa_id", $empresa_id);
    }  
	
	public function datosEmpresa() {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
	
	public function datosPago() {
        return $this->belongsTo(Pagos::class, 'id_pago');
    }
}
