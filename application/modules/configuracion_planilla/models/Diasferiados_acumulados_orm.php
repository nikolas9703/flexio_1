 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Diasferiados_acumulados_orm extends Model
{
	protected $table = 'pln_config_diasferiados_acumulados';
	protected $fillable = ['id','id_dia_feriado','id_acumulado','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	
	public function acumulados()
	{
		return $this->hasOne('Acumulados_orm', 'id', 'id_acumulado');
	}
	
	public function deducciones()
	{
		return $this->hasOne('Deducciones_orm', 'id', 'id_deduccion');
	}
	
	public static function DiasFeriados_Acumulados($id_feriado = NULL)
	{
		
		$data['acumulados'] = array();
		$query = self::with('acumulados');
		$query->where('id_dia_feriado', '=', $id_feriado);
		$lista_acumulados =  $query->get();
 
		$listaAc = $lista_acumulados->toArray();
		if(!empty($listaAc)){
			foreach($listaAc as $valores){
 				 $data['acumulados'][] = array('id'=>$valores['acumulados']['id'], 'nombre'=>$valores['acumulados']['nombre']);
			}
		}
	 	return $data;
	}
 }

