<?php

use \Flexio\Migration\Migration;

class DeleteClienteCrearProducction extends Migration
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

         $this->execute('DELETE FROM roles_permisos WHERE permiso_id IN (87)');
          $this->execute('DELETE FROM permisos WHERE recurso_id=7012');
          $this->execute('DELETE FROM recursos WHERE id=7012');

    }
}
