<?php
namespace Flexio\Modulo\Contratos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;

class ContratoTipo extends Model
{
  protected $table = 'cont_contratos_tipos';

  protected $fillable = ['cuenta_id','monto','contrato_id','empresa_id','tipo','porcentaje'];

	protected $guarded = ['id'];


  function contrato(){
    return $this->belongsTo(Contrato::class,'contrato_id');
  }


}
