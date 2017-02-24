<?php
namespace Flexio\Modulo\Anticipos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Anticipos\Services\ScopableAnticipo;
use Flexio\Library\Util\GenerarCodigo;
use Flexio\Politicas\PoliticableTrait;


class Anticipo extends Model
{

    use ScopableAnticipo, PoliticableTrait, Pagable, OrdenVentaAnticipable;

    protected $table = 'atc_anticipos';
    protected $politica = 'anticipo';
    protected $fillable = ['codigo','empresa_id','fecha_anticipo','anticipable_id','anticipable_type','depositable_id','depositable_type','estado','monto','metodo_anticipo','referencia', 'creado_por', 'centro_contable_id'];

    protected $guarded = ['id','uuid_anticipo'];
    protected $casts =['referencia' => 'array','monto'=>'float'];

    protected $appends = ['icono','enlace'];

    //relaciones con aticipos
    protected $modelDepositables = ['banco' => 'Flexio\\Modulo\\Contabilidad\\Models\\Cuentas',
                                    'caja' => 'Flexio\\Modulo\\Cajas\\Models\\Cajas'];
    protected $modelEmpezable = [];


    public static function boot() {
        parent::boot();
    }

    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_anticipo' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }


    public function getUuidAnticipoAttribute($value) {
    	return strtoupper(bin2hex($value));
    }

    function setCodigoAttribute($value){
        $year = Carbon::now()->format('y');
        return $this->attributes['codigo'] = GenerarCodigo::setCodigo('ANT'.$year, $value);
    }

    public function getFechaAnticipoAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function getHasProveedorAttribute()
    {
        return $this->anticipable_type == 'Flexio\Modulo\Proveedores\Models\Proveedores';
    }

    public function setFechaAnticipoAttribute($date){

        if (Carbon::createFromFormat('d/m/Y', $date, 'America/Panama') !== false) {
            return $this->attributes['fecha_anticipo'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
        }
        return $this->attributes['fecha_anticipo'] =  Carbon::now();
    }

    public function getEnlaceAttribute()
    {
    	return base_url("anticipos/ver/".$this->uuid_anticipo);
    }

    public function getNumeroDocumentoAttribute(){
        $objDocumento = new \Flexio\Modulo\Anticipos\Services\TipoDocumento($this);

        if(is_null($objDocumento->documento())){
            return $this->anticipable->codigo;
        }

        return $objDocumento->documento();
    }

    public function getDocumentoEnlaceAttribute()
    {
        $objEnlace = new \Flexio\Modulo\Anticipos\Services\AnticipoTipoEnlace($this);

        if(is_null($objEnlace->enlace())){
            return $this->anticipable->enlace;
        }

        return $objEnlace->enlace();
    }


    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
    }


    public function anticipable()
    {
       return $this->morphTo();
    }

    public function catalogo_estado()
    {
        return $this->belongsTo('Flexio\Modulo\Catalogos\Models\Catalogo','estado','etiqueta')->where('tipo','=','estado')
        ->where('modulo','=','anticipo');
    }

    public function catalogo_anticipo()
    {
        return $this->belongsTo('Flexio\Modulo\Catalogos\Models\Catalogo','metodo_anticipo','etiqueta')->where('tipo','=','metodo_anticipo')
        ->where('modulo','=','anticipo');
    }


    public function present(){
        return new \Flexio\Modulo\Anticipos\Presenter\AnticipoPresenter($this);
    }

    public function orden_compra()
    {
       return $this->morphedByMany('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra', 'empezable')->withPivot('empezable_type');
    }

   public function subcontrato()
   {
      return $this->morphedByMany('Flexio\Modulo\SubContratos\Models\SubContrato', 'empezable')->withPivot('empezable_type');
   }

   public function documentos()
   {
      return $this->morphMany('Flexio\Modulo\Documentos\Models\Documentos', 'documentable');
   }

   public function orden_venta()
   {
      return $this->morphedByMany('Flexio\Modulo\OrdenesVentas\Models\OrdenVenta', 'empezable')->withPivot('empezable_type');
   }

   public function contrato()
   {
      return $this->morphedByMany('Flexio\Modulo\Contratos\Models\Contrato', 'empezable')->withPivot('empezable_type');
   }

   //funciones del landing_page
   public function getIconoAttribute(){
     return 'fa fa-shopping-cart';
   }
   public function landing_comments(){
      return $this->morphMany('Flexio\Modulo\Comentario\Models\Comentario','comentable');
    }
    function sistema_transaccion(){
        return $this->morphMany('Flexio\Modulo\Transaccion\Models\SysTransaccion','linkable');
    }

    public function pagos()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables', '', "pago_id")
        ->withPivot('monto_pagado','empresa_id')->withTimestamps()
        ->where("pag_pagos.estado", "aplicado")
        ->where('empezable_type','=','Flexio\\Modulo\\Anticipos\\Models\\Anticipo');
    }

    public function getSumaPagosAttribute() {
        return $this->pagos()->sum("pag_pagos_pagables.monto_pagado");
    }

    public function getSaldoAttribute() {
        $pagos = $this->suma_pagos;
        return $this->monto - $pagos;
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Anticipos\Services\AnticipoFilters;
        return $queryFilter->apply($query, $campo);
    }
}
