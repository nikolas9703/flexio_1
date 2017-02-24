<?php
namespace Flexio\Modulo\Pedidos\Models;

use Flexio\Modulo\Atributos\Models\Atributos;
use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Politicas\PoliticableTrait;
use Flexio\Notifications\Notify;
use Flexio\Modulo\Pedidos\Observer\PedidoObserver;


class Pedidos extends Model
{
    use RevisionableTrait;
    use PoliticableTrait;
    use Notify;
    //propiedades politicas
    protected $politica = 'pedido';
    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['fecha_creacion', 'referencia', 'numero', 'uuid_centro', 'uuid_lugar', 'id_tipo', 'id_estado', 'creado_por', 'id_empresa', 'observaciones'];

    protected $table        = 'ped_pedidos';
    protected $fillable     = ['uuid_pedido', 'fecha_creacion', 'referencia', 'numero', 'uuid_centro', 'uuid_lugar', 'id_tipo', 'id_estado', 'creado_por', 'id_empresa', 'observaciones'];
    protected $guarded      = ['id'];
    public $timestamps      = false;
    protected $appends =['icono','codigo','enlace'];

    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_pedido' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        Pedidos::observe(PedidoObserver::class);

        static::creating(function($pedido){
            $cambio = $pedido->getDirty();
            if(isset($cambio['id_estado'])){
                $pedido->sendNotify($cambio['id_estado']);
            }
            return $pedido;
        });

        static::updating(function($pedido) {
            $cambio = $pedido->getDirty();
            if(isset($cambio['id_estado'])){
                $pedido->sendNotify($cambio['id_estado']);
            }
            return $pedido;
        });
    }

    public function ordenes()
    {
        //return OrdenesCompra::dePedido($this->uuid_pedido)->get();
        return $this->hasMany(OrdenesCompra::class, 'uuid_pedido', 'uuid_pedido');
    }

    public function getModuloIdAttribute()
    {
        return 20;//table modulos(id)
    }

    public function getUuidPedidoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    ///agregado para landing {
    public function getUuidCentroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidLugarAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    ////} agregado para landing

    public function getNumeroAttribute($value)
    {
     //   if(is_numeric($value)){

            return sprintf('%s', $value);

       // }

       // return $this->numero;

    }

    public function getCodigoAttribute(){
        return $this->numero;
    }

    public function getEnlaceAttribute()
    {
        return base_url("pedidos/ver/".$this->uuid_pedido);
    }

    public function getEmpresaIdAttribute()
    {

        return $this->id_empresa;

    }

    public function getFechaCreacionAttribute($value){

        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');

    }

    public function setFechaCreacionAttribute($date){

        return  $this->attributes['fecha_creacion'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');

    }

    public function setUuidCentroAttribute($value){

        return  $this->attributes['uuid_centro'] = hex2bin($value);

    }

    public function setUuidLugarAttribute($value){

        return  $this->attributes['uuid_lugar'] = hex2bin($value);

    }

    public function scopeOrdenables($query){

        return $query->join('ped_pedidos_inv_items', 'ped_pedidos.id', "=", 'ped_pedidos_inv_items.id_pedido')
        ->join('inv_items', 'ped_pedidos_inv_items.id_item', '=', 'inv_items.id')
        ->where('inv_items.estado', '!=', '9')//approved items
        ->where(function($q){
            $q->where('ped_pedidos.id_estado','2')//en cotizacion
            ->orWhere('ped_pedidos.id_estado','3');//parcial
        })
        ->select('ped_pedidos.*')
        ->groupBy('ped_pedidos.id');

    }

    //this scope is for example... must be delete
    public function scopeDeCategoria($query, $categoria_id)
    {
        return $query->join('inv_items_categorias', 'inv_items_categorias.id_item', '=', 'inv_items.id')
                ->where('inv_items_categorias.id_categoria',$categoria_id)
                ->select('inv_items.*');
    }


    public function getComprableAttribute()
    {
        $items_por_aprobar = $this->pedidos_items()
                            ->join('inv_items', 'inv_items.id', '=', 'ped_pedidos_inv_items.id_item')
                            ->where('inv_items.estado', 9)->count();

        //en cotizacion o pedido parcial
        return ($this->id_estado == 2 || $this->id_estado == 3) && $items_por_aprobar == 0;
    }

    public function pedidos_items(){

        return $this->hasMany('Flexio\Modulo\Pedidos\Models\PedidosItems', 'id_pedido', 'id');

    }

    public function getPedidoItems(){
        $queryPedidoItems = PedidosItems::query()->getQuery()
            ->select([
               // 'ped_pedidos_inv_items.*',
                'categoria_id',
                'id_item as item_id',
                'cuenta as cuenta_id',
                'cantidad',
                'cantidad_usada',
                'unidad as unidad_id ',
                'inv_items.descripcion',
                'contab_impuestos.id as impuesto_id',
                'id_item as item_hidden_id',
                'unidad as unidad_hidden_id',
                'atributo_text',
                'atributo_id',
                "cuentas",
                "contab_cuentas.codigo as cuenta_codigo",
                "contab_cuentas.nombre as cuenta_nombre"
            ])
            ->leftJoin("inv_items", "id_item", "=", "inv_items.id")
            ->leftJoin("contab_impuestos", "inv_items.uuid_compra", "=", "contab_impuestos.uuid_impuesto")
            ->leftJoin("contab_cuentas", "contab_cuentas.id", "=", "ped_pedidos_inv_items.cuenta")
            ->where("id_pedido", "=", $this->id);

//LEFT JOIN `contab_cuentas`  ON contab_cuentas.id =

        return collect($queryPedidoItems->get())->map(function ($pedido) {
            $pedido->facturado = true;
            $pedido->costo_promedio = 0;
            $pedido->items = [];
            $pedido->unidades = [];
            $pedido->atributos = Atributos::query()->where("atributable_id" ,"=", $pedido->item_id)->get();
            return $pedido;
        });
    }

    public function estado(){

        return $this->belongsTo('Flexio\Modulo\Pedidos\Models\PedidosCat', 'id_estado', 'id_cat');

    }

    public function centro_contable(){

        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'uuid_centro', 'uuid_centro');

    }

    public function bodega(){
        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', 'uuid_lugar', 'uuid_bodega');
    }


    public function comentario(){
    	return $this->morphMany(Comentario::class,'comentable');
    }

    public function historial(){
    	return $this->morphMany('Flexio\Modulo\Historial\Models\Historial','historiable');
    }

    function documentos(){
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    //functiones para el landing_page
    public function getIconoAttribute(){
      return 'fa fa-shopping-cart';
    }
    public function landing_comments(){
       return $this->morphMany(Comentario::class,'comentable');
     }
    public function getModuloNotificacionesAttribute()
    {
        return '\Flexio\Modulo\Pedidos\Notifications\PedidoUpdated';
    }
}
