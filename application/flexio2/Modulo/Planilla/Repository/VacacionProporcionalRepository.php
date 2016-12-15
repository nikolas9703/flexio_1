<?php namespace Flexio\Modulo\Planilla\Repository;

  
 class VacacionProporcionalRepository{
  
     private function _periodo_actual_vacacion($fecha_contrato_inicial = NULL, $fecha_contrato_final = NULL) {
           
         $i=0;
         while($fecha_contrato_inicial < date("Y-m-d")) {
         
             $almanenando_fechas[] = $fecha_contrato_inicial;
             $nueva_fecha = strtotime ( '+1 year' , strtotime ( $fecha_contrato_inicial ) ) ;
 
             if($i>0){
                 $periodos[] = array(
                     "inicial"	=>$almanenando_fechas[$i-1],
                     "final"	=> $fecha_contrato_inicial,
                 );
             }
             $fecha_contrato_inicial = date ( 'Y-m-d' , $nueva_fecha );
             ++$i;
         }
         if($almanenando_fechas[$i-1] < date("Y-m-d")){
             $periodos[] = array(
                 "inicial"	=>$almanenando_fechas[$i-1],
                 "final"	=> $fecha_contrato_final
             );
         }
          
          return end($periodos);
     }
     public function vacacion_proporcional($ObjetoColaborador) {
         
         $salario_listas = isset($ObjetoColaborador['colaborador']['salarios_devengados_ultimos_cinco_anos'])?$ObjetoColaborador['colaborador']['salarios_devengados_ultimos_cinco_anos']:array();
         
         if(isset($ObjetoColaborador['contrato'])){
             $periodo_vacacion_actual = $this->_periodo_actual_vacacion(
                 $ObjetoColaborador['contrato']['fecha_ingreso'],
                 $ObjetoColaborador['contrato']['fecha_salida']
                 );
             $salario_vacacion = $this->_salario_xmeses($periodo_vacacion_actual, $salario_listas);
             return $salario_vacacion/11;
         }else{
             return 0;
         }
         
     
     }
     
     //$salario_vacacion = $this->_salario_xmeses($periodo_vacacion_actual, $salario_listas);
    private function _salario_xmeses($periodo_vacacion_actual = array(), $salarios=array()) {
     
          if(empty($salarios)){
             $return_sal =  0; 
         }else{
             $salario_total = 0;
              foreach($salarios as $salario){
                    if(date("Y-m-d", strtotime($salario['fecha_cierre_planilla']))>= $periodo_vacacion_actual['inicial'] && date("Y-m-d", strtotime($salario['fecha_cierre_planilla']))<=$periodo_vacacion_actual['final']){
                        $salario_total += $salario['salario_bruto'];
                   }
             }
             $return_sal =  $salario_total;
         }
           return $return_sal;
      }
     
}
 