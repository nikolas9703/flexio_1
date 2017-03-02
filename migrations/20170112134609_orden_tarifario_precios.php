<?php

use \Flexio\Migration\Migration;

class OrdenTarifarioPrecios extends Migration
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
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='1' , `valor`='tarifa_4_horas' WHERE `tipo` = 'tarifa' and `valor`='4_horas';");
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='2', `valor`='hora' WHERE `tipo` = 'tarifa' and `valor`='por_hora';");
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='3' WHERE `tipo` = 'tarifa' and `valor`='diario';");
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='4', `valor`='tarifa_6_dias' WHERE `tipo` = 'tarifa' and `valor`='6_dias';");
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='5' WHERE `tipo` = 'tarifa' and `valor`='semanal';");
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='6', `valor`='tarifa_15_dias' WHERE `tipo` = 'tarifa' and `valor`='15_dias';");
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='7', `valor`='tarifa_28_dias'  WHERE `tipo` = 'tarifa' and `valor`='28_dias';");
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='8', `valor`='tarifa_30_dias' WHERE `tipo` = 'tarifa' and `valor`='30_dias';");
        $this->execute("UPDATE `conalq_contratos_alquiler_catalogos` SET `orden`='9' WHERE `tipo` = 'tarifa' and `valor`='mensual';");

    }
}
