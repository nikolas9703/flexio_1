 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Colaboradores\Models\Colaboradores;

class Planilla_colaborador_orm extends Model
{
	protected $table = 'pln_planilla_colaborador';
	protected $fillable = [ 'planilla_id','colaborador_id','estado_ingreso_horas'];
	protected $guarded = ['id'];
	public $timestamps = false;

	public function colaboradores(){
		return $this->belongsToMany('Colaboradores_orm', 'pln_planilla_colaborador', 'planilla_id', 'colaborador_id');
	}
   //  return $this->belongsToMany(Colaboradores::Class, 'pln_planilla_colaborador', 'planilla_id', 'colaborador_id');
  public function colaborador()
  {
      return $this->belongsTo(Colaboradores::Class, 'colaborador_id', 'id');
   }

	public function ingreso_horas()
	{
 	     return $this->hasMany('Ingreso_horas_orm', 'id_planilla_colaborador', 'id');
	}

	public static function  calculando_deduccion_totales($colaborador_id = NULL, $deduccion_id){

		$valor_acumulado = Capsule::table('pln_pagadas_colaborador as cerradas')
		->rightJoin('pln_pagadas_deducciones as ded', 'ded.planilla_pagada_id', '=', 'cerradas.id')
		->where('cerradas.colaborador_id', $colaborador_id)
		->where('ded.deduccion_id',$deduccion_id)
		->sum("ded.descuento");

		return $valor_acumulado;
	}
	public static function cargar_colaboradores($clause = NULL){

		$listar = colaboradores_orm::listar( $clause , NULL, NULL, NULL, NULL );
		if(!empty($listar->toArray())){
			$i = 0;
			foreach ($listar->toArray() AS $i => $row){
				$colaboradores[$i]['id'] 			= $row['id'];
				$colaboradores[$i]['tipo_salario']  = $row['tipo_salario'];
				++$i;
			}
		}
		return $colaboradores;
	}

	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{

		$query = self::with(array( 'colaboradores'));


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
