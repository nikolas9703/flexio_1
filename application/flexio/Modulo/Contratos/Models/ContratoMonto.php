<?php
namespace Flexio\Modulo\Contratos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;

class ContratoMonto extends Model
{
  protected $table = 'cont_contratos_montos';

  protected $fillable = ['cuenta_id','descripcion','monto','contrato_id','empresa_id'];

	protected $guarded = ['id'];

  function contrato(){
    return $this->belongsTo(Contrato::class,'contrato_id');
  }

}
