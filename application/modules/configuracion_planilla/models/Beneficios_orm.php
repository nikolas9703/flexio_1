 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Beneficios_orm extends Model
{
	protected $table = 'pln_config_beneficios';
	protected $fillable = ['id','nombre','descripcion','empresa_id','modificador_actual','estado_id','creado_por','cuenta_pasivo_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	public function cuenta_pasivo(){
		return $this->hasOne('Cuentas_orm', 'id', 'cuenta_pasivo_id');
	}
	public function acumulados()
	{
		return $this->hasMany('Beneficios_acumulados_orm', 'id_beneficio');
	}
	
	public function deducciones()
	{
		return $this->hasMany('Beneficios_deducciones_orm', 'id_beneficio');
	}
 	public function empresa()
	{
		return $this->hasOne('Empresa_orm', 'id', 'empresa_id');
	}
	
	public static function listarBeneficiosPorEmpresa($empresa_id = NULL)
	{
		
		$beneficiosLista = array();
		
		$beneficios = Capsule::table('pln_config_beneficios as b')->where('empresa_id',$empresa_id)->where('estado_id',1)
		->distinct()
		->get(array('*'));
		if(!empty($beneficios)){
			foreach($beneficios as $beneficio){
				$beneficiosLista[] = array(
						'id'=>$beneficio->id,
						'nombre'=>$beneficio->nombre
				);
			}
		}
		
		return $beneficiosLista;
	}
	
	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
 		 
		/*$query = self::with('rata')
		  ->where('empresa_id',1);*/
 
		$query = self::with('empresa','cuenta_pasivo');
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
		//Si existen variables de orden
		/*if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
		
		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		
		return $query->get();*/
	}
}
