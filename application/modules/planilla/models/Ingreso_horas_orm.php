 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Ingreso_horas_orm extends Model
{
	protected $table = 'pln_planilla_ingresohoras';
	protected $fillable = ['centro_contable_id','recargo_id','cuenta_costo_id','beneficio_id','cuenta_gasto_id','fecha_creacion','id_planilla_colaborador','empresa_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
    protected $appends = ['cantidad_horas'];

	public function empresa()
	{
		return $this->hasOne('Empresa_orm', 'id', 'empresa_id');
	}

	public function dias()
	{
		return $this->hasMany('Ingreso_horas_dias_orm', 'ingreso_horas_id');
	}

	public function centro_contable()
	{
		return $this->hasOne('Centros_orm', 'id', 'centro_contable_id');
	}

	public function recargo()
	{
		return $this->hasOne('Recargos_orm', 'id', 'recargo_id');
	}

	public function cuenta_costo()
	{
		return $this->hasOne('Cuentas_orm', 'id', 'cuenta_costo_id');
	}

	public function beneficio()
	{
		return $this->hasOne('Beneficios_orm', 'id', 'beneficio_id');
	}
	public function cuenta_gasto()
	{
		return $this->hasOne('Cuentas_orm', 'id', 'cuenta_gasto_id');
	}

	public function horas_dias(){
 		return $this->hasMany('Ingreso_horas_dias_orm', 'ingreso_horas_id');
 	}
  	//$salario_por_recargo = $suma_horas_x_recargo*($ingreso['recargo']['porcentaje_hora']*$rata_hora);
//$rata = $ingreso['recargo']['porcentaje_hora']*$rata_hora;
  public function getCantidadHorasAttribute() {
      $total_horas = 0;
      $total_horas = $this->horas_dias()->sum('horas');
       return $total_horas;
  }
  /* public function getPorcentajeRecargoAttribute() {

        $porcentajeRecargo= $this->recargo()->porcentaje_hora;
         return $porcentajeRecargo;
    }*/

	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $fechas=array())
	{
 		$fecha1 = isset($fechas['inicial'])?$fechas['inicial']:'0000-00-00';
		$fecha2 = isset($fechas['terminacion'])?$fechas['terminacion']:'0000-00-00';


		$query = self::with(array('empresa','centro_contable','recargo' ,'cuenta_costo','beneficio','cuenta_gasto','horas_dias' => function($query) use($fecha1, $fecha2){
 				$query->where('fecha','>=', $fecha1);
 				$query->where('fecha','<=', $fecha2);
 		} ));

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
