 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Beneficios_deducciones_orm extends Model
{
	protected $table = 'pln_config_beneficios_deducciones';
	protected $fillable = ['id','id_beneficio','id_deduccion','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	
	public function deducciones()
	{
		return $this->hasOne('Deducciones_orm', 'id', 'id_deduccion');
	}
	
	public static function Beneficios_Deducciones($id_beneficio = NULL)
	{
		
		 $data['deducciones'] = array();
		$query = self::with('deducciones');
		$query->where('id_beneficio', '=', $id_beneficio);
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


