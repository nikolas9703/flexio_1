<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Comisiones_orm extends Model
{
	protected $table = 'com_comisiones';
	protected $fillable = ['uuid_comision','numero', 'centro_contable_id','area_negocio_id','uuid_cuenta_activo', 'metodo_pago','fecha_pago','empresa_id','fecha_creacion','estado_id','activo','descripcion','fecha_programada_pago','cuenta_id_activo'];
	protected $guarded = ['id'];
	public $timestamps = true;

    public function getUuidComisionAttribute($value) {
        return strtoupper(bin2hex($value));
    }
	public function setUuidComisionesAttribute($value)
	{
		$this->attributes['uuid_comisiones'] = Capsule::raw("ORDER_UUID(uuid())");
	}
	public function setFechaProgramadaPagoAttribute($date)
	{
			return $this->attributes['fecha_programada_pago'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
	}

	public function getFechaProgramadaPagoAttribute($date)
	{
			return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
	}
	public function acumulados()
	{
		return $this->hasMany('Comision_acumulados_orm', 'comision_id');
	}

	public function deducciones()
	{
		return $this->hasMany('Comision_deducciones_orm', 'comision_id');
	}

	public function colaboradores()
	{
		return $this->hasMany('Comision_colaborador_orm', 'comision_id');
	}

	public function centro_contable()
	{
		return $this->hasOne('Centros_orm', 'id', 'centro_contable_id');
	}
	public function estado()
	{
		return $this->hasOne('Estado_comision_orm', 'id_cat', 'estado_id');
	}
	public function area_negocio()
	{
		return $this->hasOne('Departamentos_orm', 'id', 'area_negocio_id');
	}

	public function empresa()
	{
		return $this->hasOne('Empresa_orm', 'id', 'empresa_id');
	}

	public static function lista_por_empresa($empresa_id=NULL){

		if($empresa_id==NULL){
			return false;
		}

		return self::where('empresa_id', $empresa_id)->get()->toArray();
	}

	public static function cuentas_gastos(){
		return Capsule::table('contab_tipo_cuentas AS tipo')
		->leftJoin('contab_cuentas AS cuen', 'cuen.tipo_cuenta_id', '=', 'tipo.id')
		->where('tipo.nombre', '=', 'Gastos')
		->get(array('cuen.*'));
	}
	public static function cuentas_activos($empresa_id = NULL){
		return Capsule::table('contab_tipo_cuentas AS tipo')
		->leftJoin('contab_cuentas AS cuen', 'cuen.tipo_cuenta_id', '=', 'tipo.id')
		->where('tipo.nombre', '=', 'Activo')
		->where('cuen.empresa_id', '=', $empresa_id)
		->get(array('cuen.*'));
	}
	public function colaborador_informacion(){
		return $this->belongsToMany('Colaboradores_orm', 'com_colaboradores', 'comision_id', 'colaborador_id');
	}

  	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */

	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{


   		//,'colaborador_info'
		$query = self::with(array('colaboradores','empresa','centro_contable','estado','area_negocio','colaborador_informacion' => function($query) use($sidx, $sord){
 			if(!empty($sidx) && preg_match("/nombre/i", $sidx)){
				$query->orderBy("id", $sord);
			}
		}, 'estado'));


 			//Si existen variables de limite
			if($clause!=NULL && !empty($clause) && is_array($clause))
			{

				foreach($clause AS $field => $value)
				{

					if($field == "colaborador_id"){
						continue;
					}

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



 			//Si existen variables de limite
			if($limit!=NULL) $query->skip($start)->take($limit);


  			return $query->get();


	}


}
