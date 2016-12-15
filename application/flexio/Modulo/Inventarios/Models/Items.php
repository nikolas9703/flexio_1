<?php
namespace Flexio\Modulo\Inventarios\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra as OrdenesCompra;

//utils
use Flexio\Library\Util\FlexioSession;
use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Entradas\Repository\EntradasRepository as entradasRep;
use Flexio\Modulo\Salidas\Repository\SalidasRepository as salidasRep;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository as facturasCompraRep;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Comentario\Models\Comentario;
//Rejects
use Flexio\Modulo\Inventarios\Filters\SerialesFilters as serialesFilters;
//Library
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Items extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','nombre', 'descripcion', 'tipo_id', 'codigo_barra', 'estado', 'item_alquiler', 'tarifa_hora', 'tarifa_diario', 'tarifa_mensual', 'tarifa_4_horas', 'tarifa_6_dias', 'tarifa_15_dias', 'tarifa_28_dias', 'tarifa_30_dias', 'uuid_compra', 'uuid_venta', 'empresa_id', 'creado_por'];

    protected $table        = 'inv_items';
    protected $fillable     = ['codigo','nombre', 'descripcion', 'tipo_id', 'codigo_barra', 'estado', 'item_alquiler', 'tarifa_hora', 'tarifa_diario', 'tarifa_mensual', 'tarifa_4_horas', 'tarifa_6_dias', 'tarifa_15_dias', 'tarifa_28_dias', 'tarifa_30_dias', 'uuid_compra', 'uuid_venta', 'empresa_id', 'creado_por', 'cuentas'];
    protected $guarded      = ['id', 'uuid_item'];
    protected $appends      = ['icono','enlace'];
    //protected $appends      = ['seriales'];
    //protected $appends      = ['cuenta_variante', 'cantidad_disponible', 'costo_promedio'];

    public $timestamps      = false;

    //repositorios
    private $entradasRep;
    private $salidasRep;
    private $facturasCompraRep;
    protected $session;

    public function __construct(array $attributes = array()) {
        $this->entradasRep          = new entradasRep();
        $this->salidasRep           = new salidasRep();
        $this->facturasCompraRep    = new facturasCompraRep();

        //version nueva
        $session = new FlexioSession;
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_item' => Capsule::raw("ORDER_UUID(uuid())"),'empresa_id'=> $session->empresaId())), true);
        parent::__construct($attributes);
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }
    public function setUuidCompraAttribute($value)
    {
        return $this->attributes['uuid_compra'] = hex2bin($value);
    }

    public function getCuentaActivoIdAttribute()
    {
        $cuentas = json_decode($this->cuentas);
        $cuenta_id = "";

        foreach($cuentas as $cuenta)
        {
            if(strrpos($cuenta, "activo") !== false)
            {
                $cuenta_id = str_replace("activo:", "", $cuenta);
            }
        }

        return $cuenta_id;
    }

    public function getCuentaCostoIdAttribute()
    {
        return "";//no se ha definido aun
        //cuentas = json_encode(["activo:2470","ingreso:2541","ingreso:2543"]);
        $cuentas = json_decode($this->cuentas);
        $cuenta_id = "";

        foreach($cuentas as $cuenta)
        {
            if(strrpos($cuenta, "activo") !== false)
            {
                $cuenta_id = str_replace("activo:", "", $cuenta);
            }
        }

        return $cuenta_id;
    }

    public function setUuidVentaAttribute($value)
    {
        return $this->attributes['uuid_venta'] = hex2bin($value);
    }

    public function setCuentasAttribute($value)
    {
        return $this->attributes['cuentas'] = json_encode($value);
    }

    public function getCuentasAttribute($value)
    {
        return strlen($value) > 0 ? $value : "[]";
    }


    //buscadores
    public static function findByUuid($uuid_item){
        return self::where("uuid_item", hex2bin($uuid_item))->first();
    }

    //GETS
    public function getUuidItemAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getNombreCompletoAttribute()
    {
        return $this->codigo.' - '.$this->nombre;
    }
    public function getCodigoEnlaceAttribute()
    {
        return '<a href="'.base_url('inventarios/ver/'. $this->uuid_item).'" style="color:blue;">'.$this->codigo.'</a>';
    }
    public function getBtnEnlaceAttribute()
    {
        return '<a href="'.base_url('inventarios/ver/'. $this->uuid_item).'" class="btn btn-block btn-outline btn-success">Ver Item</a>';
    }

    public function getCostoPromedioAttribute()
    {
        $costo_promedio = $i = 0;

        $clause                 = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["item_id"]      = $this->id;

        foreach($this->facturasCompraRep->get($clause) as $factura)
        {
            if($factura->valida)
            {
                foreach($factura->items2 as $item)
                {
                    if($this->id == $item->pivot->item_id)
                    {
                        $factor_conversion  = $this->factor_conversion($item->pivot->unidad_id);
                        $costo_promedio    += $item->pivot->precio_unidad * $factor_conversion;
                        $i++;
                    }
                }
            }
        }

        return ($i > 0) ? number_format($costo_promedio/$i, 4, '.', '') : number_format($costo_promedio, 4, '.', '');
    }

    public function getCostoPromedioLabelAttribute()
    {
        return '<label style="
                    border: #5BC0DE solid 2px;
                    color: #5BC0DE;
                    width: 100%;
                    background: transparent;
                    padding: 2px 7px;
                    text-align: center;
                    font-weight: bold;
                    ">'.$this->costo_promedio.'</label>';
    }

    //personalizadas
    public function factor_conversion($unidad_id)
    {
        //obteniendo el factor de conversion
        $factor_conversion = 1;

        foreach($this->unidades as $unidad)
        {
            if($unidad->id == $unidad_id)
            {
                $factor_conversion = $unidad->pivot->factor_conversion;
            }
        }
        return $factor_conversion;
    }
    public function unidadBaseModel()
    {
        foreach($this->unidades as $i => $unidad)
        {
            if($unidad->pivot->base == "1")
            {
                $unidadBase = $unidad;
            }
            elseif($i == 0){
                $unidadBase = $unidad;
            }
        }

        return $unidadBase;
    }

    public function getUnidadIdAttribute(){

        $unidad_id = '';
        foreach($this->unidades as $unidad)
        {
            if($unidad->pivot->base == "1")
            {
                return $unidad->id;
            }
        }

        return $unidad_id;

    }



    //SCOPES
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }
    public function scopeTipoActivoFijo($query)
    {
      return $query->where("tipo_id",8);
    }
    public function scopeDeTipo($query, $tipo_item)
    {
    	return $query->whereHas("tipo", function($q) use ($tipo_item){
			$q->where("etiqueta", "like", "%$tipo_item%");
		});
    }
    public function scopeDeCodigo($query, $codigo)
    {
        return $query->where("codigo", "like", "%$codigo%");
    }

    public function scopeDeNombre($query, $nombre)
    {
        return $query->where("nombre", "like", "%$nombre%");
    }

    public function scopeDeBodega($query, $uuid_bodega)
    {
        //me interesan solo los elementos que generan entradas
        //--ajustes
        //--ordenes de compra
        //--traslados
        return  $query->whereHas("ordenes", function($q) use ($uuid_bodega){
                    $q->where("uuid_lugar", hex2bin($uuid_bodega))
                    ->where(function($aux){
                        $aux->where("id_estado", '2')//por facturar
                                ->orWhere("id_estado", '3')//facturada parcial
                                ->orWhere("id_estado", '4');//facturada completa
                    });
                })
                ->orWhereHas("traslados", function($q2) use ($uuid_bodega){
                    $q2->where("uuid_lugar", hex2bin($uuid_bodega));
                })
                ->orWhereHas("ajustes", function($q3) use ($uuid_bodega){
                    $q3->where("uuid_bodega", hex2bin($uuid_bodega));
                });
    }

    //ordenes de compras
    public function ordenes()
    {
        return $this->belongsToMany('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra', 'lines_items', 'item_id', 'tipoable_id')
            ->where("tipoable_type", 'Flexio\\Modulo\\OrdenesCompra\\Models\\OrdenesCompra');
    }
    public function traslados()
    {
        return $this->belongsToMany('Flexio\Modulo\Traslados\Models\Traslados', 'lines_items', 'item_id', 'tipoable_id')
            ->where("tipoable_type", 'Flexio\\Modulo\\Traslados\\Models\\Traslados');
    }
    public function ajustes()
    {
        return $this->belongsToMany('Flexio\Modulo\Ajustes\Models\Ajustes', 'lines_items', 'item_id', 'tipoable_id')
            ->where("tipoable_type", 'Flexio\\Modulo\\Ajustes\\Models\\Ajustes');
    }

    public function scopeDeCategorias($query, $categorias)
    {
        return  $query->whereHas("categorias", function($q) use($categorias){
                    $q->whereIn("inv_categorias.id", $categorias);
                });
    }

    public function scopeDeCategoria($query, $categoria_id)
    {
        return $query->join('inv_items_categorias', 'inv_items_categorias.id_item', '=', 'inv_items.id')
                ->where('inv_items_categorias.id_categoria',$categoria_id)
                ->select('inv_items.*');
    }

    public function scopeDeEstado($query, $estado)
    {
        return $query->where("estado", "=", $estado);
    }

    //RELACIONES

    public function atributos()
    {
        return $this->morphMany('Flexio\Modulo\Atributos\Models\Atributos', 'atributable');
    }

    public function tipo()
    {
    	 return $this->belongsTo('Flexio\Modulo\Inventarios\Models\InventariosCat', 'tipo_id', 'id_cat');
    }
    public function categorias()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Categoria', 'inv_items_categorias', 'id_item', 'id_categoria');
    }

    public function categorias_items(){

        return $this->hasMany('Flexio\Modulo\Inventarios\Models\CategoriasItems', 'id_item');

    }

    //conflicto con nombre de columna en base de datos
    public function state()
    {
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\InventariosCat', 'estado', 'id_cat');
    }
    public function cuenta_variante()
    {
        return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas', 'uuid_variante', 'uuid_cuenta');
    }
    public function cuenta_activo()
    {
    	return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas', 'uuid_activo', 'uuid_cuenta');
    }
    public function cuenta_costo()
    {
    	return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas', 'uuid_gasto', 'uuid_cuenta');
    }
    public function cuenta_ingreso()
    {
    	return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas', 'uuid_ingreso', 'uuid_cuenta');
    }
    public function unidades()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Unidades', 'inv_item_inv_unidad', 'id_item', 'id_unidad')
            ->withPivot("factor_conversion", "base");
    }
    public function seriales()
    {
        return $this->hasMany(Seriales::class, "item_id");
    }
    public function precios() {
    	return  $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Precios', 'inv_items_precios', 'id_item', 'id_precio')
    	->withPivot("precio");
    }

    public function precios_alquiler() {
      return  $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Precios', 'inv_items_precios_alquiler', 'id_item', 'id_precio')
        ->withPivot("id_item","hora","diario","semanal","mensual","mensual","tarifa_4_horas","tarifa_6_dias","tarifa_15_dias","tarifa_28_dias","tarifa_30_dias");
    }

    public function getSerialesTablaHtmlAttribute()
    {
        $attrs  = $this->seriales->pluck("nombre")->toArray();
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return  $html->setType("htmlTableSeriales")->setAttrs($attrs)->setHtml('')->getSalida();
    }

    public function getSerializadoAttribute()
    {
        return $this->tipo_id == "5" || $this->tipo_id == "8";
    }

    public function getInventariadoAttribute()
    {
        return $this->tipo_id == "4" || $this->tipo_id == "5";
    }

    public function comp_serialesEnBodega($uuid_bodega = false)
    {
        $serialesFilters = new serialesFilters();

        $serialesSinConsumos        = $serialesFilters->serialesNoConsumosAprobadosDeBodega($this->seriales, $uuid_bodega);
        $serialesSinAjustes         = $serialesFilters->serialesNoAjustesNegativosDeBodega($serialesSinConsumos, $uuid_bodega);
        $serialesSinOrdenesVentas   = $serialesFilters->serialesNoOrdenesVentasAprobadasDeBodega($serialesSinAjustes, $uuid_bodega);
        $serialesSinFacturasVentas  = $serialesFilters->serialesNoFacturasVentasAprobadasDeBodega($serialesSinOrdenesVentas, $uuid_bodega);
        $serialesSinTraslados       = $serialesFilters->serialesNoTrasladadosDeBodega($serialesSinFacturasVentas, $uuid_bodega);

        return $serialesSinTraslados->pluck("nombre")->toArray();
    }
    public function comp_serialesTablaHtmlEnBodega($uuid_bodega = false)
    {
        $attrs      = $this->comp_serialesEnBodega($uuid_bodega);
        $html       = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return  $html->setType("htmlTableSeriales")->setAttrs($attrs)->setHtml('')->getSalida();
    }

    ////depreciacion de activos
    public function categoria(){
      return $this->belongsToMany(Categoria::class,'inv_items_categorias','id_item', 'id_categoria');
    }
    public function lines_items(){
      return $this->hasOne(LinesItems::class,'item_id')->where('tipoable_type',OrdenesCompra::class);
    }

    public function impuesto_compra(){

        return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Impuestos', 'uuid_compra', 'uuid_impuesto');

    }

    public function impuesto_venta(){

        return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Impuestos', 'uuid_venta', 'uuid_impuesto');

    }


    //personalizadas con sufijo
    public function comp_enInventario($uuid_bodega = NULL)
    {
        $registro   = array(
            "cantidadDisponibleBase"    => 0,//CantidadRecibida(Entradas) - CantidadEnviada(Salidas)
            "cantidadNoDisponibleBase"  => 0,//SalidasReservadas
            "cantidadPedidoBase"        => 0,//(CantidadPorRecibir - CantidadRecibida)(Entradas)
        );

        //Valores que suman //No se toma en cuenta el estado de la entrada
        //porque se cuenta el campo cantidad_recibida que se llena desde
        //el modulo de entrada
        $clause                 = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["item_id"]      = $this->id;


        if($uuid_bodega){
            $clause["recibir_en"] = $clause["enviar_desde"] = $uuid_bodega;
        }

        foreach($this->entradasRep->get($clause) as $e)
        {
            foreach($e->operacion->items as $ei)
            {
                if($this->id == $ei->id)
                {
                    $factor_conversion = $ei->factor_conversion($ei->pivot->unidad_id);
                    $cantidad_recibida = $ei->pivot->cantidad2 * $factor_conversion;
                    $registro["cantidadDisponibleBase"]     += $cantidad_recibida;
                    $registro["cantidadPedidoBase"]         += ($ei->pivot->cantidad * $factor_conversion) - $cantidad_recibida;
                }
            }
        }

        foreach($this->salidasRep->get($clause) as $s)
        {
            foreach($s->operacion->items as $si)
            {
                if($this->id == $si->id)
                {
                    $factor_conversion                  = $si->factor_conversion($si->pivot->unidad_id);
                    $registro["cantidadDisponibleBase"]-= $si->pivot->cantidad * $factor_conversion;

                    if($s->estado_id == "4")//Salida Reservada
                    {
                        $registro["cantidadNoDisponibleBase"]   += $si->pivot->cantidad * $factor_conversion;
                    }
                }
            }
        }


        return $registro;
    }

    public function comp_ultimosTresPrecios()
    {
        $ultimosTresPrecios = array(
            "precio1"   => 0,
            "precio2"   => 0,
            "precio3"   => 0
        );

        $clause                 = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["item_id"]      = $this->id;

        $i = 0;
        foreach($this->entradasRep->get($clause) as $e)
        {
            foreach($e->items as $item)
            {
                if($this->id == $item->pivot->item_id)
                {
                    $factor_conversion = $this->factor_conversion($item->pivot->unidad_id);

                    if($i == 0){
                        $ultimosTresPrecios["precio1"] = $item->pivot->precio_unidad * $factor_conversion;
                    }
                    elseif($i == 1){
                        $ultimosTresPrecios["precio2"] = $item->pivot->precio_unidad * $factor_conversion;
                    }
                    elseif($i == 2){
                        $ultimosTresPrecios["precio3"] = $item->pivot->precio_unidad * $factor_conversion;
                    }
                    $i++;
                }
            }
        }

        return $ultimosTresPrecios;
    }
    function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    //Mostrar Comentarios
    public function comentario_timeline() {
        return $this->hasMany(Comentario::class,'comentable_id')->where('comentable_type','Flexio\\Modulo\\Inventarios\\Models\\Items2');
    }
   /* public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }*/
    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("inventarios/ver/".$this->uuid_item);
    }
    public function getIconoAttribute(){
        return 'fa fa-cubes';
    }
    public function landing_comments() {
        return $this->hasMany(Comentario::class,'comentable_id')->where('comentable_type','Flexio\\Modulo\\Inventarios\\Models\\Items2');
    }

}
