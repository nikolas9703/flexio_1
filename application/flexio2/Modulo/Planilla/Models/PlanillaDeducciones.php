<?php
namespace Flexio\Modulo\Planilla\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Deducciones;


class PlanillaDeducciones extends Model
{
	protected $table = 'pln_planilla_deducciones';
	protected $fillable = [ 'deduccion_id','planilla_id'];
	protected $guarded = ['id'];
	public $timestamps = false;


  public function deduccion_info()
  {
      return $this->belongsTo(Deducciones::Class, 'deduccion_id', 'id');
   }
}
