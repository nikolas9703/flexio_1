<?php

use \Flexio\Migration\Migration;

class Subcontratos extends Migration
{
    private function _table_subcontratos()
    {
        $table = $this->table('sub_subcontratos');
        $table
                ->addColumn('uuid_subcontrato', 'binary', array('limit' => 16))
                ->addColumn('codigo', 'string', array('limit' => 100))
                ->addColumn('fecha_inicio', 'date')
                ->addColumn('fecha_final', 'date')
                ->addColumn('referencia', 'string', array('limit' => 300))
                ->addColumn('proveedor_id', 'integer', array('limit' => 10))
                ->addColumn('centro_id', 'integer', array('limit' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('monto_subcontrato', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
    }
    
    private function _table_subcontratos_montos()
    {
        $table = $this->table('sub_subcontratos_montos');
        $table
                ->addColumn('cuenta_id', 'integer', array('limit' => 10))
                ->addColumn('descripcion', 'string', array('limit' => 200))
                ->addColumn('monto', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('subcontrato_id', 'integer', array('limit' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
    }
    
    private function _table_subcontratos_tipos()
    {
        $table = $this->table('sub_subcontratos_tipos');
        $table
                ->addColumn('subcontrato_id', 'integer', array('limit' => 10))
                ->addColumn('monto', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('porcentaje', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('cuenta_id', 'integer', array('limit' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('tipo', 'string', array('limit' => 100))
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
    }
    
    private function _table_adendas_subcontratos()
    {
        $table = $this->table('sub_adendas_subcontratos');
        $table
                ->addColumn('uuid_adenda', 'binary', array('limit' => 16))
                ->addColumn('codigo', 'string', array('limit' => 100))
                ->addColumn('subcontrato_id', 'integer', array('limit' => 10))
                ->addColumn('usuario_id', 'integer', array('limit' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('fecha', 'datetime')
                ->addColumn('referencia', 'string', array('limit' => 200))
                ->addColumn('comentario', 'string', array('limit' => 300))
                ->addColumn('monto_adenda', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('monto_acumulado', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
    }
    
    private function _table_adendas_montos()
    {
        $table = $this->table('sub_adendas_montos');
        $table
                ->addColumn('adenda_id', 'integer', array('limit' => 10))
                ->addColumn('descripcion', 'string', array('limit' => 300))
                ->addColumn('monto', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
    }
        
    public function up()
    {
        $this->_table_subcontratos();
        $this->_table_subcontratos_montos();
        $this->_table_subcontratos_tipos();
        $this->_table_adendas_subcontratos();
        $this->_table_adendas_montos();
    }

    public function down()
    {
        $this->dropTable('sub_subcontratos');
        $this->dropTable('sub_subcontratos_montos');
        $this->dropTable('sub_subcontratos_tipos');
        $this->dropTable('sub_adendas_subcontratos');
        $this->dropTable('sub_adendas_montos');
    }
}
