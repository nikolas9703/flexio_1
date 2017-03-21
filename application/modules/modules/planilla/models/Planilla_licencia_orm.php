 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Planilla_licencia_orm extends Model
{
	protected $table = 'pln_planilla_licencia';
	protected $fillable = [ 'planilla_id','licencia_id','estado_ingreso_horas'];
	protected $guarded = ['id'];
	public $timestamps = false;
   
}