<?php
namespace Flexio\Modulo\Contratos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\AdendaMonto as AdendaMonto;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;

class Adenda extends Model
{
  protected $table = 'cont_adendas_contratos';

  protected $fillable = ['codigo','cliente_id','empresa_id','fecha','contrato_id','referencia','usuario_id','monto_adenda','comentario','monto_acumulado'];

  protected $guarded = ['id','uuid_adenda'];
  protected $appends =['icono','enlace'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_adenda' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

  public function getUuidAdendaAttribute($value){
    return strtoupper(bin2hex($value));
  }

  public function getFechaAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
  }

  function adenda_montos(){
    return $this->hasMany(AdendaMonto::class);
  }

  public function usuario(){
    return $this->belongsTo("Usuario_orm",'usuario_id');
  }

  public function contrato(){
    return $this->belongsTo(Contrato::class,'contrato_id');
  }

  public function monto_contrato(){
    return  $this->contrato()->sum('monto_contrato');
  }
    public function comentario(){
        return $this->morphMany(Comentario::class,'comentable');
    }

    //scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeUuid($query, $uuid_adenda)
    {
        return $query->where("uuid_adenda", hex2bin($uuid_adenda));
    }

    public function scopeDeCodigo($query, $codigo)
    {
        return $query->where("codigo", $codigo);
    }

    ///functiones del landing_page
    public function getIconoAttribute(){
      return 'fa fa-line-chart';
    }
    public function landing_comments(){
        return $this->morphMany(Comentario::class,'comentable');
    }

     public function getEnlaceAttribute()
    {
         return base_url("contratos/editar_adenda/".$this->uuid_adenda);
    }

}
