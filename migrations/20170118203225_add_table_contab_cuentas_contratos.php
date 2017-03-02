<?php

use Phinx\Migration\AbstractMigration;

class AddTableContabCuentasContratos extends AbstractMigration
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
          $exist = $this->hasTable('contab_cuenta_contratos');
          if(!$exist) {

            $contab_contrato = $this->table('contab_cuenta_contratos');
            $contab_contrato
                    ->addColumn('cuenta_id', 'integer')
                    ->addColumn('empresa_id', 'integer')
                    ->addColumn('created_at', 'datetime')
                    ->addColumn('updated_at', 'datetime')
                    ->addColumn('tipo', 'string')
                    ->save();
        }

    }
}
