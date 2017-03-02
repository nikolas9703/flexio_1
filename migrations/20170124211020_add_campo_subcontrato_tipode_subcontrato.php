<?php

use \Flexio\Migration\Migration;

class AddCampoSubcontratoTipodeSubcontrato extends Migration
{
    public function up()
    {
      // ---------------------------------------------
      // Verificar si existe tabla de subcontrato
      // ---------------------------------------------
      $exist = $this->hasTable('sub_subcontratos');
      if($exist) {

        $tabla = $this->table('sub_subcontratos');

        //Verificar si el campo ya existe
        $column = $tabla->hasColumn('tipo_subcontrato_id');
        if (!$column) {

          //Agregar campo
          $tabla->addColumn('tipo_subcontrato_id', 'integer', array('limit' => 10, 'after'=>'referencia'))
            ->addIndex(array('tipo_subcontrato_id'))
            ->update();
        }
      }

      // ---------------------------------------------
      // Agregar un campo mas a la tabla de flexio_catalogos
      // ---------------------------------------------
      //Verificar si el campo ya existe
      $column = $tabla->hasColumn('flexio_catalogos');
      if (!$column) {
        //Agregar campo
        //Este campo servira para los casos en donde se filtran datos segun los catalogos que el usuario tenga asignado
        $this->table('flexio_catalogos')->addColumn('con_acceso', 'integer', array('limit' => 10, 'after'=>'orden'))
          ->update();
      }

      // ---------------------------------------------
      // Agregar catalogo para tipo de subcontrato
      // ---------------------------------------------
      $rows = [
          ['key' => '5', 'valor'  => 'Regular', 'etiqueta' => 'regular', 'tipo' => 'tipo_subcontrato', 'modulo' => 'subcontratos', 'orden' => '1', 'con_acceso' => 0],
          ['key' => '6', 'valor'  => 'Honorarios profesionales', 'etiqueta' => 'honorarios_profesionales', 'tipo' => 'tipo_subcontrato', 'modulo' => 'subcontratos', 'orden' => '2', 'con_acceso' => 1],
      ];
      $this->insert('flexio_catalogos', $rows);
    }

    public function down() {
      $exist = $this->hasTable('sub_subcontratos');
      if($exist) {
        //Verificar si el campo ya existe
        $tabla = $this->table('sub_subcontratos');
        $column = $tabla->hasColumn('tipo_subcontrato_id');
        if (!$column) {
          //Eliminar campo
          $this->table('sub_subcontratos')->removeColumn('atributo_id')->save();
        }

        //Eliminar catalogo catalogo para tipo de subcontrato
        $this->execute("DELETE FROM flexio_catalogos WHERE modulo = 'subcontratos' and `key` in (5,6)");
      }
    }
}
