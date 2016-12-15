<?php
namespace Flexio\Modulo\Comisiones\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Acumulados;


class ComisionAcumulado extends Model
{
	protected $table = 'com_acumulados';
	protected $fillable = [ 'acumulado_id','comision_id'];
	protected $guarded = ['id'];
	public $timestamps = false;


  public function acumulado_info()
  {
      return $this->belongsTo(Acumulados::Class, 'acumulado_id', 'id');
   }
}
