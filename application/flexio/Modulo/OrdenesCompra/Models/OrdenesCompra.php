<?php
namespace Flexio\Modulo\OrdenesCompra\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Politicas\PoliticableTrait;
//utilities
use Carbon\Carbon as Carbon;

class OrdenesCompra extends Model
{
    use PoliticableTrait;
    use RevisionableTrait;
    // propiedad de politica
    protected $politica = 'orden_compra';
    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['referencia', 'numero', 'uuid_centro', 'uuid_lugar', 'uuid_pedido', 'uuid_proveedor', 'modo_pago_id', 'dias', 'id_estado', 'creado_por', 'id_empresa', 'monto', 'termino_pago'];
    protected $prefijo      = 'OC';
    protected $table        = 'ord_ordenes';
    protected $fillable     = ['referencia', 'numero', 'uuid_centro', 'uuid_lugar', 'uuid_pedido', 'uuid_proveedor', 'modo_pago_id', 'dias', 'id_estado', 'creado_por', 'id_empresa', 'monto', 'termino_pago'];
    protected $guarded      = ['id','uuid_orden'];
    public $timestamps      = false;
    protected $appends =['icono','codigo','enlace','valido_hasta'];


    public function __construct(array $attributes = array()){

        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_orden' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);

    }

    //Finds
    public static function findByUuid($uuid){
        return self::where("uuid_orden", hex2bin($uuid))->first();
    }

    public static function boot() {
            parent::boot();
           static::updating(function($orden) {
                $cambio = $orden->getDirty();
                $original = $orden->getOriginal();
                  if(isset($cambio['id_estado'])){
                    $catalogo = OrdenesCompraCat::where("id_cat","=",$original['id_estado'])->get();
                    $orden->load("estado");
                    $descripcion = "Cambio de estado: ".$catalogo[0]->etiqueta." a ".$orden->estado->etiqueta;

                }
                else if(isset($cambio['uuid_lugar'])){
                     $descripcion = "Cambio de lugar a recibir";
                 }
                  else if(isset($cambio['uuid_proveedor'])){
                     $descripcion = "Cambio el proveedor";
                 }
                  else{
                    $descripcion = "Se ha actualizado la Orden";
                }
                $create = [
                      'codigo' => $orden->numero,
                      'usuario_id' => $orden->creado_por,
                      'empresa_id' => $orden->id_empresa,
                      'orden_id'=> $orden->id,
                      'tipo'   => "actualizado",
                      'descripcion' => $descripcion
                ];
                OrdenesHistorial::create($create);

                $comentario_texto = ['comentario'=>$descripcion,'usuario_id'=>$orden->creado_por];
                $comentario = new Comentario($comentario_texto);
                $orden->comentario()->save($comentario);

                 return $orden;
           });

           static::created(function($orden){
                $comentario_texto = ['comentario'=>"Se ha creado la orden.",'usuario_id'=>$orden->creado_por];
                $comentario = new Comentario($comentario_texto);
                $orden->comentario()->save($comentario);

                $create = [
                      'codigo' => $orden->numero,
                      'usuario_id' => $orden->creado_por,
                      'empresa_id' => $orden->id_empresa,
                      'orden_id'=> $orden->id,
                      'tipo'   => "creado",
                      'descripcion' => "Se ha creado la orden."
                 ];
                OrdenesHistorial::create($create);
                return $orden;
            });

       }
    //GETS
    public function getUuidOrdenAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    ///agregado para landing{
    public function getUuidCentroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidPedidoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidLugarAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidProveedorAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    ////} agregado para landing

    public function getImprimibleAttribute()
    {
        return $this->id_estado == '2' || $this->id_estado == '3' || $this->id_estado == '4';
    }

    public function getNumeroAttribute($value){

        return is_numeric($value) ? 'OC'.sprintf('%08d', $value) : $value;

    }

    public function getCodigoAttribute(){
        return $this->numero;
    }


     public function getValidoHastaAttribute(){
          return date('d-m-Y', strtotime($this->fecha_creacion. ' + 30 day'));
    }


    public function getNumeroDocumentoAttribute(){

        return $this->numero;

    }
   public function historial(){
	  return $this->hasMany(OrdenesHistorial::class,'orden_id');
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

     public function comentario(){
    	return $this->morphMany(Comentario::class,'comentable');
    }

    public function getEnlaceAttribute()
    {
        return base_url("ordenes/ver/".$this->uuid_orden);
    }

    public function getModuloAttribute()
    {
        return 'Orden de compra';//mod series
    }

    public function getTimelineAttribute()
    {
        return [
            "Proveedor: ".$this->proveedor->nombre,
            "Centro contable: ".$this->centro_contable->nombre,
            "Fecha: ".$this->fecha_creacion
        ];
    }
    public function getFechaCreacionAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getEdadAttribute()
    {
        return Carbon::now()->diffForHumans(Carbon::createFromFormat("d-m-Y", $this->fecha_creacion), true);
    }

    public function getTipoSpanAttribute()
    {
        $attrs  = [
            "style" => "float:right;color:orange;"
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType("htmlSpan")->setAttrs($attrs)->setHtml("&Oacute;rden de compra")->getSalida();
    }
    public function getTipoFaAttribute()
    {
        $attrs = [
            "class" => "fa fa-shopping-cart",
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

    public function getProveedorNombreAttribute() {

        if(is_null($this->proveedor)){
            return "";
        }

        return $this->proveedor->nombre;

    }

    //scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("id_empresa", $empresa_id);
    }

    public function scopeDePedido($query, $uuid_pedido)
    {
        return $query->where("uuid_pedido", hex2bin($uuid_pedido));
    }

     public function scopeDeFacturaDecompra($query, $factura_compra_id)
    {
        return $query->whereHas("facturas", function($factura) use ($factura_compra_id){
            $factura->where("id", $factura_compra_id);
        });
    }

    public function scopeFacturables($query)
    {
        return $query->where(function($q){
            $q->where("id_estado", 2)           //->por facturar
                    ->orWhere("id_estado", 3);  //->facturado parcial
        });
    }

    public function scopeListasParaFacturar($query)
    {
        //2.- Orden por facturar
        //3.- Orden facturada parcial
        return $query->where("id_estado", "2")->orWhere("id_estado", "3");
    }

    //relaciones
    public function modo_pago()
    {
        return $this->belongsTo('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompraCat', 'modo_pago_id', 'id_cat');
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'id_empresa');
    }

    public function estado(){

        return $this->belongsTo('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompraCat', 'id_estado', 'id_cat');

    }

    public function facturas()
    {
        return $this->hasMany('Flexio\Modulo\FacturasCompras\Models\FacturaCompra', 'operacion_id', 'id')
                ->where("operacion_type", "Ordenes_orm");
        //return $this->morphMany("Facturas_compras_orm", "operacion");
    }

    public function proveedor() {

        return $this->belongsTo('Flexio\Modulo\Proveedores\Models\Proveedores', "uuid_proveedor_bin", "uuid_proveedor");

    }

    public function externo()
    {
        return $this->proveedor();
    }

    public function getUuidProveedorBinAttribute(){

        return hex2bin($this->uuid_proveedor);

    }

    public function centro_contable(){

        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', "uuid_centro_bin", "uuid_centro");

    }

    public function getUuidCentroBinAttribute(){

        return hex2bin($this->uuid_centro);

    }

    public function bodega(){

        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', "uuid_lugar_bin", "uuid_bodega");

    }

    public function getUuidLugarBinAttribute(){

        return hex2bin($this->uuid_lugar);

    }

    public function getFechaHoraAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->fecha_creacion)->format('d/m/Y @ H:i');
    }

    public function getUbicacionAttribute()
    {
        return $this->bodega;
    }
    public function items() {
        return $this->morphToMany('Flexio\Modulo\Inventarios\Models\Items', 'tipoable', 'lines_items', 'tipoable_id', 'item_id')
            ->withPivot('id', 'uuid_line_item', 'categoria_id', 'empresa_id', 'cantidad', 'unidad_id', 'precio_unidad', 'impuesto_id', 'descuento', 'cuenta_id', 'precio_total', 'impuesto_total', 'descuento_total', 'observacion', 'cantidad2','atributo_id','atributo_text');
    }

    public function lines_items(){

        return $this->morphMany('Flexio\Modulo\Inventarios\Models\LinesItems', 'tipoable');

    }

    public function pedido(){

        return $this->belongsTo('Flexio\Modulo\Pedidos\Models\Pedidos', 'uuid_pedido_bin', 'uuid_pedido');

    }

    public function getUuidPedidoBinAttribute(){

        return hex2bin($this->uuid_pedido);

    }

    public function comprador()
    {
        return $this->belongsTo('Flexio\Modulo\Usuarios\Models\Usuarios', 'creado_por');
    }

    function documentos(){
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public function getIconoAttribute(){
      return 'fa fa-shopping-cart';
    }
    public function landing_comments(){
       return $this->morphMany(Comentario::class,'comentable');
     }
      /*public function politicas_transacciones(){
         return $this->morphMany(OrdenesCompra::class,'comentable');

     }*/

    public function present() {
      return new \Flexio\Modulo\OrdenesCompra\Presenter\OrdenCompraPresenter($this);
    }

    public function anticipos()
    {
        return $this->morphToMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'empezable');
    }
}
