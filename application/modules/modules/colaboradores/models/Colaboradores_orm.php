<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Colaboradores\Models\Familia as FamiliaModel;
use Flexio\Modulo\Talleres\Models\EquipoColaboradores;
use Flexio\Modulo\Colaboradores\Models\BaseAcumulados;

class Colaboradores_orm extends Model
{
	protected $table = 'col_colaboradores';
 
	protected $fillable = ['uuid_colaborador','gasto_de_representacion','empresa_id', 'codigo', 'estado_id','sexo_id', 'estado_civil_id','nombre', 'segundo_nombre', 'apellido', 'apellido_materno', 'cedula', 'provincia_id', 'letra_id', 'tomo', 'asiento', 'no_pasaporte', 'seguro_social', 'fecha_nacimiento','edad','lugar_nacimiento','telefono_residencial','celular','email','direccion', 'centro_contable_id','departamento_id', 'cargo_id', 'tipo_salario', 'salario_mensual', 'ciclo_id', 'rata_hora', 'horas_semanales', 'fecha_inicio_labores', 'creado_por', 'estatura', 'peso', 'talla_camisa', 'talla_pantalon', 'no_botas', 'banco_id', 'forma_pago_id', 'tipo_cuenta_id', 'numero_cuenta', 'tutor_nombre', 'tutor_parentesco_id', 'tutor_cedula', 'designado_nombre', 'designado_parentesco_id', 'designado_cedula', 'consulta_medica', 'consulta_medica_fecha', 'consulta_nombre_medico', 'consulta_causas', 'consulta_examen', 'consulta_resultado', 'enfermedad_sufre', 'enfermedad_nombre', 'enfermedad_sometido_tratamiento', 'enfermedad_explicar', 'seguro_otro', 'seguro_nombre_compania', 'seguro_valor', 'deduccion_tipo_declarante_id', 'deduccion_otros_ingresos_id', 'deduccion_zona_postal', 'deduccion_provincia_id', 'deduccion_distrito', 'deduccion_corregimiento', 'deduccion_barrio', 'deduccion_fecha', 'patrono_clasificacion_empleado', 'patrono_razon_social', 'patrono_nombre_comercial', 'patrono_ruc', 'patrono_telefono', 'patrono_direccion', 'digito_verificador', 'islr_gasto_representacion', 'ss_gasto_representacion', 'cuenta_gasto_representacion_id'];
 
	protected $guarded = ['id'];

	public function dependientes(){
		return $this->hasMany('Dependientes_orm', 'colaborador_id');
	}
	public function base_acumulados() {
			return $this->hasMany(BaseAcumulados::class, 'colaborador_id')->where("estado","activo");
	}
	public function familia() {
		return $this->hasMany(FamiliaModel::class, 'colaborador_id');
	}

	public function deducciones(){
		return $this->hasMany('Deducciones_orm', 'colaborador_id');
	}

	public function beneficiarios(){
		return $this->hasMany('Beneficiarios_orm', 'colaborador_id');
	}

	public function beneficiario_principal(){
		return $this->hasMany('Beneficiarios_orm', 'colaborador_id');
	}

	public function beneficiario_contingente(){
		return $this->hasMany('Beneficiarios_orm', 'colaborador_id');
	}

        public function ciclo(){

            return $this->hasOne('Catalogos_orm', 'id_cat', 'ciclo_id');

        }
        public function colaboradores_contratos(){
            return $this->hasMany('Colaboradores_contratos_orm', 'colaborador_id');
        }
        public function colaboradores_contratos_activo(){
            return $this->hasMany('Colaboradores_contratos_orm', 'colaborador_id')->where("estado","1");
        }
	public function beneficiario_pariente(){
		return $this->hasMany('Beneficiarios_orm', 'colaborador_id');
	}

	public function estudios(){
		return $this->hasMany('Estudios_orm', 'colaborador_id');
	}

	public function empresa(){
		return $this->hasOne('Empresa_orm', 'id', 'empresa_id');
	}

	public function experiencia(){
		return $this->hasMany('Experiencia_laboral_orm', 'colaborador_id');
	}

	public function estado(){
		return $this->hasOne('Estado_orm', 'id_cat', 'estado_id');
	}

	public function centro_contable(){
		return $this->hasOne('Centros_orm', 'id', 'centro_contable_id');
	}

	public function departamento(){
		return $this->hasOne('Departamentos_orm', 'id', 'departamento_id');
	}

	public function cargo(){
		return $this->hasOne('Cargos_orm', 'id', 'cargo_id');
	}

	public function banco(){
		return $this->hasOne('Catalogo_orm', 'id_cat', 'banco_id');
	}

	public function forma_pago(){
		return $this->hasOne('Catalogo_orm', 'id_cat', 'forma_pago_id');
	}

	public function toArray()
	{
		$array = parent::toArray();

		$array['nombre_completo'] = (!empty($this->attributes['nombre']) ? $this->attributes['nombre'] : ""). ' ' . (!empty($this->attributes['apellido']) ? $this->attributes['apellido'] : "");

		return $array;
	}

	public static function getEnumValues($table, $column)
	{
		$matches = array();
		$type = Capsule::select(Capsule::raw("SHOW COLUMNS FROM $table WHERE Field = '$column'"));

		preg_match('/^enum\((.*)\)$/', $type[0]["Type"], $matches, PREG_OFFSET_CAPTURE);

		$enum = array();
		foreach( explode(',', $matches[1][0]) as $value )
		{
			$v = trim( $value, "'" );
			$enum = array_add($enum, $v, $v);
		}
		return $enum;
	}

        public function scopeDeEmpresa($query, $empresa_id)
        {
            return $query->where("empresa_id", $empresa_id)->where('estado_id', 1);
        }

        public function scopePorCiclo($query, $ciclo_id)
        {
        	return $query->where("ciclo_id", $ciclo_id);
        }

        public function getUuidColaboradorAttribute($value)
        {
        return strtoupper(bin2hex($value));
        }

        //SE USA EN OTROS MODULOS...
        public function  uuid()
        {
            return strtoupper(bin2hex($this->uuid_colaborador));
        }

        public function comp_nombreCompleto()
        {
            return $this->nombre." ".$this->apellido;
        }

        public function comp_colaboradorEnlace()
        {
            return '<a style="color:blue;" href="'.base_url("colaboradores/ver/".$this->comp_uuidColaborador()).'">'.$this->comp_nombreCompleto().'</a>';
        }

        public function comp_uuidColaborador(){
            return strtoupper(bin2hex($this->uuid_colaborador));
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

	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
 		$cargo 			= !empty($clause["cargo"]) ? $clause["cargo"] : array();
		$departamentos 	= !empty($clause["departamento_id"]) ? $clause["departamento_id"] : array();
		$colaboradores 	= !empty($clause["colaborador"]) ? $clause["colaborador"] : array();
		$nombre_centro = !empty($clause["nombre_centro"]) ? $clause["nombre_centro"] : array();
		$equipoid 		= !empty($clause["equipoid"]) ? $clause["equipoid"] : array();

		$query = self::with(array('estado', 'cargo' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		},'departamento' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/departamento/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}, 'centro_contable' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/centro_contable/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}));

		//colaboradores de modulo equipo de trabajo
		//filtro para modulo equipo de trabajo.
		if(!empty($equipoid)){
			$colaboradores = EquipoColaboradores::where("equipo_trabajo_id", $equipoid)->get(array("colaborador_id"))->toArray();
			$col = (!empty($colaboradores) ? array_map(function($colaboradores){ return $colaboradores["colaborador_id"]; }, $colaboradores) : "");
			$query->whereIn("id", $col);
		}

		if(!empty($departamentos)){
  			$query->whereIn("departamento_id",$departamentos);
 		}
 		if(!empty($colaboradores)){
 			$query->whereIn("id",$colaboradores);
 		}

		//Filtrar Departamento
		if(!empty($cargo)){
			$cargos = Cargos_orm::where("nombre", $cargo[0], $cargo[1])->get(array('id'))->toArray();
			if(!empty($cargos)){
				$cargo_id = (!empty($cargos) ? array_map(function($cargos){ return $cargos["id"]; }, $cargos) : "");
				$query->whereIn("cargo_id", $cargo_id);
			}
		}

		//Filtrar Centro Contable
		if(!empty($nombre_centro)){
			$centros = Centros_orm::where("nombre", $nombre_centro[0], $nombre_centro[1])->get(array('id'))->toArray();
			if(!empty($centros)){
				$centro_id = (!empty($centros) ? array_map(function($centros){ return $centros["id"]; }, $centros) : "");
				$query->whereIn("centro_contable_id", $centro_id);
			}
		}

		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "equipoid" || $field == "cargo" || $field == "departamento" || $field == "departamento_id" ||   $field == "colaborador"  || $field == "id" || $field == "nombre_centro"){
					continue;
				}

				//Concatenar Nombre y Apellido para busqueda
				if($field == "nombre"){
					$field = Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, ''))");
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

		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		//return $query->get(array('id', Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre"), 'cedula', 'created_at', Capsule::raw("HEX(uuid_colaborador) AS uuid")));
		return $query->get();
	}


        public function colaborador_sexo(){

            return $this->hasOne('Catalogos_orm', 'id_cat', 'sexo_id');

        }

        public function colaborador_estado_civil(){

            return $this->hasOne('Catalogos_orm', 'id_cat', 'estado_civil_id');

        }

        public function distribucion(){
            return $this->hasMany('Flexio\Modulo\Colaboradores\Models\ColaboradoresDistribucionSalario', 'colaborador_id');
        }



}
