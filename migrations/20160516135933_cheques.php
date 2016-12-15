<?php

use \Flexio\Migration\Migration;

class Cheques extends Migration
{
    public function up()
    {
        $this->_table_cheques();
        $this->_table_cheques_catalogo();
        $this->_table_chequeras();
    }

    public function down()
    {
        $this->dropTable('che_cheques');
        $this->dropTable('che_cheques_catalogo');
        $this->dropTable('che_chequeras');
    }
    
    private function _table_cheques()
    {
        $table = $this->table('che_cheques');
        $table
                ->addColumn('uuid_cheque', 'binary', array('limit' => 16))
                ->addColumn('numero', 'string', array('limit' => 100))
                ->addColumn('monto', 'decimal', array('scale' => 2, 'precision' => 10))
                
                ->addColumn('chequera_id', 'integer', array('limit' => 10))
                ->addColumn('pago_id', 'integer', array('limit' => 10))
                ->addColumn('fecha_cheque', 'datetime')
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                ->addColumn('estado_id', 'integer', array('limit' => 10))
                
                ->save();
    }
    
    private function _table_cheques_catalogo()
    {
        $table  = $this->table('che_cheques_catalogo');
        $table  ->addColumn('valor', 'string', array('limit' => 100))
                ->addColumn('etiqueta', 'string', array('limit' => 100))
                ->save();
        
        // Estados de una razon de ajuste
        $rows = [
            [
                'id'        => 1,
                'valor'     => 'por_imprimir',
                'etiqueta'  => 'Por Imprimir'
            ],[
                'id'        => 2,
                'valor'     => 'impreso',
                'etiqueta'  => 'Impreso'
            ],[
                'id'        => 3,
                'valor'     => 'anulado',
                'etiqueta'  => 'Anulado'
            ]
        ];
        
        $this->insert('che_cheques_catalogo', $rows);
    }
    
    private function _table_chequeras()
    {
        $table  = $this->table('che_chequeras');
        $table  ->addColumn('uuid_chequera', 'binary', array('limit' => 16))
                ->addColumn('nombre', 'string', array('limit' => 100))
                ->addColumn('cuenta_banco_id', 'integer', array('limit' => 10))
                ->addColumn('cheque_inicial', 'string', array('limit' => 50))
                ->addColumn('cheque_final', 'string', array('limit' => 50))
                ->addColumn('proximo_cheque', 'string', array('limit' => 50))
                
                ->addColumn('ancho', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('alto', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('izquierda', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('derecha', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('arriba', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('abajo', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('posicion', 'decimal', array('scale' => 2, 'precision' => 10))
                
                ->addColumn('estado', 'integer', array('limit' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                ->addColumn('created_by', 'integer', array('limit' => 10))
                ->save();
    }
    
    
}
