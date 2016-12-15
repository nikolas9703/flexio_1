<?php

use \Flexio\Migration\Migration;

class DeleteClienteCrear32 extends Migration
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

      //    $recurso_id = $this->execute('SELECT id FROM recursos WHERE nombre = "clientes/crear"');
          /*$permisos_ids = $this->fetchAll('SELECT id FROM permisos WHERE recurso_id = '.$recurso_id);
          $roles_permisos_ids =  $this->fetchAll('SELECT id FROM roles_permisos WHERE permiso_id IN '.$permisos_ids);*/
  //$roles_permisos_ids = 19,20);

         $this->execute('DELETE FROM roles_permisos WHERE permiso_id IN (164,165)');
          $this->execute('DELETE FROM permisos WHERE recurso_id=131');
          $this->execute('DELETE FROM recursos WHERE id=131');

    }
}
