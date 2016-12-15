<?php
namespace Flexio\Modulo\Planilla\Repository;
  
 class IndemnizacionRepository {
  
     //Calculos para indemnizacion proporcional
     public function indemnizacion_proporcional($ObjetoColaborador) {
         
         $lista_salarios = (!empty($ObjetoColaborador['colaborador']['salarios_devengados_ultimos_cinco_anos']))?$ObjetoColaborador['colaborador']['salarios_devengados_ultimos_cinco_anos']:array();
         
         if(isset($ObjetoColaborador['contrato'])){
             return  $this->_calculo_indemnizacion( $ObjetoColaborador['contrato'], $lista_salarios);
         }
         else{
             return 0;
         }
     }
     
     private function _ultimasQuincenas($contrato = array()){
         $fecha_salida = $contrato['fecha_salida'];
         $dia = explode('-', $fecha_salida);
          
         if((int)$dia[2]>15){ //15-30
             $ultima_quincena = $dia[0].'-'.$dia[1].'-15';
         }else{ //1-14
             $ultima_quincena = $dia[0].'-'.$dia[1].'-01';
         }
         $hace_6meses =  date("Y-m-d", strtotime("-6 months",strtotime($ultima_quincena)));
         $hace_1mes =  date("Y-m-d", strtotime("-1 months",strtotime($ultima_quincena)));
         
         return array("6_meses"=>$hace_6meses, "1_mes" => $hace_1mes);
     }
     //Calculo   bruto del salario
     private function _promedio_salario_xmeses($salarios = array(), $fecha_delimitador) {
     
         if(empty($salarios)){
             $return_sal =  0; 
         }else{
             $salario_total = 0;
              foreach($salarios as $salario){
                   if(date("Y-m-d", strtotime($salario['fecha_cierre_planilla']))>$fecha_delimitador){
                      $salario_total += $salario['salario_bruto'];
                   }
             }
             $return_sal =  $salario_total;
         }
         
          return $return_sal;
      }
      
     private function _calculo_indemnizacion($contrato = array(), $salarios = array()) {
         $promedio_salarial = $indemnizacion_total = 0;
         $fecha_ingreso = $contrato['fecha_ingreso'];
         $fecha_salida = $contrato['fecha_salida'];
     
         $diff = abs(strtotime($fecha_salida) - strtotime($fecha_ingreso));
          
         $years    = floor($diff / (365*60*60*24));
         $months   = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
         $days     = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
         
         //Para este caso, se saca el promedio de los ultimos 6 meses y de los ultimos 30 dias devengados
         
         $quincenas  = $this->_ultimasQuincenas($contrato); //retorna   array("6_meses"=>3,454.00, "1_mes" => 1,000) sin promedio;
         
         $seis_meses   = $this->_promedio_salario_xmeses($salarios, $quincenas['6_meses'])/6;
         $treinta_dias = $this->_promedio_salario_xmeses($salarios, $quincenas['1_mes']);
         
         /*echo "a=>".$seis_meses."</BR>";
         echo "b=>".$treinta_dias."</BR>";*/
         
         if($seis_meses > $treinta_dias){
             $promedio_salarial = $seis_meses/4.3333;
         }else{
             $promedio_salarial = $treinta_dias/4.3333;
         }
         
         if($years < 10) //menos de 10 a�os
         {
             $anos_recientes = $years*1;
             $meses = $months/12;
             $dias = $days/360;
             $variable_dias_colab = $anos_recientes + $meses + $dias;
             
             //3.4 es constante
             $indemnizacion_total = $variable_dias_colab*(3.4)*$promedio_salarial;
             
         }else{ //Mas de 10 a�os
             $anos_excedente = ($years - 10)*1;
             $anos_recientes = 10*3.4;
             $meses = $months/12;
             $dias = $days/360;
             $variable_dias_colab = $anos_excedente + $anos_recientes + $meses + $dias;
            
             $indemnizacion_total = $variable_dias_colab*$promedio_salarial;
         }
       
         return $indemnizacion_total;
     }
 
}
