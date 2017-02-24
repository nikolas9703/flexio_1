<?php
namespace Flexio\Modulo\Traslados\Models;

use \Illuminate\Database\Eloquent\Model as Model;

//utilities
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Traslados extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = false;
    protected $keepRevisionOf = ['referencia', 'prefijo', 'numero', 'uuid_lugar', 'uuid_lugar_anterior', 'credito', 'dias', 'id_estado', 'creado por', 'fecha_creacion', 'fecha_entrega', 'id_empresa', 'monto', 'uuid_pedido'];

    protected $prefijo      = 'TRAS';
    protected $table        = 'tras_traslados';
    protected $fillable     = ['referencia','prefijo','numero','uuid_lugar','uuid_lugar_anterior','id_estado','creado_por','fecha_creacion','fecha_entrega','id_empresa','monto','uuid_pedido','observaciones'];
    protected $guarded      = ['id','uuid_traslado'];
    public $timestamps      = false;

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_traslado' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }

    public function setUuidLugarAttribute($value)
    {
        $this->attributes['uuid_lugar'] = hex2bin($value);
    }

    public function setUuidLugarAnteriorAttribute($value)
    {
        $this->attributes['uuid_lugar_anterior'] = hex2bin($value);
    }

    public function setUuidPedidoAttribute($value)
    {
        $this->attributes['uuid_pedido'] = hex2bin($value);
    }

    public function setFechaCreacionAttribute($value)
    {
        $value = str_replace('-', '/', $value);
        $this->attributes['fecha_creacion'] = !empty($value) ? Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d H:i.s') : '';
    }

    public function setFechaEntregaAttribute($value)
    {
        $value = str_replace('-', '/', $value);
        $this->attributes['fecha_entrega'] = (!empty($value) && $value != '0000/00/00 00:00:00') ? Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d H:i.s') : '';
    }

    //comps
    public function comp_numeroDocumento()
    {
        return $this->prefijo.$this->numero;
    }

    //GETS
    public function getModuloAttribute()
    {
        return 'Traslado';
    }

    public function getUuidTrasladoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidAttribute()
    {
        return $this->uuid_traslado;
    }

    public function getNumeroAttribute($value)
    {
        return sprintf('%08d', $value);
    }

    public function getNumeroDocumentoAttribute(){

        return $this->prefijo.$this->numero;
    }
    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
            "href"  => $this->enlace,
            "class" => "link"
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
        return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }
    public function getEnlaceAttribute()
    {
        return base_url("traslados/ver/".$this->uuid_traslado);
    }
    public function getUbicacionAttribute()
    {
        return $this->bodega;
    }

    public function externo()
    {
        return $this->deBodega();
    }

    public function getTimelineAttribute()
    {
        return [
            "Origen: ".$this->deBodega->nombre_codigo,
            "Destino: ".$this->ubicacion->nombre_codigo,
            "Fecha: ".$this->fecha_creacion
        ];
    }
    public function getFechaCreacionAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getFechaHoraAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->fecha_creacion)->format('d/m/Y @ H:i');
    }

    public function getEdadAttribute()
    {
        return Carbon::now()->diffForHumans(Carbon::createFromFormat("d-m-Y", $this->fecha_creacion), true);
    }

    public function getTipoSpanAttribute()
    {
        $attrs  = [
            "style" => "float:right;color:green;"
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType("htmlSpan")->setAttrs($attrs)->setHtml("Traslado")->getSalida();
    }
    public function getTipoFaAttribute()
    {
        $attrs = [
            "class" => "fa fa-cubes",
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return  $html->setType("htmlI")->setAttrs($attrs)->setHtml('')->getSalida();
    }
    public function getTimeAgoAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->fecha_creacion)->diffForHumans();
    }
    public function getDiaMesAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->fecha_creacion)->formatLocalized('%d de %B');
    }

    //relaciones
    public function proveedor() {
        return $this->belongsTo('Flexio\Modulo\Proveedores\Models\Proveedores', "uuid_proveedor", "uuid_proveedor");
    }

    public function pedido()
    {
        return $this->belongsTo('Flexio\Modulo\Pedidos\Models\Pedidos', 'uuid_pedido', 'uuid_pedido');
    }

    public function centro_contable() {
        //un traslado no tiene centro contable
        //return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', "uuid_centro", "uuid_centro");
    }
    public function getDestinoAttribute() {
        return $this->bodega;
    }
    public function getOrigenAttribute() {
        return $this->deBodega;
    }
    public function bodega() {
        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', "uuid_lugar", "uuid_bodega");
    }
    public function deBodega() {
        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', "uuid_lugar_anterior", "uuid_bodega");
    }
    public function items() {
        return $this->morphToMany('Flexio\Modulo\Inventarios\Models\Items', 'tipoable', 'lines_items', 'tipoable_id', 'item_id')
            ->withPivot('id', 'uuid_line_item', 'categoria_id', 'empresa_id', 'cantidad', 'unidad_id', 'precio_unidad', 'impuesto_id', 'descuento', 'cuenta_id', 'precio_total', 'impuesto_total', 'descuento_total', 'observacion', 'cantidad2');
    }

    public function lines_items()
    {
        return $this->morphMany('Flexio\Modulo\Inventarios\Models\LinesItems', 'tipoable');
    }

    public function estado()
    {
        return $this->belongsTo('Flexio\Modulo\Traslados\Models\TrasladoCat', 'id_estado', 'id_cat');
    }

    public function present()
    {
        return new \Flexio\Modulo\Traslados\Presenter\TrasladoPresenter($this);
    }

    public function getCodigoAttribute()
    {
        return $this->prefijo.sprintf('%s', $this->numero);
    }

    public function traslados_items(){

        return $this->morphMany('Flexio\Modulo\Inventarios\Models\LinesItems','tipoable');

    }
    //Mostrar Comentarios
    public function comentario_timeline() {
        return $this->hasMany(Comentario::class,'comentable_id')->where('comentable_type','Flexio\\Modulo\\Traslados\\Models\\Traslados2');
    }
    //functiones para el landing_page
    public function landing_comments() {
        return $this->hasMany(Comentario::class,'comentable_id')->where('comentable_type','Flexio\\Modulo\\Traslados\\Models\\Traslados2');
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Traslados\Services\TrasladoFilters;
        return $queryFilter->apply($query, $campo);
    }

}
