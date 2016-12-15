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
use Flexio\Modulo\Comisiones\Repository\ComisionesRepository;


class ColaboradorRepository
{
   private $ComisionesRepository;
   function __construct()
  {
          $this->ComisionesRepository = new ComisionesRepository();

  }
  function find($id) {
           return ComisionColaborador::where('id',$id)->first();
   }
   public function findBy($clause = array())
 	{
  			$comisiones = ComisionColaborador::deComision($clause["comision_id"]);


  			$this->_filtros($comisiones, $clause);

  			return $comisiones->first();
 	}
    public function eliminar_colaborador($ids){
      foreach ($ids as $colaborador) {
       $collectionColaborador = $this->find($colaborador);
       if($collectionColaborador){
         $collectionColaborador->acumulados_aplicados()->delete();
         $collectionColaborador->deducciones_aplicados()->delete();
         $collectionColaborador->delete();
       }
     }
       return $collectionColaborador;
    }
    //Aqui se realizan los calculos para Deducciones y acumulados
  public function editar_calculos($collectionColaborador, $post){

     $collectionColaborador->monto_total = $post['Monto'];
     $collectionColaborador->descripcion = $post['Detalle'];
     $collectionColaborador->save();

     //$resultado = $this->ComisionesRepository->calculos_acumulados($collectionColaborador);
     //$resultado = $this->ComisionesRepository->calculos_deducciones($collectionColaborador);

    return $collectionColaborador;
   }

  public function agregar_colaboradores($pago_extra, $post){
        $result = [];

            if(!empty($post['colaboradores'])){
               $colaboradores_nuevos = $pago_extra->colaboradores()->saveMany($this->ComisionesRepository->_getColaboradores($post['colaboradores']));
               $pago_extra->load("deducciones","acumulados");
               $result  = $this->creandoRelaciones($pago_extra, $colaboradores_nuevos);

             }
              return $result;
  }

  private function creandoRelaciones($comision_creada, $colaboradores_nuevos){

             foreach ($colaboradores_nuevos as &$colaborador_new) {
                      $colaborador_new->each(function ($colaborador) use ($comision_creada){
                                    //Limpiamos por si acaso las relaciones
                                  $colaborador->acumulados_aplicados()->delete();
                                  $colaborador->deducciones_aplicados()->delete();

                                  if(count($comision_creada->acumulados)>0){
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
        }

        return $comision_creada;
    }


   public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
   {
       $colaboradores = ComisionColaborador::where(function($query) use ($clause){

           $this->_filtros($query, $clause);

       });

       if($sidx !== null && $sord !== null){$colaboradores->orderBy($sidx, $sord);}
       if($limit != null){$colaboradores->skip($start)->take($limit);}
       return $colaboradores->get();
   }

   private function _filtros($query, $clause)
   {
       if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
       if(isset($clause['comision_id']) and !empty($clause['comision_id'])){$query->whereComisionId($clause['comision_id']);}
       if(isset($clause['estado']) and !empty($clause['estado'])){$query->whereEstado($clause['estado']);}
       if(isset($clause['colaborador_id']) and !empty($clause['colaborador_id'])){$query->whereColaboradorId($clause['colaborador_id']);}
   }

   public function count($clause = array())
   {
       $colaboradores = ComisionColaborador::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        });

       return $colaboradores->count();
   }

}
