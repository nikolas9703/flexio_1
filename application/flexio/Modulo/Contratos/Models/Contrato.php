<?php
namespace Flexio\Modulo\Contratos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\ContratoMonto as ContratoMonto;
use Flexio\Modulo\Contratos\Models\ContratoTipo as ContratoTipo;
use Flexio\Modulo\Contratos\Models\Adenda as Adenda;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Contratos\Presenter\ContratoPresenter;

class Contrato extends Model
{

  protected $table = 'cont_contratos';


  protected $fillable = ['codigo','cliente_id','empresa_id','fecha_inicio','fecha_final','referencia','centro_id','monto_contrato'];

	protected $guarded = ['id','uuid_contrato'];

  protected $appends      = ['icono','enlace'];


  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_contrato' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

  public function getUuidContratoAttribute($value){
    return strtoupper(bin2hex($value));
  }

  public function getFechaInicioAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
  }

  public function getFechaFinalAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
  }

  public function getClienteNombreAttribute(){
    if (is_null($this->cliente)) {
        return '';
    }
    return $this->cliente->nombre;
  }

 function contrato_montos(){
   return $this->hasMany(ContratoMonto::class);
 }

 function tipo_abono(){
   return $this->hasMany(ContratoTipo::class)->where('tipo','=','abono');
 }

 function tipo_retenido(){
   return $this->hasMany(ContratoTipo::class)->where('tipo','=','retenido');
 }

 function cliente(){
   return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');
 }

 function centro_contable(){
   return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables','centro_id');
 }

 function adenda(){
   return $this->hasMany(Adenda::class);
 }
 function facturas(){
     return $this->morphToMany(FacturaVenta::class,'fac_facturable')->withPivot('empresa_id');
 }

 function facturas_por_cobrar(){
    return $this->morphToMany(FacturaVenta::class,'fac_facturable')->where('fac_facturas.estado','por_cobrar');
 }

 public function anticipos()
 {
     return $this->morphToMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'empezable')->where('estado','aprobado');
 }
 public function anticipos_no_anulados()
 {
     return $this->morphToMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'empezable')->whereIn('estado',['por_aprobar','aprobado']);
 }

 function facturas_habilitadas(){
    return $this->morphToMany(FacturaVenta::class,'fac_facturable')->whereIn('fac_facturas.estado',['cobrado_parcial','cobrado_completo']);
 }

 function facturas_cobro_parcial(){
    return $this->morphToMany(FacturaVenta::class,'fac_facturable')->whereIn('fac_facturas.estado',['cobrado_parcial']);
 }

 function monto_original(){
   return $this->contrato_montos()->sum('monto');
 }

 function monto_adenda(){
   return $this->adenda()->sum('monto_adenda');
 }

 function por_facturar(){
     return $this->facturas()->sum('total');
 }

 function present(){
   return new ContratoPresenter($this);
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
        return base_url("contratos/ver/".$this->uuid_contrato);
    }
    public function getIconoAttribute(){
        return 'fa fa-line-chart';
    }

}
