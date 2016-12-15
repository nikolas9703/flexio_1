<?php

use \Flexio\Migration\Migration;

class CambiosEstadosAnticipos extends Migration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
     public function change()
     {
          //se actualiza el catalogo para anticipos

         $this->execute("UPDATE flexio_catalogos SET valor='Por aprobar', etiqueta='por_aprobar' where id=1;");
         $this->execute("UPDATE flexio_catalogos SET valor='Aprobado', etiqueta='aprobado' where id=2;");

         //se actualiza los estados de los anticipos
         $this->execute("UPDATE atc_anticipos SET estado='por_aprobar';");

     }
}
