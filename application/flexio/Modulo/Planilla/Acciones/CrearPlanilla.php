<?php
namespace Flexio\Modulo\Planilla\Acciones;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaDeducciones;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaAcumulados;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaColaborador;
use Flexio\Modulo\Planilla\Models\Abiertas\PlanillaCentros;
use Flexio\Modulo\Colaboradores\Repository\ColaboradoresRepository;

class CrearPlanilla{
  protected $ColaboradoresRepository;

  function __construct() {
        $this->ColaboradoresRepository = new ColaboradoresRepository();
  }
  public function editar($planilla, $post){

        $fecha_rango_1 = '';
        if( $post['rango_fecha1']!=''){
          $fecha1 = explode("/", trim($post['rango_fecha1']));
          $rango_1 = $fecha1[2].'-'.$fecha1[1].'-'.$fecha1[0];
          $fecha_rango_1 =  date("Y-m-d", strtotime($rango_1));
        }

        $fecha_rango_2 = '';
        if($post['rango_fecha2']!=''){
          $fecha2 = explode("/", trim($post['rango_fecha2']));
          $rango_2 = $fecha2[2].'-'.$fecha2[1].'-'.$fecha2[0];
          $fecha_rango_2 =  date("Y-m-d", strtotime($rango_2));
        }

        $planilla->rango_fecha1 =$fecha_rango_1;
        $planilla->rango_fecha2 =$fecha_rango_2;
        $planilla->pasivo_id =$post['pasivo_id'];
        $planilla->cuenta_debito_id =$post['cuenta_debito_id'];
        $planilla->save();

        $planilla->deducciones2()->delete();
        if(isset($post['deducciones']))
		 	    $planilla->deducciones2()->saveMany($this->collection_tabla_deducciones($post['deducciones']));

        $planilla->acumulados2()->delete();
        if(isset($post['acumulados']))
				$planilla->acumulados2()->saveMany($this->collection_tabla_acumulados($post['acumulados']));

        return $planilla;
  }

  public function crear($post){

        $planilla_creada = Planilla::create($this->collection_planilla($post));


        if(isset($post['deducciones']))
		 	    $planilla_creada->deducciones2()->saveMany($this->collection_tabla_deducciones($post['deducciones']));

        if(isset($post['acumulados']))
				    $planilla_creada->acumulados2()->saveMany($this->collection_tabla_acumulados($post['acumulados']));

				$planilla_creada->colaboradores_planilla()->saveMany($this->collection_tabla_colaboradores($post['to']));
        $planilla_creada->centros_contables()->saveMany($this->collection_tabla_centro($post['centro_contable_id']));

        return $planilla_creada;
  }

   private function collection_tabla_colaboradores($lista_colaboradores) {

     $planilla_colaborador = [];
     if(count($lista_colaboradores)){
       foreach($lista_colaboradores as $id){
           $tipo =  $this->ColaboradoresRepository->find($id);

          $fieldset = array();
           $fieldset['colaborador_id'] 		= $id;
           $fieldset['estado_ingreso_horas'] 	= 0;

         if($tipo->tipo_salario == 'Mensual')
           $fieldset['estado_ingreso_horas'] 	= 3;

        /* if($tipo->valor == 'xiii_mes')
         {
           $fieldset['estado_ingreso_horas'] 	= 3;
         }*/
           $planilla_colaborador[] 	 = new PlanillaColaborador($fieldset);
       }

     }
     return $planilla_colaborador;
   }

private function collection_tabla_centro($centros_contables) {
  $centro =[];
  if(count($centros_contables)){
      foreach ($centros_contables AS $valor){
         $fieldset["centro_contable_id"] 	= $valor;
         $centro[] 				= new PlanillaCentros($fieldset);
    }
  }
   return $centro;
}


  private function collection_tabla_acumulados($lista_acumulados) {
    $acumulados =[];
    if(count($lista_acumulados)){

       foreach ($lista_acumulados AS $acumulado){
          $fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
          $fieldset["acumulado_id"] 	= $acumulado;
          $acumulados[] 				= new PlanillaAcumulados($fieldset);
      }
    }
     return $acumulados;
}

  private function collection_tabla_deducciones($lista_deducciones) {
    $deducciones =[];
    if(count($lista_deducciones)){

       foreach ($lista_deducciones AS $deduccion){
          $fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
          $fieldset["deduccion_id"] 	= $deduccion;
          $deducciones[] 				= new PlanillaDeducciones($fieldset);
      }
    }
     return $deducciones;
}

  private function collection_planilla($post) {
    $fecha1 = explode("/", trim($post['rango_fecha1']));
  	$rango_1 = $fecha1[2].'-'.$fecha1[1].'-'.$fecha1[0];
  	$fecha2 = explode("/", trim($post['rango_fecha2']));
  	$rango_2 = $fecha2[2].'-'.$fecha2[1].'-'.$fecha2[0];

    $fecha_rango_1 =  date("Y-m-d", strtotime($rango_1));
    $fecha_rango_2 =  date("Y-m-d", strtotime($rango_2));
    $secuencial = $this->ajax_cargar_numero_secuencial($post['empresa_id']);

    //81: liquidaciones;
    //80: vacaciones; 29
    //79: regular;
    //83: licnecias
    //96: Decimo 29

    $estado_id = 13;
    if($post['tipo_id'] == 80 || $post['tipo_id'] == 96 ){
      $estado_id = 29;
    }
    	return  array(
      "codigo" 	 =>  'PL'.(int)date("W", strtotime($fecha_rango_1)).(int)date("y", strtotime($fecha_rango_1)).$secuencial,
      "identificador" 	 =>  'PL',
      "semana" 			 =>  (int)date("W", strtotime($fecha_rango_1)),
      "ano" 			 	  => (int)date("y", strtotime($fecha_rango_1)),
      "secuencial" 			 	  => $secuencial,
      "fecha_cierre_planilla" 		 =>  $fecha_rango_2,
      "fecha_pago" 		 =>  '',
      "rango_fecha1" 		 =>  $fecha_rango_1,
      "rango_fecha2" 		 =>  $fecha_rango_2,
      "empresa_id" 		 =>  $post['empresa_id'],
      "activo" 	  		 =>  1,
      "estado_id" 		 =>  $estado_id,
      "pasivo_id" 		 =>  $post['pasivo_id'],
      "cuenta_debito_id" 		 =>  $post['cuenta_debito_id'],
      "fecha_creacion" 	 =>  date("Y-m-d H:m:s"),
      "ciclo_id" 		 	 =>  $post['ciclo_id'],
      "tipo_id" 		 	 =>  $post['tipo_id'],
      "area_negocio" 		 	 =>  $post['area_negocio_id'],
      "total_colaboradores" => count($post['to'])
   	);

  }


   	private function ajax_cargar_numero_secuencial($empresa_id) {

   		$response = Planilla::where("empresa_id", "=",$empresa_id)
  		->orderBy('id', 'DESC')
  		->limit(1)
  		->get()->first();

  		if(count($response) && $response->secuencial != NULL){
   			$codigo = (int)$response->secuencial + 1;
  		}
  		else
  			$codigo = 1;


  		return $codigo;
  	}


  }
