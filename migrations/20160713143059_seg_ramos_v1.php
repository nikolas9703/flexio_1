<?php

use \Flexio\Migration\Migration;

class SegRamosV1 extends Migration
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
        
        $table = $this->table('seg_ramos');
        $table  ->addColumn('codigo_ramo', 'string', array('limit' => 3))
                ->addColumn('id_tipo_int_asegurado', 'integer', array('limit' => 11))
                ->addColumn('id_tipo_poliza', 'integer', array('limit' => 11))
                ->removeColumn('formulario_solic')
                ->save();
        
        $table = $this->table('seg_ramos_tipo_interes');
        $table  ->addColumn('nombre', 'string', array('limit' => 100))
                
                //no functionals requeriments
                ->addColumn('created_by', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                
                ->save();
        
        $table = $this->table('seg_ramos_tipo_poliza');
        $table  ->addColumn('nombre', 'string', array('limit' => 100))
                
                //no functionals requeriments
                ->addColumn('created_by', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                
                ->save();
        
        $rows = [
            [
              'id'    => 1,
              'nombre'  => utf8_decode('Artículo')
            ],
            [
              'id'    => 2,
              'nombre'  => 'Carga'
            ],
            [
              'id'    => 3,
              'nombre'  => utf8_decode('Casco Aéreo')
            ],
            [
              'id'    => 4,
              'nombre'  => utf8_decode('Casco marítimo')
            ],
            [
              'id'    => 5,
              'nombre'  => 'Persona'
            ],
            [
              'id'    => 6,
              'nombre'  => 'Proyecto'
            ],
            [
              'id'    => 7,
              'nombre'  => utf8_decode('Ubicación')
            ],
            [
              'id'    => 8,
              'nombre'  => utf8_decode('Vehículo')
            ]
        ];

        $this->insert('seg_ramos_tipo_interes', $rows);
        
        $rows2 = [
            [
              'id'    => 1,
              'nombre'  => 'Individual'
            ],
            [
              'id'    => 2,
              'nombre'  => 'Colectivo'
            ]
        ];

        $this->insert('seg_ramos_tipo_poliza', $rows2);
        
        
    }

    public function down()
    {
        $table = $this->table('seg_ramos');
        $table  ->removeColumn('codigo_ramo')
                ->removeColumn('id_tipo_int_asegurado')
                ->removeColumn('id_tipo_poliza')
                ->addColumn('formulario_solic', 'string', array('limit' => 3))
                ->save();
        
        $this->dropTable('seg_ramos_tipo_interes');
        $this->dropTable('seg_ramos_tipo_poliza');
    }
}
