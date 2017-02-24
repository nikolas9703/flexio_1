<?php 

namespace Flexio\Modulo\RemesasEntrantes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class RemesasEntrantesFacturas extends Model
{
    protected $table = 'seg_remesas_entrantes_facturas';
    protected $fillable = ['id','uuid_remesa_entrante_factura','remesa_entrante_id','factura_id','mont_pag_factura','comision_pagada','chequeada','created_at','update_at'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_remesa_entrante_factura' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }


    public static function findByUuid($uuid){
        return self::where('uuid_remesa_entrante_factura',hex2bin($uuid))->first();
    }
}
