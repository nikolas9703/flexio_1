<?php
namespace Flexio\Modulo\Cobros_seguros\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro as FacturaSeguro;
use Flexio\Modulo\Transaccion\Models\SysTransaccion;
use Flexio\Modulo\Cobros_seguros\Comando\CobroEstadoManipulado;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Util\GenerarCodigo;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Flexio\Modulo\Polizas\Models\Polizas;

use Flexio\Modulo\Cobros_seguros\Models\CobroFactura;

class Cobros_seguros extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','cliente_id','empresa_id','fecha_pago','estado','monto_pagado','referencia','tipo','formulario','depositable_id', 'depositable_type','empezable_type','empezable_id'];

    protected $table = 'cob_cobros';

    protected $fillable = ['codigo','cliente_id','empresa_id','fecha_pago','estado','monto_pagado','referencia','tipo','formulario','depositable_id', 'depositable_type','empezable_type','empezable_id','num_remesa','num_remesa_entrante'];

    protected $guarded = ['id','uuid_cobro'];
    protected $appends = ['icono','enlace'];
    protected $casts =['monto_pagado'=>'float'];
    protected $deposito = ['banco'=>"Flexio\Modulo\Contabilidad\Models\Cuentas",'caja'=>'Flexio\Modulo\Cajas\Models\Cajas'];

    protected $empezar = ['cliente'=>'Flexio\Modulo\Cliente\Models\Cliente','polizas'=>'Flexio\Modulo\Polizas\Models\Polizas','factura'=>'Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro' ];


    public function __construct(array $attributes = array()){
      $session = new FlexioSession;
      $this->setRawAttributes(array_merge($this->attributes, array('uuid_cobro' => Capsule::raw("ORDER_UUID(uuid())"),'empresa_id'=> $session->empresaId())), true);
      parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        static::updating(function($cobro) {
            $cambio = $cobro->getDirty();
            if(isset($cambio['estado']) && $cambio['estado'] =='anulado'){
              $manipular = new CobroEstadoManipulado;
              $manipular->anulado($cobro);
            }
            return $cobro;
        });
    }

    public function getUuidCobroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    function setCodigoAttribute($value){
        $year = Carbon::now()->format('y');
        return $this->attributes['codigo'] = GenerarCodigo::setCodigo('PAY'.$year, $value);
    }

    public function getFechaPagoAttribute($date){
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function setDepositableTypeAttribute($value){
        return $this->attributes['depositable_type'] = $this->deposito[$value];
    }

    public function getDepositableTypeAttribute($value){
        $tipos = array_flip($this->deposito);
        if(array_key_exists($value,$tipos)){
            return  $tipos[$value];
        }

    }

    public function setEmpezableTypeAttribute($value){
		$debug = debug_backtrace(false);
        //echo "f llmadora ".$debug[1]['function'];
		//print_r($debug[1]);
        return $this->attributes['empezable_type'] = $this->empezar[$value];
    }

    public function getEmpezableTypeAttribute($value){

        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
          'cliente' => \Flexio\Modulo\Cliente\Models\Cliente::class,
          'polizas' => \Flexio\Modulo\Polizas\Models\Polizas::class,
          'factura' => \Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro::class,
      ]);
        $tipos = array_flip($this->empezar);
        if(array_key_exists($value,$tipos)){
            return array_get($tipos, $value, $value);
        }
    }

    /*function getActualClassNameForMorph($class){
        dd($class);
    }*/
    public function getClienteNombreAttribute() {
        if (is_null($this->cliente)) {
            return '';
        }
        return $this->cliente->nombre;
    }

    public function setFechaPagoAttribute($date){
      if (Carbon::createFromFormat('d/m/Y', $date, 'America/Panama') !== false) {
          return $this->attributes['fecha_pago'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
      }
      return $this->attributes['fecha_pago'] =  Carbon::now();
    }

    /*public function factura_cobros()
    {
      return $this->belongsToMany(FacturaVenta::class,'cob_cobro_facturas','cobro_id','factura_id')->withPivot('monto_pagado','empresa_id')->withTimestamps();
  }*/


    //esta es la relacion para buscar los cobros q se le hicieron a la factura
    public function factura_cobros(){
        return $this->belongsToMany(FacturaSeguro::class,'cob_cobro_facturas','cobro_id','cobrable_id')->where('cobrable_type','Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro')->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }
    /// relacion de cobrables
    public function cobros_facturas(){
		return $this->hasMany(CobroFactura::class,'cobro_id');
    }

    public function polizas(){
        return $this->belongsToMany(Polizas::class,'id','empezable_id')->select('numero as numero_poliza');
    }

    //no va
/*    public function total_cobrado(){
      return $this->cobros_facturas->sum('monto_pagado');
  }*/

    public function metodo_cobro()
    {
      return $this->hasMany(MetodoCobro::class,'cobro_id');
    }
	
	public function empezablePoliza()
    {
		return $this->belongsTo('Flexio\Modulo\Polizas\Models\Polizas', 'empezable_id');
     // return $this->belongsTo(Polizas::class,'empezable_id');
    }

    public function catalogo_estado()
    {
        return $this->belongsTo('Flexio\Modulo\Catalogos\Models\Catalogo','estado','etiqueta')->where('tipo','=','estado')->where('modulo','cobro');
    }


    public function cliente()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente', 'cliente_id');
    }

    function empresa(){
       return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa','empresa_id');
    }

    function sistema_transaccion(){
        return $this->morphMany(SysTransaccion::class,'linkable');
    }

    public function getNumeroDocumentoAttribute()
    {

    	return $this->codigo;
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
    	return base_url("cobros_seguros/ver/".$this->uuid_cobro);
    }

    public static function registrar(){
        return new static;
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function cobros_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    public function empezable()
    {
       return $this->morphTo();
    }
    //functiones para el landing_page
   public function getIconoAttribute(){
           return 'fa fa-line-chart';
    }

   public function present() {
       return new \Flexio\Modulo\Cobros_seguros\Presenter\CobroPresenter($this);
   }

   public function Facturas(){
        return $this->hasMany(CobroFactura::class, 'cobro_id','id');
   }    

  }
