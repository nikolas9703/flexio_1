<?php

use \Flexio\Migration\Migration;

class AddReporteFiscales extends Migration
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
        // inserting multiple rows
        $rows = [
            [
              'tipo'    => 'reporte',
              'etiqueta' => 'formulario_43',
              'valor' => 'Formulario 43',
              'orden'  => '9'
            ],
            [
              'tipo'    => 'reporte',
              'etiqueta' => 'formulario_433',
              'valor' => 'Formulario 433',
              'orden'  => '10'
            ]
        ];

        $this->insert('cat_reporte_financiero', $rows);
    }

    public function down()
    {
        $this->execute('DELETE FROM cat_reporte_financiero WHERE etiqueta IN("formulario_43","formulario_433")');
    }
}
