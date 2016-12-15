<?php

use \Flexio\Migration\Migration;

class SegPlanesCambiosV1 extends Migration
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
    public function up(){
        
        
        $this->table('seg_planes')
                ->changeColumn('comision', 'integer', ['limit'=>8])
                ->changeColumn('sobre_comision', 'integer', ['limit'=>8])
                ->save();
        
    }
    
    public function down(){
        
        $this->table('seg_planes')
                ->changeColumn('comision', 'string', ['limit'=>255,'default'=>''])
                ->changeColumn('sobre_comision', 'string', ['limit'=>255,'default'=>''])
                ->save();
        
    }
}
