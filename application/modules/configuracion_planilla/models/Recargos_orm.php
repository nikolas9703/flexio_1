 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Recargos_orm extends Model
{
	protected $table = 'pln_config_recargos';
	protected $fillable = [ 'nombre','descripcion','porcentaje_hora','estado_id','empresa_id','creado_por'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	public static function listarRecargosPorEmpresa($empresa_id = NULL)
	{
		$recargosLista = array();
		
		$recargos = Capsule::table('pln_config_recargos as r')->where('empresa_id',$empresa_id)->where('estado_id',1)
		->distinct()
		->get(array('*'));
		if(!empty($recargos)){
			foreach($recargos as $recargo){
				$recargosLista[] = array(
						'id'=>$recargo->id,
						'nombre'=>$recargo->nombre
				);
			}
		}
		return $recargosLista;
	}
	
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
	
		$query = self::with(array());
	
		 
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
	
			foreach($clause AS $field => $value)
			{
 				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}
	
				//verificar si valor es array
				if(is_array($value)){
					$query->where($field, $value[0], $value[1]);
				}else{
					$query->where($field, '=', $value);
				}
			}
		}
	
		if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();
	}
 }
