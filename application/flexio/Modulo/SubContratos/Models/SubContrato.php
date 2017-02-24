<?php
namespace Flexio\Modulo\SubContratos\Models;

use Illuminate\Database\Eloquent\Model                 as Model;
use Illuminate\Database\Capsule\Manager                as Capsule;
use Flexio\Modulo\SubContratos\Models\SubContratoMonto as SubContratoMonto;
use Flexio\Modulo\SubContratos\Models\SubContratoTipo  as SubContratoTipo;
use Flexio\Modulo\SubContratos\Models\Adenda           as Adenda;
use Flexio\Modulo\Proveedores\Models\Proveedores       as Proveedores;
use Flexio\Modulo\Catalogos\Models\Catalogo;
use Carbon\Carbon                                      as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Historial\Models\Historial;
use Flexio\Modulo\SubContratos\Observer\SubContratoObserver;

class SubContrato extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = [
        'codigo',
        'proveedor_id',
        'empresa_id',
        'fecha_inicio',
        'fecha_final',
        'referencia',
        'centro_id',
        'monto_subcontrato'
    ];
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'sub_subcontratos';

    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [
        'codigo',
        'proveedor_id',
        'tipo_subcontrato_id',
        'empresa_id',
        'fecha_inicio',
        'fecha_final',
        'referencia',
        'centro_id',
        'monto_subcontrato',
        'estado',
        'creado_por'
    ];

    protected $casts = [
        'monto_subcontrato' => 'float',
    ];

    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id','uuid_subcontrato'];
    protected $appends      = ['icono','enlace'];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_subcontrato' => Capsule::raw("ORDER_UUID(uuid())")
            )), true);
        parent::__construct($attributes);
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
        SubContrato::observe(SubContratoObserver::class);
    }

    public function getUuidSubcontratoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function catalogo_estado()
    {
        return $this->belongsTo('Flexio\Modulo\Catalogos\Models\Catalogo','estado','etiqueta')
        ->where('tipo','=','estado')->where('modulo','subcontratos');
    }

    public function getProveedorNombreAttribute(){
        if(is_null($this->proveedor)){
            return '';
        }

        return $this->proveedor->nombre;
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }

    public function getFacturableAttribute()
    {
        $subcontratos = SubContrato::where("id", $this->id)->get();

        $array_ids =    $subcontratos->filter(function($subcontrato){
                            return $subcontrato->por_facturar() > 0 || $subcontrato->facturado() == 0;
                        })->pluck("id")->toArray();

        return count($array_ids) > 0;
    }

    public function getTipoSubcontratoAttribute()
    {
        $tipo_subcontrato = $this->tipo_subcontrato()->first();
        return !empty($tipo_subcontrato) && $tipo_subcontrato != null ? $tipo_subcontrato->valor : "";
    }

    public function getFechaInicioAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
    }

    public function getFechaFinalAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
    }

    public function setFechaInicioAttribute($value)
    {
        $this->attributes['fecha_inicio'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function setFechaFinalAttribute($value)
    {
        $this->attributes['fecha_final'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function setMontoSubcontratoAttribute($value)
    {
        $this->attributes['monto_subcontrato'] = str_replace(',', '', $value);
    }

    public function subcontrato_montos()
    {
        return $this->hasMany(SubContratoMonto::class, 'subcontrato_id', 'id');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeFacturables($query, $empresa_id)
    {
        $subcontratos = SubContrato::deEmpresa($empresa_id)->get();

        $array_ids =    $subcontratos->filter(function($subcontrato){
                            return $subcontrato->por_facturar() > 0 || $subcontrato->facturado() == 0;
                        })->pluck("id")->toArray();

        return $query->whereIn("id", $array_ids);
    }

    public function scopePagables($query, $empresa_id)
    {
        $subcontratos = SubContrato::deEmpresa($empresa_id)->get();

        $array_ids =    $subcontratos->filter(function($subcontrato){
                            return $subcontrato->facturas_por_pagar->count() > 0;
                        })->pluck("id")->toArray();

        return $query->whereIn("id", $array_ids);
    }

    public function scopeDeMontoDesde($query, $monto)
    {
        return $query->whereHas('subcontrato_montos', function($q) use ($monto){
            $q->groupBy('sub_subcontratos_montos.subcontrato_id');
            $q->havingRaw('sum(sub_subcontratos_montos.monto) >= '.$monto);
        });
    }

    public function scopeDeMontoHasta($query, $monto)
    {
        return $query->whereHas('subcontrato_montos', function($q) use ($monto){
            $q->groupBy('sub_subcontratos_montos.subcontrato_id');
            $q->havingRaw('sum(sub_subcontratos_montos.monto) <= '.$monto);
        });
    }

    public function scopeDeMontoOriginal($query, $empresa_id, $monto_original)
    {
        $subcontratos = SubContrato::deEmpresa($empresa_id)->get();

        $array_ids =    $subcontratos->filter(function($subcontrato) use ($monto_original){
                            return round($subcontrato->subcontrato_montos()->sum('monto'),2) == round($monto_original,2);
                        })->pluck("id")->toArray();

        return $query->whereIn("id", $array_ids);
    }

    public function tipo()
    {
        return $this->hasMany(SubContratoTipo::class, 'subcontrato_id', 'id');
    }

    public function tipo_subcontrato()
    {
        return $this->hasMany(Catalogo::class, 'id', 'tipo_subcontrato_id');
    }

    public function tipo_abono()
    {
        return $this->hasMany(SubContratoTipo::class, 'subcontrato_id', 'id')->where('tipo', '=', 'abono');
    }

    public function tipo_retenido()
    {
        return $this->hasMany(SubContratoTipo::class, 'subcontrato_id', 'id')->where('tipo', '=', 'retenido');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'proveedor_id');
    }

    public function centro_contable()
    {
        return $this->belongsTo('Centros_orm','centro_id');
    }

    public function adenda()
    {
        return $this->hasMany(Adenda::class, 'subcontrato_id', 'id');
    }

    //la relacion es con facturas de compras
    public function facturas()
    {
        return $this->morphMany('Flexio\Modulo\FacturasCompras\Models\FacturaCompra', 'faccom_facturas', 'operacion_type', 'operacion_id');
    }

    public function pagos_retenido()
    {
        return $this->morphMany('Flexio\Modulo\Pagos\Models\Pagos', 'pag_pagos','empezable_type','empezable_id');
    }

    public function adenda_cuenta(){
        return $this->hasManyThrough(AdendaMonto::class, Adenda::class,'subcontrato_id','adenda_id','id');
    }

    public function facturas_por_pagar()
    {
        //14 -> por pagar
        //15 -> pagada parcial
        return $this->facturas()
                ->whereIn("faccom_facturas.estado_id", ['14', '15']);
    }

    public function facturas_habilitadas()
    {
        //14 -> por pagar
        //15 -> pagada parcial
        //16 -> pagada completa
        return $this->facturas()
                ->whereIn('faccom_facturas.estado_id',['14','15','16']);
    }

    public function facturas_cobro_parcial()
    {
        //15 -> pagada parcial
        return $this->facturas()
                ->whereIn('faccom_facturas.estado_id',['15']);
    }

    public function monto_original()
    {
        return $this->subcontrato_montos()->sum('monto');
    }

    public function monto_adenda()
    {
        return (float)$this->adenda()->sum('monto_adenda');
    }

    //por facturar deberia ser la sumatoria del saldo de las facturas asociadas
    public function por_facturar()
    {
        return $this->monto_subcontrato - $this->facturado();
    }

    //facturado debe ser el subtotal de facturas
    public function facturado()
    {
        return $this->facturas_habilitadas()->join('faccom_facturas_items','faccom_facturas.id', '=', 'faccom_facturas_items.factura_id')->sum('faccom_facturas_items.subtotal');
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function subcontratos_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("subcontratos/ver/".$this->uuid_subcontrato);
    }
    public function getIconoAttribute(){
        return 'fa fa-file-text';
    }
    function present(){
     return new \Flexio\Modulo\SubContratos\Presenter\SubContratoPresenter($this);
   }

   public function anticipos()
   {
       return $this->morphToMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'empezable');
   }
   public function anticipos_aprobados()
   {
       return $this->morphToMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'empezable')->where('atc_anticipos.estado','aprobado');
   }
   public function anticipos_no_anulados()
   {
       return $this->morphToMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'empezable')->whereIn('estado',['por_aprobar','aprobado']);
   }
   function documentos(){
    	return $this->morphMany(Documentos::class, 'documentable');
   }

   public function scopeDeFiltro($query, $campo)
   {
       $queryFilter = new \Flexio\Modulo\SubContratos\Services\SubContratoQueryFilters;
       return $queryFilter->apply($query, $campo);
   }

    public function historial(){
        return $this->morphMany(Historial::class,'historiable');
    }

}
