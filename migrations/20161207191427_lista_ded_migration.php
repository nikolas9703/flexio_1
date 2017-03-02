<?php

use \Flexio\Migration\Migration;

class ListaDedMigration extends Migration
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

        //borro nuevas opciones del catalogo
        $this->execute("DELETE FROM col_colaboradores_cat WHERE id_cat = '14'");
        $this->execute("DELETE FROM col_colaboradores_cat WHERE id_cat = '15'");

          $this->execute("UPDATE col_colaboradores_cat SET etiqueta='Declaraci&oacute;n conjunta $800'  WHERE id_cat = '13'");


    }
}
