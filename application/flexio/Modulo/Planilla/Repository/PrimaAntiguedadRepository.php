<?php namespace Flexio\Modulo\Planilla\Repository;

  
 class PrimaAntiguedadRepository{
  
     public function prima_antiguedad_proporcional($ObjetoColaborador) {
          
         $semanalmente = $this->_calculo_salarios_ganados($ObjetoColaborador);
         $tiempo_laborado = $this->_calculo_tiempo_laborado($ObjetoColaborador);
     
         return $semanalmente*$tiempo_laborado;
     
     }
     private function _calculo_salarios_ganados($ObjetoColaborador) {
         //Formulas del documento
         if(!empty($ObjetoColaborador['colaborador']['salarios_devengados_ultimos_cinco_anos'])){
             $objeto          = $ObjetoColaborador['colaborador']['salarios_devengados_ultimos_cinco_anos'];
             $lo_ganado       =  array_sum(array_column($objeto, 'salario_bruto'));
             $anualmente      = $lo_ganado/5; //Lo ganado Anualmente en 5 años
             $mensualmente    = $anualmente/12; //Lo ganado mensualmente
             $semanalmente    = $mensualmente/4.3333; //Lo ganado semanalmente
     
         }else {
             $semanalmente =   0;
         }
     
         return $semanalmente;
     }
     
     private function _calculo_tiempo_laborado($objeto = array()) {
         
         
         if( isset($objeto['contrato'])){
             $fecha_ingreso = $objeto['contrato']['fecha_ingreso'];
             $fecha_salida = $objeto['contrato']['fecha_salida'];
             
             $diff = abs(strtotime($fecha_salida) - strtotime($fecha_ingreso));
              
             $years    = floor($diff / (365*60*60*24));
             $months   = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
             $days     = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
              
             $variable_ano = ($years>9)?10.00000:1.0000;
              
             $variable_tiempo_laborado = $variable_ano + $months/12 + $days/360;
         }
         else{
             $variable_tiempo_laborado = 0;
         }
       
         return $variable_tiempo_laborado;
     }
     
}
 