<?php

use \Flexio\Migration\Migration;

class ChangeCamposComision2 extends Migration
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

      $this->execute("UPDATE com_comisiones_campos SET posicion='11' WHERE id_campo = '15'");
      $this->execute("UPDATE com_comisiones_campos SET posicion='12' WHERE id_campo = '14'");
      $this->execute("UPDATE com_comisiones_campos SET posicion='13' WHERE id_campo = '11'");
      $this->execute("UPDATE com_comisiones_campos SET posicion='14' WHERE id_campo = '12'");
      $this->execute("UPDATE com_comisiones_campos SET posicion='15' WHERE id_campo = '16'");
    }
}
