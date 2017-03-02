<?php

use \Flexio\Migration\Migration;

class AddPermisoProveedorCambioEstado extends Migration
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
        $rows_recusos =  $this->fetchAll('SELECT * FROM `recursos` WHERE nombre LIKE "%proveedores/ver/(:any)%" LIMIT 1');

        if(isset($rows_recusos[0])){
            $recurso_id = $rows_recusos[0]['id'];
            $this->execute("DELETE FROM permisos where nombre ='ver__cambioestadoProveedores'");
            $this->execute("INSERT INTO `permisos` (`nombre`, `recurso_id`) VALUES ('ver__cambioestadoProveedores', $recurso_id)");
        }
    }
}
