<?php
namespace Flexio\Modulo\NotaCredito\Models;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\GenerarCodigo as GenerarCodigo;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\NotaCredito\Transaccion\AnularTransaccionNotaCredito;
use Flexio\Strategy\Transacciones\Anular\AnularTransaccion;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class NotaCredito extends Model{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','cliente_id','empresa_id','estado','creado_por','total','centro_contable_id','centro_facturacion_id','factura_id','narracion','tipo','fecha','subtotal','impuesto'];

    protected $table = 'venta_nota_creditos';

  protected $fillable = ['codigo','cliente_id','empresa_id','estado','creado_por','total','centro_contable_id','centro_facturacion_id','factura_id','narracion','tipo','fecha','subtotal','impuesto'];

  protected $guarded = ['id','uuid_nota_credito'];
  protected $appends = ['icono','enlace'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array('uuid_nota_credito' => Capsule::raw("ORDER_UUID(uuid())"))), true);
    parent::__construct($attributes);
  }

  public static function boot(){
      parent::boot();
        self::updating(function($nota_credito) {
          $cambio = $nota_credito->getDirty();
          if(isset($cambio['estado']) && $cambio['estado'] =='anulado'){
            $transaccion = new AnularTransaccion;
            $transaccion->anular($nota_credito->fresh(), new AnularTransaccionNotaCredito);
          }
          return $nota_credito;
      });
  }

  function setCodigoAttribute($value){
      return $this->attributes['codigo'] = GenerarCodigo::setCodigo('NC'.Carbon::now()->format('y'), $value);
  }

  // mutators get
 public function getFechaAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
 }

 public function getUuidNotaCreditoAttribute($value){
   return strtoupper(bin2hex($value));
 }

 public function getNombreVendedorAttribute(){
     if(is_null($this->vendedor)){
         return '';
     }
     return $this->vendedor->nombre.' '.$this->vendedor->apellido;
 }

 public function getClienteNombreAttribute() {
        if (is_null($this->cliente)) {
            return '';
        }
        return $this->cliente->nombre;
    }

//relationship
 public function centro_contable(){
   return $this->belongsTo('Centros_orm','centro_contable_id');
 }

 public function setFechaAttribute($date){
   return  $this->attributes['fecha'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
 }

 /*public function setFechaFacturaAttribute($date){
   return $this->attributes['fecha_factura'] = Carbon::createFromFormat('m/d/Y', $date, 'America/Panama');
 }*/

 public function etapa_catalogo(){
   return $this->belongsTo(CatalogoNotaCredito::class,'estado','etiqueta')->where('tipo','=','estado');
 }

 public function items(){
   return $this->hasMany(NotaCreditoItem::class,'nota_credito_id');
 }

 public function total(){
   return $this->items->sum('monto');
 }

 public function cliente()
 {
     return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente', 'cliente_id');
 }

 public function vendedor(){
   return $this->belongsTo('Flexio\Modulo\Usuarios\Models\Usuarios','creado_por');
 }

 public function factura(){
    return $this->belongsTo(FacturaVenta::class, 'factura_id');
 }

 public function comentario(){
   return $this->morphMany(Comentario::class,'comentable');
 }

 public function empresa()
 {
     return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
 }

 //entradas manuales
 // facturas
 public function getNumeroDocumentoAttribute()
 {

  	return $this->codigo;
 }
 public function getNumeroDocumentoEnlaceAttribute()
 {
 	$attrs = [
 	"href"  => $this->enlace,
 	"class" => "link"];

 	$html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
 	return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
  }
 public function getEnlaceAttribute()
 {
 	return base_url("notas_creditos/ver/".$this->uuid_nota_credito);
 }

 public function getIconoAttribute(){
   return 'fa fa-line-chart';
 }
 public function landing_comments(){
    return $this->morphMany(Comentario::class,'comentable');
  }

  public function present() {
    return new \Flexio\Modulo\NotaCredito\Presenter\NotaCreditoPresenter($this);
  }

}
