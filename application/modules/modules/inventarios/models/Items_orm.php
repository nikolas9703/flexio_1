<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

use Flexio\Modulo\Entradas\Repository\EntradasRepository as entradasRep;
use Flexio\Modulo\Salidas\Repository\SalidasRepository as salidasRep;
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository as ordenesCompraRep;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository as facturasCompraRep;
use Flexio\Modulo\Inventarios\Models\Seriales;

class Items_orm extends Model
{

    //repositorios
    private $entradasRep;
    private $salidasRep;
    private $ordenesCompraRep;
    private $facturasCompraRep;

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'inv_items';


    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Indica el formato de la fecha en el modelo
     * en caso de que aplique
     *
     * @var string
     */
    protected $dateFormat = 'U';


    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['nombre'];


    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];



    /**
     * Instancia de CodeIgniter
     */
    protected $Ci;


    public function __construct() {
        $this->Ci = & get_instance();

        //cargando los modelos
        $this->entradasRep          = new entradasRep();
        $this->salidasRep           = new salidasRep();
        $this->ordenesCompraRep     = new ordenesCompraRep();
        $this->facturasCompraRep    = new facturasCompraRep();
    }


    /**
     * Obtiene uuid_centro
     *
     * Se convierte la data binaria en una representacion
     * hexadecimal
     *
     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getUuidItemAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    
    public function atributos() {
        
        return $this->morphMany('Flexio\Modulo\Atributos\Models\Atributos', 'atributable');
        
    }

    public function getCostoPromedioAttribute() {
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

    public function factor_conversion($unidad_id) {
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

    public function getInventariadoAttribute() {
        return $this->tipo_id == "4" || $this->tipo_id == "5";
    }
      
    /**
     * Obtiene la lista de unidades asociadas al item
     */
    public function unidades() {
        $this->Ci->load->model("inventarios/Unidades_orm");
        return  $this->belongsToMany('Unidades_orm', 'inv_item_inv_unidad', 'id_item', 'id_unidad')
                ->withPivot("base", "factor_conversion");
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_item',hex2bin($uuid))->first();
    }

    public function precios() {
        return  $this->belongsToMany('Precios_orm', 'inv_items_precios', 'id_item', 'id_precio')
                ->withPivot("precio");
    }

    public function unidadBase() {
        $unidadBase = "No aplica";

        foreach($this->item_unidades as $iu)
        {
            if($iu->base == "1")
            {
                $unidadBase = $iu->unidad->nombre;
            }
        }

        return $unidadBase;
    }

    public function unidadBaseModel() {
        foreach($this->item_unidades as $i => $iu)
        {
            if($iu->base == "1")
            {
                $unidadBase = $iu->unidad;
            }
            elseif($i == 0){
                $unidadBase = $iu->unidad;
            }
        }

        return $unidadBase;
    }

    public function item_unidades() {
        $this->Ci->load->model("inventarios/Items_unidades_orm");
        return $this->hasMany('Items_unidades_orm', 'id_item', 'id');
    }

    public function item_categorias() {
        return $this->hasMany('Items_categorias_orm', 'id_item', 'id');
    }

    /**
     * Obtiene la lista de categorias asociadas al item
     */
    public function categorias() {
        return $this->belongsToMany('Categorias_orm', 'inv_items_categorias', 'id_item', 'id_categoria');
    }

    public function seriales()
    {
    	return $this->hasMany(Seriales::class, "item_id");
    }
    
    public function orden() {
        return $this->belongsToMany('Ordenes_orm', 'ord_orden_items', 'id_item', 'id_orden');
    }

    public function pedido() {
        return $this->belongsToMany('Pedidos_orm', 'ped_pedidos_inv_items', 'id_item', 'id_pedido');
    }

    public function traslado() {
        return $this->belongsToMany('Traslados_orm', 'tras_traslados_items', 'id_item', 'id_traslado');
    }


    public function state() {
        return $this->belongsTo('Items_estados_orm', 'estado', 'id_cat');
    }

    public function cuentaGasto() {
        return $this->belongsTo('Cuentas_orm', 'uuid_gasto', 'uuid_cuenta');
    }

    public function impuesto() {
        return $this->belongsTo('Impuestos_orm', 'uuid_venta', 'uuid_impuesto')->select(['impuesto','nombre','cuenta_id','retiene_impuesto','porcentaje_retenido']);

    }

    public function impuestoCompra() {
        return $this->belongsTo('Impuestos_orm', 'uuid_compra', 'uuid_impuesto');
    }

    public function comp_nombre() {
        return $this->codigo." - ".$this->nombre;
    }

    public function suma_pedidosDecimal() {

        $pedidos_items  = new Pedidos_items_orm;
        $pedidos_items  = $pedidos_items
                        ->where("ped_pedidos_inv_items.id_item", "=", $this->id)
                        ->with(array("pedido"   => function($pedido){
                            $pedido->where('ped_pedidos.id_estado', '<', '4');
                        }))
                        ->with(array("item"   => function($item){
                            $item->with(array("item_unidades"   => function($item_unidades){
                                $item_unidades->with("unidad");
                            }));
                        }))
                        ->get();



        $suma_pedidos   = 0;
        $unidad_base    = "Si unidad base";
        foreach($pedidos_items as $row)
        {
            $cantidad           = $row->cantidad;
            $factor_conversion  = 1;

            foreach($row->item->item_unidades as $j)
            {
                if($row->unidad == $j->id_unidad)
                {
                    $factor_conversion  = $j->factor_conversion;
                }

                if($j->base == 1)//Unidad Base
                {
                    $unidad_base = $j->unidad->nombre;
                }
            }

            $suma_pedidos      += $cantidad * $factor_conversion;
        }

        return $suma_pedidos;
    }

    public function scopeDeCategoria($query, $categoria_id) {
        return  $query->whereHas("item_categorias", function($q) use ($categoria_id){
            $q->where("id_categoria", $categoria_id);
        });
    }

    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeCodigo($query, $codigo) {
        return $query->where("codigo", "like", "%$codigo%");
    }

    public function scopeDeNombre($query, $nombre) {
        return $query->where("nombre", "like", "%$nombre%");
    }

    public function scopeDeCategorias($query, $categorias) {
        return  $query->whereHas("categorias", function($q) use($categorias){
                    $q->whereIn("inv_categorias.id", $categorias);
                });
    }

    public function scopeDeEstado($query, $estado) {
        return $query->where("estado", "=", $estado);
    }

    public function scopeDeBodega($query, $uuid_bodega) {
        return  $query->whereHas("orden", function($q) use ($uuid_bodega){
                    $q->where("uuid_lugar", hex2bin($uuid_bodega))
                    ->where("id_estado", '2');//abierta
                })
                ->orWhereHas("traslado", function($q2) use ($uuid_bodega){
                    $q2->where("uuid_lugar", hex2bin($uuid_bodega));
                });
    }



    /**
     * Esta funcion retorna la cantidad disponible que se entiende
     * como la sumatoria de todos los items recibidos en bodega susbtrayendo
     * aquellos que se encuentran en la lista de salida.
     *
     * Esta funcion tendra ajustes a medida que se vayan realizando otros
     * modulos y aplicacion ciertas restriciones
     *
     * @return array
     */
    public function enInventario($uuid_bodega = NULL) {
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
            $clause["recibir_en"] = $uuid_bodega;
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

        //Valores que restan
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

    public function cantidadOrdenadaTrasladada($uuid_pedido = NULL) {
        $cantidadOrdenadaTrasladada = 0;

        $clause = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["uuid_pedido"]  = $uuid_pedido;

        $ordenes_compra = $this->ordenesCompraRep->get($clause);

        foreach($ordenes_compra as $orden)
        {
            if($orden->id_estado != "5")//Orden de Compras No Anuladas
            {
                foreach($orden->items as $item)
                {
                    if($this->id == $item->id)
                    {
                        $factor_conversion          = $item->factor_conversion($item->pivot->unidad_id);
                        $cantidadOrdenadaTrasladada+= $item->pivot->cantidad * $factor_conversion;
                    }
                }
            }
        }

        return $cantidadOrdenadaTrasladada;
    }

    /**
     * Esta funcion no ha sido verificada
     *
     * @return float
     */
    public function precioBase() {
        $precioBase = 0;

        $clause                 = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["item_id"]      = $this->id;

        $i = 0;//Aplica para calcular el promedio del precio base
        foreach($this->entradasRep->get($clause) as $e)
        {
            foreach($e->items as $ei)
            {
                if($this->id == $ei->id)
                {
                    $factor_conversion  = $ei->factor_conversion($ei->pivot->unidad_id);
                    $precioBase        += $ei->pivot->precio_unidad * $factor_conversion;
                    $i++;
                }
            }
        }

        $precioBase /= ($i > 0) ? $i : 1;

        return round($precioBase, 2);
    }

    public function ultimosTresPrecios() {
        $ultimosTresPrecios = array(
            "precio1"   => 0,
            "precio2"   => 0,
            "precio3"   => 0
        );


        $entradas   = Entradas_orm::deEmpresa($this->empresa_id)
                    ->deItem($this->id);
        $i = 0;
        foreach($entradas->get() as $e)
        {
            foreach($e->comp__entradasItemsModel() as $ei)
            {
                if($this->id == $ei->id_item)
                {
                    if($i == 0)
                    {
                        $ultimosTresPrecios["precio1"] = $ei->precioPorFactorConversion();
                        $i++;
                    }
                    elseif($i == 1)
                    {
                        $ultimosTresPrecios["precio2"] = $ei->precioPorFactorConversion();
                        $i++;
                    }
                    elseif($i == 2)
                    {
                        $ultimosTresPrecios["precio3"] = $ei->precioPorFactorConversion();
                        $i++;
                    }

                }
            }
        }

        return $ultimosTresPrecios;
    }

}
