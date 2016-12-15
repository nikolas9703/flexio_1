<?php
namespace Flexio\Modulo\Devoluciones\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;
use Flexio\Modulo\Devoluciones\Models\DevolucionCatalogo as DevolucionCatalogo;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Flexio\Modulo\Entradas\Models\Entradas as Entradas;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Devolucion extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','cliente_id','empresa_id','fecha_devolucion','fecha_factura','estado','created_by','comentario','subtotal','impuestos','total','bodega_id','centro_contable_id','referencia','razon','factura_id'];

    protected $table = 'dev_devoluciones';

    protected $fillable = ['codigo','cliente_id','empresa_id','fecha_devolucion','fecha_factura','estado','created_by','comentario','subtotal','impuestos','total','bodega_id','centro_contable_id','referencia','razon','factura_id'];

    protected $guarded = ['id','uuid_devolucion'];

    protected $appends      = ['icono','enlace'];

    public function __construct(array $attributes = array()){
      $this->setRawAttributes(array_merge($this->attributes, array('uuid_devolucion' => Capsule::raw("ORDER_UUID(uuid())"))), true);
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

    public function getUuidDevolucionAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getFechaFacturaAttribute($date){
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

  	public function getFechaDevolucionAttribute($date){
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function setFechaFacturaAttribute($date){
  		return  $this->attributes['fecha_factura'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

  	public function setFechaDevolucionAttribute($date){
      return $this->attributes['fecha_devolucion'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function getVendedorNombreAttribute() {
        if (is_null($this->vendedor)) {
            return '';
        }
        return $this->vendedor->nombre . " " . $this->vendedor->apellido;
    }

    public function getClienteNombreAttribute() {
        if (is_null($this->cliente)) {
            return '';
        }
        return $this->cliente->nombre;
    }

    public function bodega()
    {
        return $this->belongsTo('Bodegas_orm', 'bodega_id', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente', 'cliente_id');
    }

    public function items(){
      return $this->morphMany(LineItem::class,'tipoable');
  	}

    public function vendedor(){
  		return $this->belongsTo('Flexio\Modulo\Usuarios\Models\Usuarios','created_by');
  	}

  	public function etapa_catalogo(){
  		return $this->belongsTo(DevolucionCatalogo::class,'estado','etiqueta')->where('tipo','=','etapa');
  	}
    
    public function razon_catalogo(){
  		return $this->belongsTo(DevolucionCatalogo::class,'razon','etiqueta')->where('tipo','=','razon');
  	}

    function empresa(){
       return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa','empresa_id');
    }

    function facturas(){
      return $this->belongsTo(FacturaVenta::class,'factura_id');
    }

    public function entrada(){
      return $this->morphMany(Entradas::class, 'operacion');
    }

    //Mostrar Comentarios
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("devoluciones/ver/".$this->uuid_devolucion);
    }
    public function getIconoAttribute(){
        return 'fa fa-line-chart';
    }

    public function present() {
        return new \Flexio\Modulo\Devoluciones\Presenter\DevolucionPresenter($this);
    }

}
