<?php

use \Flexio\Migration\Migration;

class PoliticasModulos extends Migration
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
        // se agrega la nueva columna para la politica
        $politica = $this->table('ptr_transacciones');
        $column = $politica->hasColumn('politica_estado');
        if(!$column){
             $politica->addColumn('politica_estado', 'integer');
             $politica->update();
        }

        /// se crea el catalogo de politicas
        /// se elimina la tabla del catalogo
        $this->execute('TRUNCATE TABLE ptr_transacciones_catalogo;');

        $catalogo = $this->table('ptr_transacciones_catalogo');

        $column = $catalogo->hasColumn('estado1');
        if (!$column) {
            $catalogo->addColumn('estado1','string', array('limit' => 20))->update();
        }

        $column = $catalogo->hasColumn('estado2');
        if (!$column) {
            $catalogo->addColumn('estado2','string', array('limit' => 20))->update();
        }

        $rows = [
            [
              'id'    => 1,
              'key'  => 1,
              'valor' => '1-2',
              'etiqueta' => 'Por aprobar - por facturar',
              'tipo' => 'orden_compra',
              'estado1' => '1',
              'estado2' => '2',
              'orden' => 1
            ],
            [
                'id'    => 2,
                'key'  => 2,
                'valor' => '1-2',
                'etiqueta' => 'Por aprobar - En cotizacion',
                'tipo' => 'pedido',
                'estado1' => '1',
                'estado2' => '2',
                'orden' => 1
            ],
            [
                'id'    => 3,
                'key'  => 3,
                'valor' => '13-14',
                'etiqueta' => 'Por aprobar - por pagar',
                'tipo' => 'factura_compra',
                'estado1' => '13',
                'estado2' => '14',
                'orden' => 1
            ],
            [
                'id'    => 4,
                'key'  => 4,
                'valor' => 'por_aprobar-por_aplicar',
                'etiqueta' => 'Por aprobar - por pagar',
                'tipo' => 'pago',
                'estado1' => 'por_aprobar',
                'estado2' => 'por_aplicar',
                'orden' => 1
            ]
        ];

        $this->insert('ptr_transacciones_catalogo', $rows);
    }

    public function down(){}
}
