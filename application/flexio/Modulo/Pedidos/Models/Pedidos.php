<?php
namespace Flexio\Modulo\Pedidos\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Documentos\Models\Documentos;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Politicas\PoliticableTrait;

class Pedidos extends Model
{
    use RevisionableTrait;
    use PoliticableTrait;
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
    public static function boot() {
        parent::boot();
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

    public function getComprableAttribute()
    {
        $items_por_aprobar = $this->pedidos_items()
                            ->join('inv_items', 'inv_items.id', '=', 'ped_pedidos_inv_items.id_item')
                            ->where('inv_items.estado', 9)->count();

        //en cotizacion o pedido parcial
        return ($this->id_estado == 2 || $this->id_estado == 3) && $items_por_aprobar == 0;
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

        return $query->where(function($q){
            $q->where('id_estado','2')//en cotizacion
                    ->orWhere('id_estado','3');//parcial
        });

    }

    public function pedidos_items(){

        return $this->hasMany('Flexio\Modulo\Pedidos\Models\PedidosItems', 'id_pedido', 'id');

    }

    public function estado(){

        return $this->belongsTo('Flexio\Modulo\Pedidos\Models\PedidosCat', 'id_estado', 'id_cat');

    }

    public function centro_contable(){

        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'uuid_centro', 'uuid_centro');

    }


    public function comentario(){
    	return $this->morphMany(Comentario::class,'comentable');
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

}
