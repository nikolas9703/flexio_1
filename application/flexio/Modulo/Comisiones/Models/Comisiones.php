<?php
namespace Flexio\Modulo\Comisiones\Models;
use Illuminate\Database\Capsule\Manager as Capsule;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comisiones\Models\ComisionColaborador;
use Flexio\Modulo\Comisiones\Models\ComisionDeduccion;
use Flexio\Modulo\Comisiones\Models\ComisionAcumulado;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\ConfiguracionRrhh\Models\RrhhAreas;
use Flexio\Modulo\Contabilidad\Models\Cuentas;

class Comisiones extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['numero', 'centro_contable_id','area_negocio_id','uuid_cuenta_activo','cuenta_id_activo', 'metodo_pago','fecha_pago','empresa_id','fecha_creacion','estado_id','activo','descripcion','fecha_programada_pago'];

    protected $table    = 'com_comisiones';
    protected $fillable = ['uuid_comision','numero', 'centro_contable_id','area_negocio_id','uuid_cuenta_activo', 'metodo_pago','fecha_pago','empresa_id','fecha_creacion','estado_id','activo','descripcion','fecha_programada_pago','cuenta_id_activo','total_colaboradores','pagadas_colaboradores'];
    protected $guarded  = ['id'];
    public $timestamps  = true;
    protected $appends  = ['icono','codigo','enlace','monto_total','monto_neto'];
//'monto_neto','monto_deducido',

     public function __construct(array $attributes = array()) {
         $this->setRawAttributes(array_merge($this->attributes, array(
             'uuid_comision' => Capsule::raw("ORDER_UUID(uuid())")
                 )), true);
         parent::__construct($attributes);
     }
     public function colaboradores()
   	{
   		return $this->hasMany(ComisionColaborador::Class, 'comision_id');
   	}
     public function setFechaProgramadaPagoAttribute($date)
     {
         return $this->attributes['fecha_programada_pago'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
     }

     public function getFechaProgramadaPagoAttribute($date)
     {
         return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
     }

    public static function boot() {
        parent::boot();
    }
    public function getUuidAttribute() {
        return $this->uuid_comision;
    }
    public function acumulados()
  	{
  		return $this->hasMany(ComisionAcumulado::class, 'comision_id');
  	}

    public function deducciones(){
         return $this->hasMany(ComisionDeduccion::class, 'comision_id');
   }


   public function getMontoTotalAttribute() {

       $monto_total = $this->colaboradores()->sum('monto_total');
        return (float) $monto_total;
   }
  public function getMontoNetoAttribute() {
         $monto_neto = $this->colaboradores->sum('monto_neto');
        return (float) $monto_neto;
   }
/*
   public function getMontoDeducidoAttribute() {
      $monto_deducido = $this->colaboradores()->sum('monto_deducido');
      return (float) $monto_deducido;
   }*/

   public function getUuidComisionAttribute($value){
         return strtoupper(bin2hex($value));
   }

   public function getUuidCuentaActivoAttribute($value)
     {
       return strtoupper(bin2hex($value));
     }

     public function cuenta_info()
     {
          return $this->belongsTo(Cuentas::Class, 'cuenta_id_activo', 'id');

     }
     public function comisiones_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }


    public function getNumeroDocumentoEnlaceAttribute()
    {
      $attrs = [
      "href"  => $this->enlace,
      "class" => "link"
          ];

      $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
      return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero)->getSalida();
    }

    public function getEnlaceAttribute()
    {
        return base_url("comisiones/ver/".$this->uuid_comision);
    }
    public function getIconoAttribute(){
        return 'fa fa-institution';
    }
    public function getCodigoAttribute(){
      //  dd($this->uuid_comision);
        return $this->numero;
    }
    public function centro_contable()
    {
      return $this->hasOne(CentrosContables::Class, 'id', 'centro_contable_id');
    }
    public function estado()
    {
      return $this->hasOne(ComisionEstado::Class, 'id_cat', 'estado_id');
    }
    public function area_negocio()
    {
      return $this->hasOne(RrhhAreas::Class, 'id', 'area_negocio_id');
    }

    public function empresa()
    {
      return $this->hasOne(Empresa::Class, 'id', 'empresa_id');
    }
}
