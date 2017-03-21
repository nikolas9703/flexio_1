<?php
namespace Flexio\Modulo\Pagos\Models;

use Flexio\Modulo\Pagos\Observer\PagosObserver;
use \Illuminate\Database\Eloquent\Model as Model;
use Carbon\Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Politicas\PoliticableTrait;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Util\GenerarCodigo;
use Flexio\Modulo\Historial\Models\Historial;
use Flexio\Modulo\Proveedores\Models\Proveedores;

class Pagos extends Model
{
    use RevisionableTrait;
    use PoliticableTrait;
    //propiedades politicas
    protected $politica = 'pago';
    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo', 'proveedor_id', 'empresa_id', 'fecha_pago', 'estado', 'monto_pagado', 'cuenta_id', 'referencia', 'formulario', 'depositable_id', 'depositable_type', 'empezable_id', 'empezable_type'];

    protected $table        = 'pag_pagos';
    protected $fillable     = ['codigo', 'proveedor_id', 'empresa_id', 'fecha_pago', 'estado', 'monto_pagado', 'cuenta_id', 'referencia', 'formulario', 'depositable_id', 'depositable_type', 'empezable_id', 'empezable_type'];
    protected $appends      = ['icono','enlace'];
    protected $guarded      = ['id','uuid_pago'];
    public $timestamps      = true;

    //otros
    protected $depositables = [
      'banco' => 'Flexio\\Modulo\\Contabilidad\\Models\\Cuentas',
      'cuenta_contable' => 'Flexio\\Modulo\\Contabilidad\\Models\\Cuentas2',
      'caja' => 'Flexio\\Modulo\\Cajas\\Models\\Cajas'
    ];
    protected $empezables = [
        'factura' => 'Flexio\\Modulo\\FacturasCompras\\Models\\FacturaCompra',
        'proveedor' => 'Flexio\\Modulo\\Proveedores\\Models\\Proveedores',
        'subcontrato' => 'Flexio\\Modulo\SubContratos\\Models\\SubContrato',
        'anticipo' => 'Flexio\\Modulo\\Anticipos\\Models\\Anticipo',
        'movimiento_monetario' => 'Flexio\\Modulo\\MovimientosMonetarios\\Models\\MovimientosRetiros',
		'participacion' => 'Flexio\\Modulo\\HonorariosSeguros\\Models\\HonorariosSeguros',
        'remesas_salientes' => 'Flexio\\Modulo\\Remesas\\Models\\Remesa'
    ];

    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_pago' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }




    /**
     * Register any other events for your application.
     *
     * @return void
     */

    public static function boot() {
        parent::boot();
        Pagos::observe(PagosObserver::class);
    }
    //GETS
    public function getUuidPagoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    function setCodigoAttribute($value){
        $year = Carbon::now()->format('y');
        return $this->attributes['codigo'] = GenerarCodigo::setCodigo('PGO'.$year, $value);
    }

    public function setFechaPagoAttribute($date){

        if (Carbon::createFromFormat('d/m/Y', $date, 'America/Panama') !== false) {
            return $this->attributes['fecha_pago'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
        }
        return $this->attributes['fecha_pago'] =  Carbon::now();
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }

    public function getFechaPagoAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
    }

    public function getFechaPagoDataBaseAttribute(){

        return Carbon::createFromFormat('d/m/Y', $this->fecha_pago);
    } 

    public function getDepositableTypeAttribute($value)
    {
        return array_search($value, $this->depositables);
    }

    public function getEmpezableTypeAttribute($value)
    {
        return array_search($value, $this->empezables);
    }

    public function getEmpezableTypeModelAttribute($value)
    {
        return $this->empezables[$this->empezable_type];
    }

    public function getDepositableTypeModelAttribute($value)
    {
        return $this->depositables[$this->depositable_type];
    }

    public function setDepositableTypeAttribute($value)
    {
        $this->attributes['depositable_type'] = $this->depositables[$value];
    }

    public function setEmpezableTypeAttribute($value)
    {
        $this->attributes['empezable_type'] = $this->empezables[$value];
    }

    public function setMontoPagadoAttribute($value)
    {
        $this->attributes['monto_pagado'] = str_replace(",", "", $value);
    }



    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    //Relaciones
    public function facturas() {
        return $this->morphedByMany('Flexio\Modulo\FacturasCompras\Models\FacturaCompra', 'pagable', 'pag_pagos_pagables', 'pago_id')
                        ->withPivot('monto_pagado', 'pagable_id', 'pagable_type', 'empresa_id')->withTimestamps();
    }

    public function movimientos_monetarios() {
        return $this->morphedByMany('Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros', 'pagable', 'pag_pagos_pagables', 'pago_id')
                        ->withPivot('monto_pagado', 'pagable_id', 'pagable_type', 'empresa_id')->withTimestamps();
    }

    public function transferencias()
    {
        return $this->morphedByMany('Flexio\Modulo\Cajas\Models\Transferencias','pagable', 'pag_pagos_pagables','pago_id')
                ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function facturas_pagadas()
    {
      $factura = $this->facturas()->filter(function ($value, $key) {
          return (int)$value->pivot->monto_pagado > 0;
      });
      return $factura->first();
    }

    public function planillas()
    {
        return $this->belongsToMany('Flexio\Modulo\Planilla\Models\Planilla', 'pag_pagos_pagables', 'pago_id', 'pagable_id')
                ->where("pagable_type", "Flexio\Modulo\Planilla\Models\Planilla")
                ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function retiros()
    {
        return $this->belongsToMany('Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros', 'pag_pagos_pagables', 'pago_id', 'pagable_id')
                ->where("pagable_type", "Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros")
                ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function pagos_extraordinarios()
    {
        return $this->belongsToMany('Flexio\Modulo\Comisiones\Models\Comisiones', 'pag_pagos_pagables', 'pago_id', 'pagable_id')
                ->where("pagable_type", "Flexio\Modulo\Comisiones\Models\Comisiones")
                ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function anticipo()
    {
        return $this->morphedByMany('Flexio\Modulo\Anticipos\Models\Anticipo','pagable', 'pag_pagos_pagables','pago_id')
                ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }
	
	public function honorario()
    {
        return $this->belongsToMany('Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros', 'pag_pagos_pagables','pago_id','pagable_id')->select('seg_comisiones.no_comision', 'seg_comisiones.fecha', 'seg_comisiones.monto_recibo')
                ->withPivot('monto_pagado','pagable_id','pagable_type','empresa_id')->withTimestamps();
    }

    public function empezable()
    {
       return $this->morphTo();
    }

    public function proveedor()
    {
        return $this->belongsTo('Flexio\Modulo\Proveedores\Models\Proveedores', 'proveedor_id');
    }

    public function colaborador()
    {
        return $this->belongsTo('Flexio\Modulo\Colaboradores\Models\Colaboradores', 'proveedor_id');
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
    }

    public function metodo_pago()
    {
        return $this->hasMany('Flexio\Modulo\Pagos\Models\PagosMetodos','pago_id');
    }

    public function catalogo_estado()
    {
        return $this->belongsTo('Flexio\Modulo\Pagos\Models\PagosCatalogos','estado','etiqueta')->where('tipo','=','etapa3');
    }

    //scopes
    public function scopeDeCodigo($query, $codigo)
    {
        return $query->where("codigo", $codigo);
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeUuids($query, $uuids)
    {
        $aux = [];

        foreach($uuids as $uuid)
        {
            $aux[] = hex2bin($uuid);
        }

        return $query->whereIn("uuid_pago", $aux);
    }

    public function scopeDeUuid($query, $uuid_pago)
    {
        return $query->where("uuid_pago", hex2bin($uuid_pago));
    }

    public function aplicar()
    {
        $this->estado = 'aplicado';
        $this->save();
    }

    public function chequeEnTransito()
    {
        $this->estado = 'cheque_en_transito';
        $this->save();
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function pagos_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("pagos/ver/".$this->uuid_pago);
    }
    public function getIconoAttribute(){
        return 'fa fa-shopping-cart';
    }
    public function historial(){
        return $this->morphMany(Historial::class,'historiable');
    }

    public function getNombreProveedorAttribute(){
        if(is_null($this->proveedor)){
            return "";
        }
    return $this->proveedor->nombre;
  }
   /* public function getCodigoAttribute(){
        return $this->codigo;
    }*/
}
