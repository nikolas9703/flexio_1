<?php  
namespace Flexio\Modulo\Planilla\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 

class LiquidacionPlanilla extends Model
{
	protected $table = 'pln_planilla_liquidacion';
	protected $fillable = [ 'planilla_id','liquidacion_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
   
}