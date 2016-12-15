<?php
namespace Flexio\Modulo\EntregasAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItemsDetalles;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerHistorial;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class EntregasAlquiler extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','fecha_entrega','entregable_id','entregable_type','cliente_id','centro_facturacion_id','estado_id','saldo_facturar','empresa_id','created_by','created_at','updated_at'];

    protected $table    = 'entalq_entregas_alquiler';
    protected $fillable = ['codigo','fecha_entrega','entregable_id','entregable_type','cliente_id','centro_facturacion_id','estado_id','saldo_facturar','empresa_id','created_by','created_at','updated_at'];
    protected $guarded  = ['id','uuid_entrega_alquiler'];
    protected $appends      = ['icono','enlace'];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_entrega_alquiler' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    public function getUuidEntregaAlquilerAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
            'href'  => $this->enlace,
            'class' => 'link'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlA')->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }

    public function getSaldoFacturarCurrencyAttribute()
    {
        return "$".number_format($this->saldo_facturar, 2, '.', ',');
    }

    public function getSaldoFacturarLabelAttribute()
    {
        $attrs = [
            'style'  => 'border: #d9534f solid 2px;color: #d9534f;width: 100%;background: transparent;padding: 2px 7px;text-align: center;font-weight: bold;'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlLabel')->setAttrs($attrs)->setHtml($this->saldo_facturar_currency)->getSalida();
    }

    public function getEnlaceAttribute()
    {
        return base_url('entregas_alquiler/editar/'.$this->uuid_entrega_alquiler);
    }

    public function getFechaEntregaAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }

    public function getFechaFormatAttribute()
    {
        //
        //return $this->fecha->format('d/m/Y');

        $data = Carbon::createFromFormat('Y-m-d H:i:s', $this->fecha_entrega);
        return $data->format('d/m/Y');

    }





    public function setFechaEntregaAttribute($date)
    {
        return $this->attributes['fecha_entrega'] = Carbon::createFromFormat('d/m/Y H:i', $date, 'America/Panama');
    }

    public function cliente()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');
    }
    public function items_relacionados()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');
    }
     public function getItemsAttribute()
    {
        $entrega_id = $this->id;
        $aux = $this->entregable->contratos_items->filter(function($contrato_item) use ($entrega_id){
            $contrato_item->contratos_items_detalles;
            $contrato_item->contratos_items_detalles_entregas;
            return $contrato_item->contratos_items_detalles()->where(function($query) use ($entrega_id){
                $query->where('operacion_type', 'Flexio\\Modulo\\EntregasAlquiler\\Models\\EntregasAlquiler')
                 ->where('operacion_id', $entrega_id);
            })->count() > 0;
        });

        $aux->load('item','item.seriales','item.atributos');
        return $aux;
    }

     public function entregable()
    {
        return $this->morphTo();
    }

    function cargos(){
    	return $this->morphMany(CargosAlquiler::class, 'cargoable');
    }

    public function estado()
    {
        return $this->belongsTo('Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquilerCatalogos','estado_id');
    }

    function scopeEstadoEntregado($query) {
    	return $query->whereHas('estado', function ($query) {
    		$query->where('tipo', '=', 'estado')->where('nombre', 'LIKE', '%entregado%');
    	});
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa','empresa_id');
    }

    public function contratos_alquiler()
    {
        return $this->belongsTo('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler', 'entregable_id')
                ->where('entregable_type','Flexio\\Modulo\\ContratosAlquiler\\Models\\ContratosAlquiler');
    }

    public function scopeDeCodigo($query, $codigo = '')
    {
        return $query->where('codigo', 'like', '%'.$codigo.'%');
    }

    public function scopeDesde($query, $fecha_desde)
    {
        return $query->whereDate('fecha_entrega','>=',Carbon::createFromFormat('d/m/Y', $fecha_desde)->format('Y-m-d'));
    }

    public function scopeDeItem($query, $item_id)
    {

        return $query->whereHas('contratos_alquiler',function($contrato_alquiler) use ($item_id){
            $contrato_alquiler->whereHas('contratos_items',function($contrato_item) use ($item_id){
                $contrato_item->where("contratos_items.item_id", $item_id);
                $contrato_item->whereHas("contratos_items_detalles",function($contrato_item_detalle){
                    $contrato_item_detalle->where('operacion_type', 'Flexio\\Modulo\\EntregasAlquiler\\Models\\EntregasAlquiler');
                });
            });
        });

    }

    public function scopeHasta($query, $fecha_hasta)
    {
        return $query->whereDate('fecha_entrega','<=',Carbon::createFromFormat('d/m/Y', $fecha_hasta)->format('Y-m-d'));
    }

    public function scopeDeContratoAlquiler($query, $contrato_alquiler_id)
    {
        return $query->whereHas('contratos_alquiler', function($contrato_alquiler) use ($contrato_alquiler_id){
            $contrato_alquiler->where('entregable_id',$contrato_alquiler_id);
        });
    }

    public function scopeDeCentroFacturacion($query, $centro_facturacion_id)
    {
        return $query->whereHas('contratos_alquiler', function($contrato_alquiler) use ($centro_facturacion_id){
            $contrato_alquiler->where('centro_facturacion_id',$centro_facturacion_id);
        });
    }

    public function scopeDeNoContrato($query, $no_contrato)
    {
        return $query->whereHas('contratos_alquiler', function($contrato_alquiler) use ($no_contrato){
            $contrato_alquiler->where('codigo','like',"%$no_contrato%");
        });
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getIconoAttribute(){
        return 'fa fa-car';
    }

    /* Funciones agregados por @joseluis */
    public function items_entregados() {
    	//return $this->belongsTo(ContratosAlquilerItemsDetalles::class, 'id', 'operacion_id')->where('contratos_items_detalles.operacion_type','Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler');
    	return $this->hasManyThrough('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItemsDetalles', 'Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler');
    }

    public function contrato_alquiler() {
    	return $this->belongsTo('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler', 'entregable_id');
    }

    public static function boot() {

           parent::boot();
            static::updating(function($collection_modulo) {

                $objetoContrato = ContratosAlquiler::where("id","=",$collection_modulo->entregable_id )->get()->first();

                $cambio = $collection_modulo->getDirty();
                $original = $collection_modulo->getOriginal();

                  if(isset($cambio['estado_id'])){
                    $catalogo = EntregasAlquilerCatalogos::where("id","=",$original['estado_id'])->get();
                    $collection_modulo->load("estado");
                    $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado en Entrega No: ".$collection_modulo->codigo."</b> </br></br>";
                    $descripcion .= "Estado actual: ".$collection_modulo->estado->nombre.'</br></br>';
                    $descripcion .= "Estado anterior: ".$catalogo[0]->nombre.'</br>';

                    $create = [
                      'codigo' => $objetoContrato->codigo,
                      'usuario_id' => $objetoContrato->created_by,
                      'empresa_id' => $objetoContrato->empresa_id,
                      'contrato_id'=> $objetoContrato->id,
                      'tipo'   => "actualizado",
                      'descripcion' => $descripcion
                    ];
                    ContratosAlquilerHistorial::create($create);

                    return $collection_modulo;
                  }


           });

           static::created(function($collection_modulo){

                $objetoContrato = ContratosAlquiler::where("id","=",$collection_modulo->entregable_id )->get()->first();

                $descripcion = "<b style='color:#0080FF; font-size:15px;'>Creó entrega </b></br></br>";
                $descripcion .= "No: ".$collection_modulo->codigo.'</br></br>';
                $descripcion .= "Estado:  Por Aprobar </br>";

                $create = [
                      'codigo' => $objetoContrato->codigo,
                      'usuario_id' => $objetoContrato->created_by,
                      'empresa_id' => $objetoContrato->empresa_id,
                      'contrato_id'=> $objetoContrato->id,
                      'tipo'   => "creado",
                      'descripcion' => $descripcion
                 ];
                 ContratosAlquilerHistorial::create($create);
                 return $collection_modulo;
            });

       }
}
