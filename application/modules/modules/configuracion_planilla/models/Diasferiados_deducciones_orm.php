 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Diasferiados_deducciones_orm extends Model
{
	protected $table = 'pln_config_diasferiados_deducciones';
	protected $fillable = ['id','id_dia_feriado','id_deduccion','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	
	public function deducciones()
	{
		return $this->hasOne('Deducciones_orm', 'id', 'id_deduccion');
	}
	
	public static function DiasFeriados_Deducciones($id_feriado = NULL)
	{
		
		 $data['deducciones'] = array();
		$query = self::with('deducciones');
		$query->where('id_dia_feriado', '=', $id_feriado);
		$lista_acumulados =  $query->get();
 
		$listaAc = $lista_acumulados->toArray();
		if(!empty($listaAc)){
			foreach($listaAc as $valores){
 				 $data['deducciones'][] = array('id'=>$valores['deducciones']['id'], 'nombre'=>$valores['deducciones']['nombre']);
			}
		}
	 	return $data;
	}
 }


