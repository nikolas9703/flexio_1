<?php

use \Flexio\Migration\Migration;

class AddInteresesAsegurados extends Migration
{
  private function _insertCatalogo()
  {//'En negociaci&oacute;n'
    $this->execute('TRUNCATE TABLE int_intereses_asegurados_cat;');
    $data = [
        ['id_cat'=>1,'id_campo'=>0,'valor'=>'articulo','etiqueta'=>'Articulo'],
        ['id_cat'=>2,'id_campo'=>0,'valor'=>'carga','etiqueta'=>'Carga'],
        ['id_cat'=>3,'id_campo'=>0,'valor'=>'casco_aereo','etiqueta'=>'Casco aereo'],
        ['id_cat'=>4,'id_campo'=>0,'valor'=>'casco_maritimo','etiqueta'=>'Casco maritimo'],
        ['id_cat'=>5,'id_campo'=>0,'valor'=>'persona','etiqueta'=>'Persona'],
        ['id_cat'=>6,'id_campo'=>0,'valor'=>'proyecto_actividad','etiqueta'=>'Proyecto/Actividad'],
        ['id_cat'=>7,'id_campo'=>0,'valor'=>'ubicacion','etiqueta'=>'Ubicacion'],
        ['id_cat'=>8,'id_campo'=>0,'valor'=>'vehiculo','etiqueta'=>'Vehiulo']
    ];
    $this->insert('int_intereses_asegurados_cat', $data);
  }



  public function up()
    {
      $exist = $this->hasTable('int_intereses_asegurados_cat');
      if(!$exist) {
        $this->schema->create('int_intereses_asegurados_cat', function ($table) {
            $table->increments('id_cat');
            $table->integer('id_campo');
            $table->string('valor');
            $table->string('etiqueta');
         });
      }
        $this->_insertCatalogo();
   }


}
