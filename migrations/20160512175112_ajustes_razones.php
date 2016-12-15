<?php

use \Flexio\Migration\Migration;

class AjustesRazones extends Migration
{
    private function _insertAjustesRazonesEstados()
    {
        // Estados de una razon de ajuste
        $rows = [
            [
                'id_cat'    => 6,
                'valor'     => 'estado_ajuste_razon',
                'etiqueta'  => 'Activo'
            ],
            [
                'id_cat'    => 7,
                'valor'     => 'estado_ajuste_razon',
                'etiqueta'  => 'Inactivo'
            ],
        ];
        
        $this->insert('aju_ajustes_cat', $rows);
    }
    
    private function _alterTableAjuAjustes()
    {
        $table  = $this->table("aju_ajustes");
        $table
                ->addColumn('razon_id', 'integer', ['limit' => 10])
                //save
                ->save();
    }
    
    public function up()
    {
        $table  = $this->table('aju_ajustes_razones');
        $table
                //ajuste_razon
                ->addColumn('uuid_razon', 'binary', array('limit' => 16))
                ->addColumn('nombre', 'string', array('limit' => 100))
                ->addColumn('descripcion', 'string', array('limit' => 100))
                
                //foreign key - ajuste_razon
                ->addColumn('estado_id', 'integer', array('limit' => 10))
                
                //foreign key - generales
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('created_by', 'integer', array('limit' => 10))
                
                //orm eloquent
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                
                //save()
                ->save();
        
        //inserto nuevas opciones en el catalogo
        $this->_insertAjustesRazonesEstados();
        //agrego la columna en la tabla de ajustes
        $this->_alterTableAjuAjustes();
    }
    
    public function down()
    {
        $this->dropTable('aju_ajustes_razones');
        
        //borro nuevas opciones del catalogo
        $this->execute("DELETE FROM aju_ajustes_cat WHERE id_cat = '6'");
        $this->execute("DELETE FROM aju_ajustes_cat WHERE id_cat = '7'");
        
        //quito la nueva columna de la tabla aju_ajustes
        $table  = $this->table('aju_ajustes');
        $table
                ->removeColumn('razon_id')
                //save()
                ->save();
    }
}
