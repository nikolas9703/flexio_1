<?php

use \Flexio\Migration\Migration;

class CreatePlanFunc extends Migration
{


     private function actualizar_planilla(){


      $exists = $this->schema->hasTable('pln_planilla');

       if($exists){

          $tabla2 = $this->table('pln_planilla');
           $tabla2->addColumn('cuenta_debito_id','integer',array('after'=>'pasivo_id'))
            ->addColumn('created_at', 'datetime',array('after'=>'codigo'))
            ->addColumn('updated_at', 'datetime',array('after'=>'codigo'))
           ->update();

       }
     }

     private function crear_planilla_centros(){
       $exists = $this->schema->hasTable('pln_planilla_centros');
       if(!$exists){
         $this->schema->create('pln_planilla_centros', function(Illuminate\Database\Schema\Blueprint $table) {
             $table->integer('centro_contable_id')->unsigned()->index();
             $table->integer('planilla_id')->unsigned()->index();
         });
       }
     }

     private function borrar_deducciones_constructores(){
       $exists = $this->schema->hasTable('pln_config_deducciones_constructores');
       if($exists){
          $this->dropTable('pln_config_deducciones_constructores');
       }
     }

     private function borrar_planilla_beneficios(){
       $exists =  $this->schema->hasTable('pln_planilla_beneficios');
       if($exists){
          $this->dropTable('pln_planilla_beneficios');
       }
     }

     private function borrar_planilla_campos(){
       $exists = $this->schema->hasTable('pln_planilla_campos');

        if($exists){
          $this->dropTable('pln_planilla_campos');
       }
     }

     private function remover_columna_rata(){
       $exists = $this->schema->hasTable('pln_pagadas_colaborador.rata');
       if($exists){
         $table  = $this->table('pln_pagadas_colaborador');
         $table->removeColumn('rata')->save();
       }
     }
     private function remover_columna_fecha_inicial(){
         $exists =$this->schema->hasTable('pln_pagadas_colaborador.fecha_inicial');
         if($exists){
         $table  = $this->table('pln_pagadas_colaborador');
         $table->removeColumn('fecha_inicial')->save();
        }
     }


     public function change()
     {
         /*$this->actualizar_planilla();
        $this->crear_planilla_centros();


         $this->remover_columna_rata();
         $this->remover_columna_fecha_inicial();
         $this->borrar_deducciones_constructores();
         $this->borrar_planilla_beneficios();
         $this->borrar_planilla_campos();*/
      }
}
