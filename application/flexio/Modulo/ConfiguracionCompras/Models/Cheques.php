<?php
namespace Flexio\Modulo\ConfiguracionCompras\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Cheques extends Model
{

    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['numero','monto','chequera_id','pago_id','fecha_cheque','empresa_id','updated_at','created_at','estado_id'];

    protected $table = 'che_cheques';

    protected $fillable = ['numero','monto','chequera_id','pago_id','fecha_cheque','empresa_id','updated_at','created_at','estado_id'];

    protected $guarded = ['id','uuid_cheque'];

    protected $appends = ['icono','codigo','enlace'];

    public function __construct(array $attributes = array()){
      $this->setRawAttributes(array_merge($this->attributes, array('uuid_cheque' => Capsule::raw("ORDER_UUID(uuid())"))), true);
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
    //Mutators
    public function getUuidChequeAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidAttribute()
    {
        return $this->uuid_cheque;
    }

    public function getEnlaceAttribute()
    {
        return base_url("cheques/ver/".$this->uuid_cheque);
    }

    public function getFechaChequeAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }

    public function setFechaAttribute($date){
  		return  $this->attributes['fecha_cheque'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }
    
    public function getImprimibleAttribute()
    {
        return $this->estado_cheque->valor == "por_imprimir";
    }
    
    public function getAnulableAttribute()
    {
        return $this->estado_cheque->valor != "anulado";
    }

    //Relationships
    public function pago(){
        return $this->belongsTo('Flexio\Modulo\Pagos\Models\Pagos', 'pago_id', 'id');
    }

    public function empresa(){
        return $this->belongsTo('Empresa_orm','empresa_id','id');
    }

    public function chequera()
    {
        return $this->belongsTo('Flexio\Modulo\ConfiguracionCompras\Models\Chequera','chequera_id','id');
    }

    public function estado_cheque(){
        return $this->belongsTo(ChequesCatalogo::class,'estado_id');
    }

    public static function getCheques($clause = array(), $vista=null)
    {
      if(!empty($clause)){
        $cheques = self::where(function($query) use($clause, $vista){
          $query->where('empresa_id','=',$clause['empresa_id']);
          if($vista == 'registrar_pago_cobro'){
            $query->whereIn('estado',array('por_aprobar'));
        }elseif($vista =='crear'){
            $query->where('estado','=','por_aprobar');
        }else{
              $query->whereNotIn('estado',array('anulada'));
          }
        })->get(array('uuid_cheque','codigo','cliente_id'));
        return $cheques;
      }elseif(empty($clause)){
        return array();
      }
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function cheques_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getIconoAttribute() {
        return 'fa fa-shopping-cart';
    }
    public function getCodigoAttribute() {
        return $this->numero;
    }


}
