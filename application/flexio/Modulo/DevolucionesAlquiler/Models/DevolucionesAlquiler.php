<?php
namespace Flexio\Modulo\DevolucionesAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerHistorial;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;

class DevolucionesAlquiler extends Model
{

    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','fecha_devolucion','estado_id','referencia','empresa_id','created_by','created_at','updated_at','fecha_inicio_contrato','fecha_fin_contrato','cliente_id','recibido_id','vendedor_id','observaciones','tipo_contrato'];

    protected $table    = 'devalq_devoluciones_alquiler';
    protected $fillable = ['codigo','fecha_devolucion','estado_id','referencia','empresa_id','created_by','created_at','updated_at','fecha_inicio_contrato','fecha_fin_contrato','cliente_id','recibido_id','vendedor_id','observaciones','tipo_contrato'];
    protected $guarded  = ['id','uuid_devolucion_alquiler'];
    protected $appends      = ['icono','enlace'];

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_devolucion_alquiler' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    public function getUuidDevolucionAlquilerAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function getNumeroDocumentoAttribute() {
        return $this->codigo;
    }

    public function getNumeroDocumentoEnlaceAttribute() {
        $attrs = [
            'href'  => $this->enlace,
            'class' => 'link'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlA')->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }

    public function getEnlaceAttribute() {
        return base_url('devoluciones_alquiler/editar/'.$this->uuid_devolucion_alquiler);
    }

     public function getItemsAttribute() {
        $devolucion_id = $this->id;
          $aux =$this->entregas->first()->entregable->contratos_items->filter(function($contrato_item) use ($devolucion_id){
            $contrato_item->contratos_items_detalles;
            $contrato_item->contratos_items_detalles_devoluciones;
            return $contrato_item->contratos_items_detalles()->where(function($query) use ($devolucion_id){
                $query->where('operacion_type','Flexio\\Modulo\\DevolucionesAlquiler\\Models\\DevolucionesAlquiler')
                ->where('operacion_id', $devolucion_id);
            })->count() > 0;
        });
         return $aux;
    }

    public function scopeDeItem($query, $item_id)
    {

        return $query->whereHas('entregas', function($entrega) use ($item_id){
            $entrega->whereHas('contratos_alquiler',function($contrato_alquiler) use ($item_id){
                $contrato_alquiler->whereHas('contratos_items',function($contrato_item) use ($item_id){
                    $contrato_item->where("contratos_items.item_id", $item_id);
                    $contrato_item->whereHas("contratos_items_detalles",function($contrato_item_detalle){
                        $contrato_item_detalle->where('operacion_type', 'Flexio\\Modulo\\DevolucionesAlquiler\\Models\\DevolucionesAlquiler');
                    });
                });
            });
        });

    }

    public function cliente()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');
    }

    public function getItemscontratosAttribute() {
        $devolucion_id = $this->id;


        $aux =$this->contratos->first()->contratos_items->filter(function($contrato_item) use ($devolucion_id){
            $contrato_item->contratos_items_detalles;
            $contrato_item->contratos_items_detalles_devoluciones;
            return $contrato_item->contratos_items_detalles()->where(function($query) use ($devolucion_id){
                $query->where('operacion_type','Flexio\\Modulo\\DevolucionesAlquiler\\Models\\DevolucionesAlquiler')
                ->where('operacion_id', $devolucion_id);
            })->count() > 0;
        });
            return $aux;
    }
    public function getFechaDevolucionAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }

    /*public function getFechaAlquilerContratoAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }*/
    public function getFechaInicioContratoAttribute($date) {
    	return Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }
    public function getFechaFinContratoAttribute($date) {
    	return $date != "0000-00-00 00:00:00" && $date != "" ? Carbon::createFromFormat('Y-m-d H:i:s', $date) : "0000-00-00 00:00:00";
    }

    public function setFechaDevolucionAttribute($date) {
        return $this->attributes['fecha_devolucion'] = Carbon::createFromFormat('d/m/Y H:i:s', $date, 'America/Panama');
    }
    public function setFechaAlquilerContratoAttribute($date) {
        return $this->attributes['fecha_alquiler_contrato'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }
    public function setFechaInicioContratoAttribute($date) {
    	return $this->attributes['fecha_inicio_contrato'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }
    public function setFechaFinContratoAttribute($date) {
    	return $this->attributes['fecha_fin_contrato'] = $date != "0000-00-00 00:00:00" && $date != "" ? Carbon::createFromFormat('d/m/Y', $date, 'America/Panama') : "";
    }
    public function estado() {
        return $this->belongsTo('Flexio\Modulo\DevolucionesAlquiler\Models\DevolucionesAlquilerCatalogos','estado_id');
    }

    public function empresa() {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa','empresa_id');
    }

    public function entregas() {
        return $this->belongsToMany('Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler', 'entregas_devoluciones', 'devolucion_id', 'entrega_id');
    }

    public function ubicacion()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');
    }

    public function externo()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');
    }

    public function getModuloAttribute()
    {
        return 'Retorno';//mod series
    }

    public function getTimelineAttribute()
    {
        return [
            "Cliente: ".$this->cliente->nombre,
            "Fecha: ".Carbon::createFromFormat("Y-m-d H:i:s", $this->created_at)->format('d-m-Y')
        ];
    }

    public function getTipoSpanAttribute()
    {
        $attrs  = [
            "style" => "float:right;color:orange;"
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType("htmlSpan")->setAttrs($attrs)->setHtml("Retorno")->getSalida();
    }
    public function getTipoFaAttribute()
    {
        $attrs = [
            "class" => "fa fa-car",
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return  $html->setType("htmlI")->setAttrs($attrs)->setHtml('')->getSalida();
    }
    public function getTimeAgoAttribute()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->created_at)->diffForHumans();
    }
    public function getDiaMesAttribute()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->created_at)->formatLocalized('%d de %B');
    }

    public function getFechaHoraAttribute()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->created_at)->format('d/m/Y @ H:i');
    }

    public function scopeDeEntregaAlquiler($query, $entrega_alquiler_id)
    {
        return $query->whereHas('entregas',function($entrega) use ($entrega_alquiler_id){
            $entrega->where('entalq_entregas_alquiler.id', $entrega_alquiler_id);
        });
    }

    public function contratos() {
        return $this->belongsToMany('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler', 'entregas_devoluciones', 'devolucion_id', 'entrega_id');
    }

    public function contratos_alquiler_retornos(){
        return $this->belongsToMany('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItems', 'contratos_items_detalles', 'operacion_id', 'relacion_id');
    }

    public function getFechaFormatAttribute() {
        $data = Carbon::createFromFormat('Y-m-d H:i:s', $this->fecha_devolucion);
        return $data->format('d/m/Y');

    }

    public function scopeDeCodigo($query, $codigo = '') {
        return $query->where('codigo', 'like', '%'.$codigo.'%');
    }

    public function scopeDesde($query, $fecha_desde) {
        return $query->whereDate('fecha_devolucion','>=',Carbon::createFromFormat('d/m/Y', $fecha_desde)->format('Y-m-d'));
    }

    public function scopeHasta($query, $fecha_hasta) {
        return $query->whereDate('fecha_devolucion','<=',Carbon::createFromFormat('d/m/Y', $fecha_hasta)->format('Y-m-d'));
    }

    public function scopeDeContratoAlquiler($query, $contrato_alquiler_id) {
        return $query->whereHas('entregas', function($entrega) use ($contrato_alquiler_id){
            $entrega->whereHas('contratos_alquiler', function($contrato_alquiler) use ($contrato_alquiler_id){
                $contrato_alquiler->where('entregable_id',$contrato_alquiler_id);
            });
        });
    }

    public function scopeDeNoContrato($query, $no_contrato) {
        return $query->whereHas('entregas', function($entrega) use ($no_contrato){
            $entrega->whereHas('contratos_alquiler', function($contrato_alquiler) use ($no_contrato){
                $contrato_alquiler->where('conalq_contratos_alquiler.codigo','like',"%$no_contrato%");
            });
        });
    }

    public function scopeDeCliente($query, $cliente_id) {
        return $query->whereHas('entregas', function($entrega) use ($cliente_id){
            $entrega->whereHas('contratos_alquiler', function($contrato_alquiler) use ($cliente_id){
                $contrato_alquiler->whereHas('cliente',function($cliente) use ($cliente_id){
                    $cliente->where('cli_clientes.id',$cliente_id);
                });
            });
        });
    }

    public function scopeDeCentroFacturacion($query, $centro_facturacion_id) {
        return $query->whereHas('entregas', function($entrega) use ($centro_facturacion_id){
            $entrega->whereHas('contratos_alquiler', function($contrato_alquiler) use ($centro_facturacion_id){
                $contrato_alquiler->whereHas('centro_facturacion',function($centro_facturacion) use ($centro_facturacion_id){
                    $centro_facturacion->where('cli_centros_facturacion.id',$centro_facturacion_id);
                });
            });
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
    public static function boot() {

           parent::boot();

            static::updating(function($collection_modulo) {

                $cambio = $collection_modulo->getDirty();
                $original = $collection_modulo->getOriginal();
                if(isset($cambio)){
                   list($objetoContrato, $tipo_devolucion, $objetoEntrega) = self::detectandoCambios($collection_modulo);

                  if(isset($cambio['estado_id'])){
                        $catalogo = DevolucionesAlquilerCatalogos::where("id","=",$original['estado_id'])->get();
                        $collection_modulo->load("estado");
                        $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado en retorno No: ".$collection_modulo->codigo."</b> </br></br>";
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


                }


           });

           static::created(function($collection_modulo){

                list($objetoContrato, $tipo_devolucion, $objetoEntrega) = self::detectandoCambios($collection_modulo);

                $descripcion = "<b style='color:#0080FF; font-size:15px;'>Creó retorno  </b></br></br>";
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


      protected static function  detectandoCambios($collection_modulo) {
                 if($collection_modulo->tipo_contrato == 1) //Viene del empezable contrato
                 {
                     $contrato_id = $_POST['campo']['empezar_desde_id'];
                     $objetoContrato = ContratosAlquiler::where("id","=",$contrato_id )->get()->first();
                     $tipo_devolucion = "un contrato ";
                     $objetoEntrega = $objetoContrato;

                  }else{ //Viene del empezable entrega
                     $entrega_id = $_POST['campo']['empezar_desde_id'];
                     $objetoEntrega = EntregasAlquiler::where("id","=",$entrega_id )->get()->first();
                     $objetoContrato = ContratosAlquiler::where("id","=",$objetoEntrega->entregable_id )->get()->first();
                     $tipo_devolucion = "una entrega ";
                  }

                  return array($objetoContrato,$tipo_devolucion, $objetoEntrega) ;
       }

       public function lines_items()
       {
           return $this->morphMany('Flexio\Modulo\Inventarios\Models\LinesItems','tipoable');
       }

       public function scopeDeFiltro($query, $campo)
       {
           $queryFilter = new \Flexio\Modulo\DevolucionesAlquiler\Services\DevolucionAlquilerFilters;
           return $queryFilter->apply($query, $campo);
       }

       public function scopeEstadoDevuelto($query)
       {
          return $query->whereHas('estado', function ($query) {
            $query->where('tipo', '=', 'estado')->where('valor', 'LIKE', '%devuelto%');
          });
       }
}
