<?php
namespace Flexio\Modulo\Planilla\Models\Pagadas;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Colaboradores\Models\Colaboradores;
use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasAcumulados;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDeducciones;
use Carbon\Carbon;

/*
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasIngresos;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDeducciones;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDescuentos;*/

 class PagadasColaborador extends Model
{
	protected $table = 'pln_pagadas_colaborador';
	protected $fillable = ['salario_bruto','planilla_id','contrato_id','colaborador_id','salario_neto','fecha_pago','fecha_creacion','fecha_cierre_planilla'];
	protected $guarded = ['id', 'uuid_colaborador'];
  protected $appends      = ['fecha_cierre_planilla_format'];

	public $timestamps = true;


	public function __construct(array $attributes = array()){
		$this->setRawAttributes(array_merge($this->attributes, array('uuid_colaborador' => Capsule::raw("ORDER_UUID(uuid())"))), true);
		parent::__construct($attributes);
	}
	//GETS
	public function getUuidColaboradorAttribute($value)
	{
	    return strtoupper(bin2hex($value));
	}

  public function getFechaCierrePlanillaFormatAttribute()
	{

      return date("d/m/Y", strtotime($this->fecha_cierre_planilla));
 	}
  public function planilla()
	{
		return $this->belongsTo(Planilla::Class, 'planilla_id');
	}

 	public function deducciones()
	{
		return $this->hasMany(PagadasDeducciones::Class, 'planilla_pagada_id');
	}
  public function acumulados()
  {
    return $this->hasMany(PagadasAcumulados::Class, 'planilla_pagada_id');
  }
  	public function descuentos()
 	{
 		return $this->hasMany(PagadasDescuentos::Class, 'planilla_pagada_id');
 	}
 	public function ingresos()
 	{
  		return $this->hasMany(PagadasIngresos::Class, 'planilla_pagada_id');
 	}
 	public function calculos()
 	{
  		return $this->hasMany(PagadasCalculos::Class, 'planilla_pagada_id');
 	}
  public function scopeDePlanilla($query, $planilla_id) {
 		return $query->where("planilla_id", $planilla_id);
 	}

  public function scopeDeColaborador($query, $colaborador_id) {
    return $query->where("colaborador_id", $colaborador_id);
  }
  public function colaborador()
  {
    //return $this->hasMany(PagadasCalculos::Class, 'planilla_pagada_id');
    return $this->hasOne(Colaboradores::class, 'id', 'colaborador_id');
  }

  	/*public function planilla()
 	{
 		return $this->hasOne('Planilla_orm','id','planilla_id');
 	}*/
 	public function scopeDeHaceCincoAnos($query) {
 	    $haceCinco = strtotime ( '-5 year' , strtotime ( date("Y-m-d") ) ) ;
 	    $haceCinco = date ( 'Y-m-d' , $haceCinco ); //Fecha de hace 5 a�os

 	    return $query->where("fecdha_pago",">=", $haceCinco);
 	}

 	public static function salario_bruto_anual($colaborador_id = NULL){

 	    $salario_bruto_trecemeses = 0;
 	    $fecha_hoy = date('Y-m-j');

 	    $ultimo_periodo = strtotime ( '-12 month' , strtotime ( $fecha_hoy ) ) ;
 	    $ultimo_periodo = date ( 'Y-m-j' , $ultimo_periodo );
 	    $salario_anual = Capsule::table('pln_pagadas_colaborador as cerrada')
 	    ->leftJoin('pln_planilla as pln', 'pln.id', '=', 'cerrada.planilla_id')
 	    ->leftJoin('mod_catalogos as cat', 'cat.id_cat', '=', 'pln.tipo_id')
 	    ->where('cerrada.colaborador_id', $colaborador_id)
 	    ->where('cat.identificador', 'Tipo Planilla')
 	    ->where('cat.valor', 'regular')
 	    ->where('pln.rango_fecha1', ">", $ultimo_periodo)
 	    ->where('pln.rango_fecha2', "<=", $fecha_hoy)
 	    ->orderBy('pln.rango_fecha1',' DESC')
 	    ->get();

 	  		$i= $sumatoria_salario_bruto = $total_anual = 0;
 	  		if(!empty($salario_anual)){
 	  		    foreach($salario_anual as $row){
  	  		        $sumatoria_salario_bruto += $row->salario_bruto;
 	  		        $fecha_inicial = $row->rango_fecha1;

 	  		        ++$i;
 	  		    }

 	  		    $cantidad_meses = $this->meses_entre_fechas($fecha_inicial);
 	  		    if($cantidad_meses < 12){
 	  		        $total_anual = ($sumatoria_salario_bruto/$cantidad_meses)*12;
 	  		    }else{
 	  		        $total_anual = $sumatoria_salario_bruto;
 	  		    }

 	  		}

 	  		return $total_anual;

 	}
 	public  function meses_entre_fechas($fecha_inicial = NULL){

 	    $fecha_hoy = date('Y-m-d');


 	    $fechainicial 	= new DateTime($fecha_inicial);
 	    $fechafinal 	= new DateTime($fecha_hoy);

 	    $diferencia = $fechainicial->diff($fechafinal);
 	    $meses = ( $diferencia->y * 12 ) + $diferencia->m;


 	    return $meses+1;
 	}

}
