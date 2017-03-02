<?php
use \Flexio\Migration\Migration;
class CentroContableIdAnticipo extends Migration
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
        $table = $this->table('atc_anticipos');
        if(!$table->hasColumn("centro_contable_id")){
             $table->addColumn('centro_contable_id', 'integer', array('after' => 'referencia'))
            ->update();
        }    
           
    }
}
