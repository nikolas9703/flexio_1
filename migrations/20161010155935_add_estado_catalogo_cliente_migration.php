<?php

use \Flexio\Migration\Migration;

class AddEstadoCatalogoClienteMigration extends Migration
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
            ['key'=>'22','valor'=>'Por Aprobar','etiqueta'=>'por_aprobar', 'tipo' => 'estado', 'orden' => '3'],
            ['key'=>'23','valor'=>'Bloqueado','etiqueta'=>'bloqueado', 'tipo' => 'estado', 'orden' => '4'],
        ];
        $this->insert('cli_clientes_catalogo', $data);
    }
}
