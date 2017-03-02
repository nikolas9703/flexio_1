<?php

use Phinx\Migration\AbstractMigration;

class AddTableDistrSalario extends AbstractMigration {

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
    public function up() {
         $exists = $this->hasTable('col_distribucion_salario');
        if (!$exists) {
            $distr_salario = $this->table('col_distribucion_salario');
            $distr_salario->addColumn('colaborador_id', 'integer')
                    ->addColumn('cuenta_costo_id', 'integer')
                ->addColumn('centro_contable_id', 'integer')
                ->addColumn('prcentaje_distribucion', 'integer')
                ->addColumn('monto_asignado', 'decimal')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
        }
        
        
    }

}
