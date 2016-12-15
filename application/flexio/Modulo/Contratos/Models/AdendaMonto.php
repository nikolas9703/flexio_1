<?php
namespace Flexio\Modulo\Contratos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\Adenda as Adenda;
use Carbon\Carbon as Carbon;

class AdendaMonto extends Model
{
  protected $table = 'cont_adendas_montos';

  protected $fillable = ['empresa_id','monto','descripcion', 'cuenta_id'];

  protected $guarded = ['id','adenda_id'];

  public function adenda(){
    return $this->belongsTo(Adenda::class,'adenda_id');
  }
}
