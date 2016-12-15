<?php

use \Flexio\Migration\Migration;

class InsertIdentificacionClienteCatalogoMigration extends Migration
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
    public function up() {
        $data = [
            ['key'=>'22','valor'=>'Natural','etiqueta'=>'natural', 'tipo' => 'identificacion', 'orden' => '1'],
            ['key'=>'23','valor'=>'Jur&iacute;dico','etiqueta'=>'juridico', 'tipo' => 'identificacion', 'orden' => '2'],
        ];
        $this->insert('cli_clientes_catalogo', $data);
    }
}
