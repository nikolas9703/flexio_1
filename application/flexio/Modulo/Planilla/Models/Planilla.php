<?php
namespace Flexio\Modulo\Planilla\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Liquidaciones\Models\Liquidacion;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasColaborador;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaDeducciones;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaAcumulados;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaColaborador;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaCentros;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaVacacion;
use Flexio\Modulo\Vacaciones\Models\Vacaciones;

//use Flexio\Modulo\ConfiguracionPlanilla\Models\Deducciones;  //Este lo agregue para sacar directamnete la deduccion de gastos de representacion
//
//Deducciones
//PagadasColaborador
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Planilla extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = [
      'identificador',
      'semana','ano','secuencial','uuid_planilla','nombre', 'fecha_pago', 'rango_fecha1', 'rango_fecha2', 'monto', 'descuentos', 'colaboradores', 'estado_id','ciclo_colaboradores',
    'activo','empresa_id','centro_contable_id','sub_centro_contable_id','area_negocio','pasivo_id','fecha_creacion','ciclo_id','tipo_id','cuenta_debito_id'];

    protected $table = 'pln_planilla';
	  protected $fillable = ['id','identificador','semana','ano','secuencial','uuid_planilla','nombre', 'fecha_pago', 'rango_fecha1', 'rango_fecha2', 'monto', 'descuentos', 'colaboradores', 'estado_id','ciclo_colaboradores','activo','empresa_id','centro_contable_id','sub_centro_contable_id','area_negocio',
      'pasivo_id','fecha_creacion','ciclo_id','tipo_id','total_colaboradores','pagadas_colaboradores','codigo','cuenta_debito_id'];
	  protected $guarded = ['id'];
    protected $appends      = ['icono','codigo','enlace','salario_bruto','salario_neto' ];
	  public $timestamps = true;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_planilla' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
      $attrs = [
      "href"  => $this->enlace,
      "class" => "link"
          ];

      $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
      return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }

    public function getNumeroDocumentoAttribute()
    {

      return $this->codigo;
    }

    public function getSalarioBrutoAttribute() {

        $salario_bruto = $this->colaboradores_pagadas()->sum('salario_bruto');
        return (float) $salario_bruto;
    }

    public function getSalarioNetoAttribute() {

        $salario_bruto = $this->colaboradores_pagadas()->sum('salario_neto');
        return (float) $salario_bruto;
    }

 	public function liquidaciones(){
		return $this->belongsToMany(Liquidacion::Class, 'pln_planilla_liquidacion', 'planilla_id', 'liquidacion_id');
	}

	public function liquidacionesPlanilla(){
 		return $this->hasMany(LiquidacionPlanilla::Class, 'planilla_id');
	}
	//GETS
	public function getUuidPlanillaAttribute($value)
	{
	    return strtoupper(bin2hex($value));
	}
	//Funciones no Aprobadas

	public function setUuidPlanillaAttribute($value)
	{
		$this->attributes['uuid_planilla'] = Capsule::raw("ORDER_UUID(uuid())");
	}
/*
public function deduccionISLRGastoRepresentacion()
{
    //Debe ir aqui para halar la deduccion de ISRL, habria q hacer los mismo para SS
}*/

  public function acumulados2()
	{
		return $this->hasMany(PlanillaAcumulados::class, 'planilla_id');
	}
  public function deducciones2(){
       return $this->hasMany(PlanillaDeducciones::class, 'planilla_id');
 }


  //Funcion obsoleta
	public function deducciones()
	{
		return $this->hasMany('Planilla_deducciones_orm', 'planilla_id');
	}

  //Funcion obsoleta
  public function acumulados()
  {
    return $this->hasMany('Planilla_acumulados_orm', 'planilla_id');
  }



	public function deducciones_info()
	{
  		return $this->belongsToMany('Deducciones_orm', 'pln_planilla_deducciones', 'planilla_id', 'deduccion_id');
 	}

 	public function acumulados_info()
 	{
 		return $this->belongsToMany('Acumulados_orm', 'pln_planilla_acumulados', 'planilla_id', 'acumulado_id')->withPivot('fecha_creacion');
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
//Informacion de lo lista de colabora que aparecen en la Planilla abierta
 	public function colaboradores(){
		return $this->belongsToMany('Colaboradores', 'pln_planilla_colaborador', 'planilla_id', 'colaborador_id');
	}

//Informacion de lo lista de colabora que aparecen en la Planilla cerrda
  public function colaboradores_pagadas(){
       return $this->hasMany(PagadasColaborador::class, 'planilla_id', 'id');
}


	public function vacaciones(){
		return $this->belongsToMany('Vacaciones_orm', 'pln_planilla_vacacion', 'planilla_id', 'vacacion_id');
	}

  public function vacaciones2(){
    return $this->belongsToMany(Vacaciones::Class, 'pln_planilla_vacacion', 'planilla_id', 'vacacion_id');
  }


	public function licencias(){
		return $this->belongsToMany('Licencias_orm', 'pln_planilla_licencia', 'planilla_id', 'licencia_id');
	}


  public function colaboradores_planilla()
  {
     return $this->hasMany(PlanillaColaborador::Class, 'planilla_id');
  }
  public function vacaciones_planilla()
  {
     return $this->hasMany(PlanillaVacacion::Class, 'planilla_id');
  }
  public function centros_contables()
  {
    return $this->hasMany(PlanillaCentros::class, 'planilla_id');

   }

	public function tipo(){
		return $this->hasOne('Catalogo_orm', 'id_cat', 'tipo_id');
	}

	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{

 		$query = self::with(array('estado','colaboradores',"tipo","liquidaciones","vacaciones","licencias"));

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
 	public function scopeDeLiquidacion($query, $id_liquidacion) {
 		return $query->where("liquidacion_id", $id_liquidacion);
 	}



    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function planillas_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("planilla/ver/".$this->uuid_planilla);
    }
    public function getIconoAttribute(){
        return 'fa fa-institution';
    }
    public function getCodigoAttribute(){
        return $this->identificador . $this->semana . $this->ano . $this->secuencial;
    }

}
