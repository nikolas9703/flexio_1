<?php namespace Flexio\Modulo\Planilla\Repository\Regular;

use Flexio\Modulo\Planilla\Models\Pagadas\PagadasColaborador;


 class PlanillaRegularRepository{

     protected $pagadasColaborador;

     function __construct() {
         $this->pagadasColaborador = new PagadasColaborador();
     }



      public function ingresos_regulares(   $ObjetoColaborador ) {
          $retun_salario = [];

         if(!empty($ObjetoColaborador['colaborador']['planilla_activa'])){
          if($ObjetoColaborador['colaborador']['tipo_salario'] == 'Mensual'){ //Menusal
             if(!empty($ObjetoColaborador['colaborador']['planilla_activa']))
             {
                 $dias_laborados =  $this->_dias_laborados($ObjetoColaborador);
                 $salario_bruto = ( $ObjetoColaborador['colaborador']['salario_mensual']/30)*$dias_laborados;

                 $retun_salario = array(
                     "salario_bruto" => $salario_bruto,
                     "prima_produccion" => 0
                 );
             }else{
                 return 0;
             }
         }else{ //Rata
             $retun_salario = $this->salarios_horas_rata($ObjetoColaborador);

         }
         }
         else{
             $retun_salario = array(
                     "salario_bruto" => 0,
                     "prima_produccion" => 0
                 );
         }


         return $retun_salario;
     }
     //Funcion que lo mas probable use RM.
     private function _calculo_prima_productividad($acumulado_salario_bruto, $suma_horas_prima ) {

         $nuevo_salario_bruto = 0;
         $mitad_salario_bruto = $acumulado_salario_bruto*0.50;

         if($suma_horas_prima > $mitad_salario_bruto){ //Debe Conciderarse como Salario Base
             $excendente = $suma_horas_prima - $mitad_salario_bruto;
             $nuevo_salario_bruto = $excendente+$acumulado_salario_bruto;
             $prima_produccion = $mitad_salario_bruto;
         }else{
             $prima_produccion = $suma_horas_prima;
             $nuevo_salario_bruto = $acumulado_salario_bruto;
         }

        return array(
            "salario_bruto"=>$nuevo_salario_bruto,
            "prima_produccion" => $prima_produccion
        );
     }
     private function _dias_laborados($ObjetoColaborador) {

        $dias_laborados = 0;

        $planilla = $ObjetoColaborador['colaborador']['planilla_activa'];

        $fecha_inicio_labores = isset($planilla['rango_fecha1'])?$planilla['rango_fecha1']:$planilla[0]['rango_fecha1'];
        $fecha_final_labores =  isset($planilla['rango_fecha2'])?$planilla['rango_fecha2']:$planilla[0]['rango_fecha2'];

        $dias_laborados	= (strtotime($fecha_inicio_labores)-strtotime($fecha_final_labores))/86400;
        $dias_laborados = abs($dias_laborados);
        $dias_laborados = floor($dias_laborados);

        return $dias_laborados;
     }

     public function salarios_horas_rata( $ObjetoColaborador ) {
         $salarios = [];

         if(!empty( $ObjetoColaborador['colaborador']['planilla_activa'] )){
             $acumulado_salario_bruto =  $suma_horas_prima = 0;
             $horas_trabajadas  = $ObjetoColaborador['colaborador']['planilla_activa'][0]['colaboradores_planilla'][0]['ingreso_horas'];
             if(!empty($horas_trabajadas)){

                 $rata_hora = $ObjetoColaborador['colaborador']['rata_hora'];

                 foreach($horas_trabajadas as $horas){
                     if(  trim(strtolower($horas['recargo']['nombre'])) == 'prima'){
                         if(!empty($horas['dias'])){
                             foreach($horas['dias'] as $valor){
                                 $suma_horas_prima += $valor['horas'];
                             }
                         }
                     }else{
                         $suma_horas_x_recargo = 0;
                         if(!empty($horas['dias'])){
                             foreach($horas['dias'] as $valor){
                                 $suma_horas_x_recargo += $valor['horas'];
                             }
                         }
                         $salario_por_recargo = $suma_horas_x_recargo*($horas['recargo']['porcentaje_hora']*$rata_hora);
                         if(!empty($horas['beneficio'])){
                             $salario_por_recargo = $salario_por_recargo + ($salario_por_recargo*($horas['beneficio']['modificador_actual']/100));

                         }
                         $acumulado_salario_bruto += $salario_por_recargo;
                     }


                 }
             }

             $salarios =  $this->_calculo_prima_productividad($acumulado_salario_bruto, $suma_horas_prima );
         }

         return $salarios;
     }


}
