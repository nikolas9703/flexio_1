<?php

use \Flexio\Migration\Migration;

class ChangeCamposComision extends Migration
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
        $this->execute("UPDATE com_comisiones_campos SET tabla_relacional='activos_cuentas_extra' WHERE id_campo = '4'");
        $this->execute("UPDATE com_comisiones_campos SET requerido='0' WHERE id_campo = '8'");
        $this->execute("UPDATE com_comisiones_campos SET requerido='0' WHERE id_campo = '9'");

    }
}
