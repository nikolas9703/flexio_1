<?php
namespace Flexio\Modulo\Planilla\Models\Abiertas;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Acumulados;


class PlanillaAcumulados extends Model
{
	protected $table = 'pln_planilla_acumulados';
	protected $fillable = [ 'acumulado_id','planilla_id'];
	protected $guarded = ['id'];
	public $timestamps = false;


  public function acumulado_info()
  {
      return $this->belongsTo(Acumulados::Class, 'acumulado_id', 'id');
   }
}
