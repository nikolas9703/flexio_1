 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Ingreso_horas_dias_orm extends Model
{
	protected $table = 'pln_planilla_ingresohoras_dias';
	protected $fillable = ['ingreso_horas_id','fecha','comentario','horas','fecha_ingreso'];
	protected $guarded = ['id'];
 	public $timestamps = false;



	public static function totalHoras($id_planilla_colaborador = NULL ){

		$result = Capsule::table('pln_planilla_ingresohoras AS h')
		->leftJoin('pln_planilla_ingresohoras_dias AS hd', 'hd.ingreso_horas_id', '=', 'h.id')
 		->where('h.id_planilla_colaborador', $id_planilla_colaborador)
		->get(array(Capsule::raw('SUM(hd.horas) as total')));
 		return !empty($result[0]->total)?$result[0]->total:0;
	}

 }
