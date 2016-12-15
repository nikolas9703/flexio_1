<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Acreedores\Models\Acreedores as Acreedores;

class Descuentos_orm extends Model
{
	protected $table = 'desc_descuentos';
	protected $fillable = ['colaborador_id', 'empresa_id', 'plan_contable_id', 'tipo_descuento_id', 'acreedor_id', 'ciclo_id', 'monto_inicial','monto_adeudado', 'monto_ciclo', 'porcentaje_capacidad', 'descuento_diciembre', 'carta_descuento', 'fecha_inicio', 'detalle', 'archivo_ruta', 'archivo_nombre', 'estado_id', 'creado_por', 'inicial', 'anio', 'secuencial', 'uuid_descuento', 'no_referencia', 'codigo'];
	protected $guarded = ['id'];

	public function plan_contable(){
		return $this->hasOne('Cuentas_orm', 'id', 'plan_contable_id');
	}

	public function tipo_descuento(){
		return $this->hasOne('Estado_orm', 'id_cat', 'tipo_descuento_id');
	}

        public function cat_descuentos(){

                return $this->hasOne('Descuentos_orm', 'tipo_descuento_id', 'id_cat');

        }

	public function acreedor(){
		return $this->hasOne('Acreedores_orm', 'id', 'acredor_id');
	}

        public function acreedores(){

            return $this->hasOne(Acreedores::class, 'id', 'acreedor_id');

        }

        public function pagadas_descuentos(){

            return $this->hasMany('Pagadas_descuentos_orm', 'descuento_id', 'id');

        }

        public static function estado_cuenta($clause){



        }

        public static function pagadas_descuentos2($fecha_creacion){

            if($fecha_creacion==NULL){
			return false;
		}

		$query = self::with(array('pagadas_descuentos'))->where('fecha_creacion', '=', $fecha_creacion);
                return $query->get();
        }

        public function colaborador(){

               return $this->belongsTo('Colaboradores_orm', 'colaborador_id', 'id');

        }

	public function ciclo(){
		return $this->hasOne('Catalogo_orm', 'id_cat', 'ciclo_id');
	}

        public function estado(){
		return $this->hasOne('Estado_orm', 'id_cat', 'estado_id');
	}

	/**
	 * Retorna listado de Colaboradores
	 * De la Empresa Actual
	 */
	public static function lista($empresa_id=NULL){

		if($empresa_id==NULL){
			return false;
		}

		return self::where('empresa_id', $empresa_id)->get()->toArray();
	}

        public function getUuidDescuentoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

	/**
	 * Listar descuentos
	 *
	 * @return [array]
	 */
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
		$colaborador_id = !empty($clause["colaborador"]) ? $clause["colaborador"] : array();

		$query = self::with(array('tipo_descuento', 'ciclo', 'estado', 'plan_contable' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}));

		if(!empty($colaborador_id)){
			$query->where("colaborador_id", $colaborador_id);
		}

		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "colaborador"){
					continue;
				}

				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}

				//verificar si valor es array
				if(is_array($value)){

					if(preg_match("/(fecha)/i", $field)){
						$query->where($field, $value[0], $value[1]);
					}else{
						//$query->whereIn("id", $value);
					}
	                if($field == "cedula"){
		                $query->whereHas("colaborador", function($q) use ($value){
		                	$q->where("cedula", "LIKE", "%$value[0]%");
						});
	                }elseif($field == "colaborador_nombre"){

                        $query->whereHas("colaborador", function($q) use ($value){

						$field = Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, ''))");
							$q->where($field, "LIKE", "%$value[0]%");
                    	});
                	}

				}elseif(!is_array($value)){
					$query->where($field, '=', $value);
				}

			}
		}

		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
				//$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();


	}

   public static function descuentos_colaborador($clause=array()){
        $query = self::with(array('tipo_descuento', 'ciclo' => function($query){
         }));
        $query->where($clause);


        return $query->get();


        }

public static function reporte($clause=array())
	{

		$query = self::with(array('pagadas_descuentos', 'ciclo', 'colaborador', 'tipo_descuento' => function($query){
		}));
		foreach($clause AS $field => $value)
			{

                //Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}
                if(is_array($value)){

					if(preg_match("/(fecha)/i", $field)){
						$query->where($field, $value['0'], $value['1']);

					}else{
						$query->whereIn("id", $value);
					}
				}else{
					$query->where($field, '=', $value);
				}
                        }

		return $query->get();

	}
}
