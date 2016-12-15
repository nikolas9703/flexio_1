<?php

use \Flexio\Migration\Migration;

class PoliticasAnticipos extends Migration
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
      $rows = [
          [
            'key'  => 7,
            'valor' => 'por_aprobar-aprobado',
            'etiqueta' => 'Por aprobar - Aprobado',
            'tipo' => 'anticipo',
            'estado1' => 'por_aprobar',
            'estado2' => 'aprobado',
            'orden' => 1
          ],
       ];

      $this->insert('ptr_transacciones_catalogo', $rows);
    }
}
