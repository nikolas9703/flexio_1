  <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Diasferiados_orm extends Model
{
	protected $table = 'pln_config_dias_feriados';
	protected $fillable = ['id','nombre','descripcion','fecha_oficial','horas_no_laboradas','empresa_id','estado_id','creado_por','cuenta_pasivo_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
 	 public function empresa()
	{
		return $this->hasOne('Empresa_orm', 'id', 'empresa_id');
	}
	
	public function cuenta_pasivo(){
		return $this->hasOne('Cuentas_orm', 'id', 'cuenta_pasivo_id');
	}
	
  	public function acumulados()
	{
		return $this->hasMany('Diasferiados_acumulados_orm', 'id_dia_feriado');
	}
	
	public function deducciones()
	{
		return $this->hasMany('Diasferiados_deducciones_orm', 'id_dia_feriado');
	}
	
	public static function AcumuladosByDia($id_feriado = NULL)
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
	
	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	public static function listar($empresa_id = NULL, $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
 		 
		$previousYear = date("Y") - 1;
		$desde =  $previousYear.'-01-01';
  		/*$query = self::with('rata')
		  ->where('empresa_id',1);*/
 
		$query = self::with('empresa','cuenta_pasivo' );
		
		$query->where('semilla', '=', 0);
		$query->where('empresa_id', '=', $empresa_id);
		//$query->where('fecha_oficial', '>=', $desde);
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
		
		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		
		return $query->get();
	}
	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	public static function duplicar_dias($id_usuario = NULL, $empresa_id = NULL)
	{
  		//$empresa_id = 1;	
 		$CurrentYear = date("Y") ;
 		$lastYear = date("Y") - 1 ;
 		
		$desde =  $CurrentYear.'-01-01';
		$hasta =  $CurrentYear.'-12-31';
		 
		try {
		
			Capsule::beginTransaction();
		
			$resultado = Diasferiados_orm::where('fecha_oficial','>=', $desde )
			->where("fecha_oficial", "<=", $hasta)
			->where("empresa_id", "=", $empresa_id)
			->delete();
			
			$desde_last =  $lastYear.'-01-01';
			$hasta_last =  $lastYear.'-12-31';
	
			$dias_feriados = new Diasferiados_orm;
			$registros = $dias_feriados->where("fecha_oficial", ">=", $desde_last)
						->where("fecha_oficial", "<=", $hasta_last)
						->where("estado", "=", 'Activado')
						->where("empresa_id", "=", $empresa_id)
						->orderBy('fecha_oficial', 'ASC');
			$lista_dias =  $registros->get();
			if($lista_dias->count()>0)
			{
	 			foreach( $lista_dias->toArray() as $row){
 					$fecha_anterior = explode("-", $row['fecha_oficial']);
					$fecha_nueva = $CurrentYear.'-'.$fecha_anterior[1].'-'.$fecha_anterior[2];
					$valores_dia_feriado = array(
							'nombre'=> $row['nombre'],
							'descripcion'=> $row['descripcion'],
							'fecha_oficial' => $fecha_nueva,
							'horas_id' => $row['horas_id'],
							'empresa_id' => $empresa_id,
							'estado' => 'Activado',
							'creado_por' =>  $id_usuario
					);
					
					$dias_feriados->insert($valores_dia_feriado);
					
				}
				$resultado = true;
			}	
			else{ 
				$resultado =  false;
			}
			Capsule::commit();
			
			
		
		} catch (\Exception $e){
		
			Capsule::rollback();
			$resultado = false;
			//Handle anything else....
 		}
		
		return $resultado;
	}
}
 