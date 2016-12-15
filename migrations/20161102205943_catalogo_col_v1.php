<?php

use \Flexio\Migration\Migration;

class CatalogoColV1 extends Migration
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
     public function up()
     {

          $this->execute("DELETE FROM mod_catalogo_modulos  WHERE id_cat = '151'");

         $data =
         [
             ['id_campo'=>107,'id_cat'=>151,'id_modulo'=>7]
         ];
         $this->insert('mod_catalogo_modulos', $data);
     }
}
