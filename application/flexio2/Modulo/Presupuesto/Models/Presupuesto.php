<?php

namespace Flexio\Modulo\Presupuesto\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\GenerarCodigo;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;

class Presupuesto extends Model
{
	protected $table = 'pres_presupuesto';
	protected $fillable = ['codigo','nombre','empresa_id','fecha_inicio','centro_contable_id','cantidad_meses','tipo','usuario_id'];
	protected $guarded = ['id','uuid_presupuesto'];
	protected $formatoTipo =['periodo'=>'Por periodo','avance'=>'Por avance'];
  protected $appends = ['inicio'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_presupuesto' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }


  function setCodigoAttribute($value){
      return $this->attributes['codigo'] = GenerarCodigo::setCodigo('PPTO', $value);
  }

  public function getCreatedAtAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
  }

  public function getInicioAttribute(){
    return Carbon::createFromFormat('d/m/Y', $this->fecha_inicio)->format('m-Y');
  }

  public function getUuidPresupuestoAttribute($value){
    return strtoupper(bin2hex($value));
  }

  public function getFechaInicioAttribute($value){
	  return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
  }

  public function getFechaAttribute(){

    return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['fecha_inicio'])->format('Y-m-d');
  }

  public function getTipoFormatoAttribute(){
	  return $this->formatoTipo[$this->tipo];
  }

  public function centro_contable(){
	  return $this->belongsTo(CentrosContables::class,'centro_contable_id');
  }

  public function lista_presupuesto()
  {
	   return $this->hasMany(CentroCuentaPresupuesto::class,'presupuesto_id');
  }

  public function historial(){
	  return $this->hasMany(PresupuestoHistorial::class,'presupuesto_id');
  }

  public static function registrar(){
    return new static;
  }

  public function present() {
    return new \Flexio\Modulo\Presupuesto\Presenter\PresupuestoPresenter($this);
  }
}
