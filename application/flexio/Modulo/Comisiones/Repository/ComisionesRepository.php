<?php

namespace Flexio\Modulo\Comisiones\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;

use Flexio\Modulo\Comisiones\Models\Comisiones;
use Flexio\Modulo\Comisiones\Models\ComisionAcumulado;
use Flexio\Modulo\Comisiones\Models\ComisionDeduccion;
use Flexio\Modulo\Comisiones\Models\ComisionColaborador;
use Flexio\Modulo\Comisiones\Models\ComisionColaboradorDeduccion;
use Flexio\Modulo\Comisiones\Models\ComisionColaboradorAcumulado;
use Flexio\Modulo\Comentario\Models\Comentario;
//use Flexio\Modulo\Comisiones\Repository\ColaboradorRepository as ColaboradorRepository;

class ComisionesRepository
{
  /*private $ColaboradorRepository;
   function __construct()
   {
   dd($this);
           //$this->ColaboradorRepository = new ColaboradorRepository();

   }*/
    public function find($id){
          return Comisiones::find($id);
    }

    function findByUuid($uuid) {
           return Comisiones::where('uuid_comision',hex2bin($uuid))->first();

    }
    function agregarComentario($id, $comentarios) {
        $comisiones = Comisiones::find($id);
        $comentario = new Comentario($comentarios);
        $comisiones->comentario_timeline()->save($comentario);
        return $comisiones;
    }

    public function create($post){

 			$comision_creada = Comisiones::create($post['campo']);
 	    $comision_creada->acumulados()->saveMany($this->_getAcumulados($post['acumulados']));
 	    $comision_creada->deducciones()->saveMany($this->_getDeducciones($post['deducciones']));
 	    $comision_creada->colaboradores()->saveMany($this->_getColaboradores($post['colaboradores_to']));

      $this->creandoRelaciones($comision_creada, array());

      return $comision_creada;
    }

    public function editar($comision_editar, $post){

      //Limpiar data almacenada por si acaso cambia los acumulados y deducciones
      $comision_editar->acumulados()->delete();
      $comision_editar->deducciones()->delete();
      if(isset($post['acumulados']))
      $comision_editar->acumulados()->saveMany($this->_getAcumulados($post['acumulados']));
      if(isset($post['deducciones']))
      $comision_editar->deducciones()->saveMany($this->_getDeducciones($post['deducciones']));

      $this->creandoRelaciones($comision_editar, $post);

      return $comision_editar;
    }

    public function ajax_por_aprobar($comision_editar, $post){

      $this->creandoRelaciones($comision_editar, $post);

      return $comision_editar;
    }

   private function creandoRelaciones($comision_creada, $post){

    if($comision_creada->colaboradores){
          $collection = $comision_creada->colaboradores;

           $collection->each(function ($colaborador, $key) use ($comision_creada, $post){

            //Limpiamos por si acaso las relaciones
             $colaborador->acumulados_aplicados()->delete();
             $colaborador->deducciones_aplicados()->delete();

             if(count($comision_creada->acumulados) > 0){
                  foreach($comision_creada->acumulados as $acumulado){
                      $acumulado_lista = [];
                      $acumulado_lista['com_acumulado_id'] = $acumulado->id;
                      $acumulado_lista['monto'] = 0;
                      $acumulado_aplicados_lista[]  = new ComisionColaboradorAcumulado($acumulado_lista);
                  }
                 $colaborador->acumulados_aplicados()->saveMany($acumulado_aplicados_lista);
             }
           if(count($comision_creada->deducciones) > 0){
              foreach($comision_creada->deducciones as $deduccion){
                  $deduccion_lista = [];
                  $deduccion_lista['monto'] = 0;
                  $deduccion_lista['com_deduccion_id'] = $deduccion->id;
                  $deducciones_aplicados_lista[]  = new ComisionColaboradorDeduccion($deduccion_lista);
              }
                  $colaborador->deducciones_aplicados()->saveMany($deducciones_aplicados_lista);
            }
      });

      if(count($post) && $post['estado']['por_pagar'] == 1) //Se deben realizar calculos en las deducciones
      {
            $comision_creada->load(
            "colaboradores.deducciones_aplicados.deduccion_dependiente.deduccion_info",
            "colaboradores.acumulados_aplicados.acumulado_dependiente.acumulado_info.formula");
            $collection_nueva = $comision_creada->colaboradores;
            $collection_nueva->each(function ($colaborador){
                $this->calculos_deducciones($colaborador);
                $this->calculos_acumulados($colaborador);
            });

      }

    }
    return $comision_creada;
 }


    public function _getColaboradores($colaboradores)
    {
       if(count($colaboradores)){
        foreach($colaboradores as $colaborador)
        {
            $fieldset = [];
            //$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
            $fieldset["colaborador_id"] = $colaborador;
            $fieldset["monto_total"] = 0.00;
            $comision_colaborador[] = new ComisionColaborador($fieldset);
          }
        }
         return $comision_colaborador;
       }

private function _getDeducciones($deducciones)
    {
         $deducciones_lista = [];
        if(count($deducciones['deducciones'])){
          foreach($deducciones['deducciones'] as $deduccion)
          {
            $fieldset = [];
            $fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
            $fieldset["deduccion_id"] = $deduccion;
            $deducciones_lista[] = new ComisionDeduccion($fieldset);
          }
        }
         return $deducciones_lista;
}

    private function _getAcumulados($acumulados)
        {
             $acumulados_lista = [];
            if(count($acumulados['acumulados'])){
              foreach($acumulados['acumulados'] as $acumulado)
              {
                $fieldset = [];
                $fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
                $fieldset["acumulado_id"] = $acumulado;
                $acumulados_lista[] = new ComisionAcumulado($fieldset);
              }
            }
             return $acumulados_lista;
    }

    public function calculos_acumulados( $ObjetoColaborador ) {
           $variable_operador = $ObjetoColaborador->monto_total;


                if(count($ObjetoColaborador->acumulados_aplicados)){
                 foreach($ObjetoColaborador->acumulados_aplicados as $acumulado){

                  if(isset($acumulado->acumulado_dependiente[0]->acumulado_info->formula)){

                     $formula_acumulado = $acumulado->acumulado_dependiente[0]->acumulado_info->formula;
                    if($formula_acumulado['tipo_calculo_uno'] != ''){
                      if($formula_acumulado['tipo_calculo_uno'] == 'Multiplicado por' ){
                         $calculo = $variable_operador*$formula_acumulado['valor_calculo_uno'];
                      }
                      elseif($formula_acumulado['tipo_calculo_uno'] == 'Dividido por' ){
                         $calculo = $variable_operador/$formula_acumulado['valor_calculo_uno'];
                      }
                    }

                    if($formula_acumulado['tipo_calculo_dos'] != ''){
                      if($formula_acumulado['tipo_calculo_dos'] == 'Multiplicado por' ){
                           $calculo = $calculo*$formula_acumulado['valor_calculo_dos'];
                      }
                      elseif($formula_acumulado['tipo_calculo_dos'] == 'Dividido por' ){
                           $calculo = $calculo/$formula_acumulado['valor_calculo_dos'];
                      }
                    }
                      $acumulado->monto = $calculo;
                      $acumulado->save();
                   }else{
                      $acumulado->monto = 0;
                      $acumulado->save();
                   }
                }
                }
       }


    public function calculos_deducciones( $ObjetoColaborador ) {

          $variable_operador = $ObjetoColaborador->monto_total;


          if($variable_operador > 0){

                 if(count($ObjetoColaborador->deducciones_aplicados)){
                  foreach($ObjetoColaborador->deducciones_aplicados as $deduccion){
                      $deducido = 0;
                      $datos_deduccion =   $deduccion->deduccion_dependiente[0]->deduccion_info;

                        if(!preg_match("/descuento/i", $datos_deduccion->nombre))
                        {
                                   if( $datos_deduccion->rata_colaborador_tipo == "Porcentual" ){
                                         $rata = $datos_deduccion->rata_colaborador/100;
                                  }
                                  else if( $datos_deduccion->rata_colaborador_tipo    == "Monto" ){
                                         $rata =  $datos_deduccion->rata_colaborador;
                                  }


                                  if(!preg_match("/Sobre la Renta/i", $datos_deduccion->nombre)){

                                             if( $datos_deduccion->rata_colaborador_tipo  == "Porcentual" ){
                                                    $deducido = $rata*$ObjetoColaborador->monto_total;
                                            }else{
                                                    $deducido = $rata;
                                            }
                                  }
                                  else{ //Es impuesto sobre la renta
                                     $deducido = 0;

                                }
                       }

                      $deduccion->monto = $deducido;
                      $deduccion->save();
                  }
                }
          }
     }

        public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
        {
            $lista_comisiones = Comisiones::where(function($query) use ($clause){

                $this->_filtros($query, $clause);

            });

            if($sidx !== null && $sord !== null){$lista_comisiones->orderBy($sidx, $sord);}
            if($limit != null){$lista_comisiones->skip($start)->take($limit);}
            return $lista_comisiones->get();
        }

        private function _filtros($query, $clause)
        {
            if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
            if(isset($clause['centro_contable_id']) and !empty($clause['centro_contable_id'])){$query->whereCentroContableId($clause['centro_contable_id']);}
            if(isset($clause['estado_id']) and !empty($clause['estado_id'])){$query->whereEstadoId($clause['estado_id']);}
        }

        public function count($clause = array())
        {
            $lista_comisiones = Comisiones::where(function($query) use ($clause){
                 $this->_filtros($query, $clause);
             });

            return $lista_comisiones->count();
        }
}
