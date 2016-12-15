<?php

use \Flexio\Migration\Migration;

class AddIntProyectoActividad extends Migration
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
            [
              'identificador'    => 'tipo_propuesta_proyecto',
              'valor'  => 'propuesta',
              'etiqueta'  => 'Propuesta',
              'orden'  => 1,
              'activo'  => 1
            ],
            [
              'identificador'    => 'tipo_propuesta_proyecto',
              'valor'  => 'cumplimiento',
              'etiqueta'  => 'Cumplimiento',
              'orden'  => 2,
              'activo'  => 1
            ]
            ,
            [
              'identificador'    => 'tipo_propuesta_proyecto',
              'valor'  => 'anticipo',
              'etiqueta'  => 'Anticipo',
              'orden'  => 3,
              'activo'  => 1
            ]
        ];

        $this->insert('mod_catalogos', $rows);
        
        $tabla = $this->table('int_proyecto_actividad');
        $tabla->addColumn('tipo_fianza', 'string', array('limit' => 100, 'null' => true))
            ->save();
        
        
    }

    public function down()
    {
        $table = $this->table('int_proyecto_actividad');
        $table  ->removeColumn('tipo_fianza')
                ->save();
        $this->execute("DELETE FROM mod_catalogos WHERE identificador = 'tipo_propuesta_proyecto'");
    }
}
