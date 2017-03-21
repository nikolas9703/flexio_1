<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasColaborador;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaCentros;


class Planilla_orm extends Model
{
	protected $table = 'pln_planilla';
	protected $fillable = ['identificador','semana','ano','secuencial','uuid_planilla','nombre', 'fecha_pago', 'rango_fecha1', 'rango_fecha2', 'monto', 'descuentos', 'colaboradores', 'estado_id','ciclo_colaboradores','activo','empresa_id','centro_contable_id','sub_centro_contable_id','area_negocio','pasivo_id','fecha_creacion','ciclo_id','tipo_id','codigo','cuenta_debito_id','total_colaboradores'];
	protected $guarded = ['id'];
	public $timestamps = false;
    protected $appends      = ['salario_bruto','salario_neto'];

		public function getUuidPlanillaAttribute($value)
		{
		    return strtoupper(bin2hex($value));
		}

	public function getSalarioBrutoAttribute() {

			$salario_bruto = $this->colaboradores_pagadas()->sum('salario_bruto');
 			return (float) $salario_bruto;
	}

	public function getSalarioNetoAttribute() {

			$salario_neto = $this->colaboradores_pagadas()->sum('salario_neto');
			return (float) $salario_neto;
	}
	public function centros_contables()
  {
    return $this->hasMany(PlanillaCentros::class, 'planilla_id');

   }
	//Informacion de lo lista de colabora que aparecen en la Planilla cerrda
	  public function colaboradores_pagadas(){
	       return $this->hasMany(PagadasColaborador::class, 'planilla_id', 'id');
	}


	public static function centro_lista_por_empresa($empresa_id=NULL){

		 $result = Capsule::table('cen_centros')
 		->where('empresa_id', $empresa_id)
		->where('estado', 'Activo')
		->whereNotIn("id", function($q) use ($empresa_id){
			$q->select("padre_id")
			->from("cen_centros")
			->where("empresa_id", $empresa_id);
		})
		->get(array('id', 'nombre'));
		return $result;
 	}

	public static function lista_departamentos_centro($empresa_id = NULL, $centro_id = NULL){

		$result = Capsule::table('dep_departamentos_centros AS dc')
		->leftJoin('dep_departamentos AS d', 'd.id', '=', 'dc.departamento_id')
		->where('dc.empresa_id', $empresa_id)
		->where('dc.centro_id', $centro_id)
		->get(array('d.id', 'd.nombre'));
		return $result;
	}

	public function setUuidPlanillaAttribute($value)
	{
		$this->attributes['uuid_planilla'] = Capsule::raw("ORDER_UUID(uuid())");
	}

	public function acumulados()
	{
		return $this->hasMany('Planilla_acumulados_orm', 'planilla_id');
	}

	public function deducciones()
	{
		return $this->hasMany('Planilla_deducciones_orm', 'planilla_id');
	}
	public function deducciones_info()
	{
  		return $this->belongsToMany('Deducciones_orm', 'pln_planilla_deducciones', 'planilla_id', 'deduccion_id');
 	}


 	public function acumulados_info()
 	{
 		return $this->belongsToMany('Acumulados_orm', 'pln_planilla_acumulados', 'planilla_id', 'acumulado_id')->withPivot('fecha_creacion');
  	}





	public static function centro_contable_lista($empresa_id=NULL, $lista_deducciones = NULL){
  		$result = Capsule::table('cen_centros AS d')
 				->where('d.empresa_id', $empresa_id)
				  ->where('d.estado', 'Activo')
				 ->whereNotIn('d.id', $lista_deducciones)
 				 ->get(array('d.id', 'd.nombre'));


  		return (!empty($result) ? array_map(function($result){ return array("id" => $result->id, "nombre" => $result->nombre ); }, $result) : array());
	}


 	public function estado(){
		return $this->hasOne('Estado_orm', 'id_cat', 'estado_id');
	}
	public function centro(){
		return $this->hasOne('Centros_orm', 'id', 'centro_contable_id');
	}
	public function subcentro(){
		return $this->hasOne('Centros_orm', 'id', 'sub_centro_contable_id');
	}
	public function area_negocios(){
		return $this->hasOne('Departamentos_orm', 'id', 'area_negocio');
	}

	public function pasivos()
	{
		return $this->hasOne('Cuentas_orm', 'id','pasivo_id');
	}

 	public function colaboradores(){
		return $this->belongsToMany('Colaboradores_orm', 'pln_planilla_colaborador', 'planilla_id', 'colaborador_id');
	}

	public function liquidaciones(){
		return $this->belongsToMany('Liquidaciones_orm', 'pln_planilla_liquidacion', 'planilla_id', 'liquidacion_id');
	}

	public function vacaciones(){
		return $this->belongsToMany('Vacaciones_orm', 'pln_planilla_vacacion', 'planilla_id', 'vacacion_id');
	}

	public function licencias(){
		return $this->belongsToMany('Licencias_orm', 'pln_planilla_licencia', 'planilla_id', 'licencia_id');
	}

	public function colaboradores_planilla()
	{
	return $this->hasMany('Planilla_colaborador_orm', 'planilla_id');
	}

	public function tipo(){
		return $this->hasOne('Catalogo_orm', 'id_cat', 'tipo_id');
	}

	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{

 		$query = self::with(array('estado','centros_contables.centro_info','colaboradores',"tipo","liquidaciones","vacaciones","licencias"));

  		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/cargo/i", $sidx)){
				$query->orderBy($sidx, $sord);
			}
		}

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

  		if($limit!=NULL) $query->skip($start)->take($limit);
 		return $query->get();
	}
 	public static function lista_codigos($empresa_id = NULL){
		return Capsule::table('pln_planilla AS pl')
 		->where('pl.empresa_id', '=', $empresa_id)
 		->get(array('pl.id',Capsule::raw("CONCAT_WS(pl.identificador, pl.semana,pl.secuencial) as nombre") ));
 	}
}
