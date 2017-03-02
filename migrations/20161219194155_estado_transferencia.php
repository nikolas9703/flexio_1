<?php

use \Flexio\Migration\Migration;

class EstadoTransferencia extends Migration
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
          $rows = [
              ['key' => '1', 'valor' => 'Por aprobar', 'etiqueta' => 'por_aprobar', 'tipo' => 'estado', 'modulo' => 'transferencias', 'orden' => '1'],
              ['key' => '2', 'valor' => 'Aprobado', 'etiqueta' => 'aprobado', 'tipo' => 'estado', 'modulo' => 'transferencias', 'orden' => '2'],
              ['key' => '3', 'valor' => 'En transito', 'etiqueta' => 'en_transito', 'tipo' => 'estado', 'modulo' => 'transferencias', 'orden' => '3'],
              ['key' => '4', 'valor' => 'Realizado', 'etiqueta' => 'realizado', 'tipo' => 'estado', 'modulo' => 'transferencias', 'orden' => '4'],
              ['key' => '5', 'valor' => 'Anulado', 'etiqueta' => 'anulado', 'tipo' => 'estado', 'modulo' => 'transferencias', 'orden' => '5']
          ];

          $this->insert('flexio_catalogos', $rows);
      }

      public function down()
      {
          $this->execute("DELETE FROM flexio_catalogos WHERE modulo = 'transferencias'");
      }
}
