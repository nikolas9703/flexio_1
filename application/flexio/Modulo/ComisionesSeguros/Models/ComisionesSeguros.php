<?php 

namespace Flexio\Modulo\ComisionesSeguros\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\Models\Empresa;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro as FacturaSeguro;
use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\RemesasEntrantes\Models\RemesasEntrantes;

class ComisionesSeguros extends Model
{
    protected $table = 'seg_comisiones';
    protected $fillable = ['id','uuid_comision','no_comision','fecha','monto_recibo','id_factura','id_aseguradora', 'id_poliza','id_cliente', 'id_ramo', 'comision', 'monto_comision','sobre_comision','monto_scomision','comision_pendiente','','id_remesa','lugar_pago','estado','created_at','updated_at','id_empresa','comision_pagada','comision_descontada','scomision_descontada','impuesto','impuesto_pago','pago_sobre_prima','id_cobro'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_comision' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
	
	public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("seg_comisiones.id_empresa", $empresa_id);
    }  
	
	public function facturasComisiones(){
    	return $this->belongsTo(FacturaSeguro::class, 'id_factura');
    }
	public function polizas(){
        return $this->belongsTo(Polizas::class, 'id_poliza');
    }
	
	public function datosRamos(){
    	return $this->hasOne(Ramos::class, 'id', 'id_ramo');
    }
	public function datosAseguradora(){
    	return $this->belongsTo(Aseguradoras::class, 'id_aseguradora');
    }
	public function datosRemesa(){
    	return $this->belongsTo(RemesasEntrantes::class, 'id_remesa');
    }
	 public function cliente() {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
	public static function findByUuid($uuid){
        return self::where('uuid_comision',hex2bin($uuid))->first();
    }
	
	public function datosEmpresa() {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
