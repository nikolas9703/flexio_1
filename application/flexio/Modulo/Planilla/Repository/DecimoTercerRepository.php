<?php namespace Flexio\Modulo\Planilla\Repository;

  
 class DecimoTercerRepository{
  
     private function _periodo_actual( $fecha_contrato_final = NULL) {
            $periodo = [];
            
            $ano_actual = date ( 'Y' , strtotime ( $fecha_contrato_final ) );
            $ano_pasado = date ( 'Y' , strtotime ( '-1 year' , strtotime ( $fecha_contrato_final ) ) );
            
              	 
            $decimo['diciembre'] = $ano_pasado."-12-15";
            $decimo['abril'] = $ano_actual."-04-15";
            $decimo['agosto'] = $ano_actual."-08-15";
              
            if($fecha_contrato_final > $decimo['diciembre'] && $fecha_contrato_final < $decimo['abril']){
               $periodo = array(
            	"inicial" => $decimo['diciembre'],
            	"final" => $decimo['abril']
              );
            }
             else if($fecha_contrato_final > $decimo['abril'] && $fecha_contrato_final < $decimo['agosto']){
                $periodo = array(
            	   "inicial" => $decimo['abril'],
            	   "final" => $decimo['agosto']
                );
            } 
            else if($fecha_contrato_final > $decimo['agosto']){
                $periodo = array(
            	   "inicial" => $decimo['agosto'],
            	   "final" => $decimo['diciembre']
                );
            } 
            return $periodo; 
            
     }
     public function decimo_tercer_mes($ObjetoColaborador) {
         
         $salarios = (!empty($ObjetoColaborador['colaborador']['salarios_devengados_ultimos_cinco_anos']))?$ObjetoColaborador['colaborador']['salarios_devengados_ultimos_cinco_anos']:array();
          
         $fecha_contrato_final = isset($ObjetoColaborador['contrato']['fecha_salida'])?$ObjetoColaborador['contrato']['fecha_salida']:NULL;
         $periodo_decimo = $this->_periodo_actual( $fecha_contrato_final );
          $salario_decimo = $this->_salario_xmeses($periodo_decimo, $salarios);
          return $salario_decimo*(8.3333/100);
     }
     
     private function _salario_xmeses($periodo_decimo = array(), $salarios=array()) {
     
          if(empty($salarios)){
             $return_sal =  0; 
         }else{
             $salario_total = 0;
              foreach($salarios as $salario){
                    if(date("Y-m-d", strtotime($salario['fecha_cierre_planilla']))>= $periodo_decimo['inicial'] && date("Y-m-d", strtotime($salario['fecha_cierre_planilla']))<=$periodo_decimo['final']){
                        
                         $salario_total += $salario['salario_bruto'];
                   }
             }
             $return_sal =  $salario_total;
         }
            return $return_sal;
      }
     
}
 