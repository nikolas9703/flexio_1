<?php

use \Flexio\Migration\Migration;

class TblaMigrationColPanel extends Migration
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
           'id_panel'    => '47',
           'id_campo'  => '218'
         ],
         [
           'id_panel'    => '47',
           'id_campo'  => '219'
         ],
         [
           'id_panel'    => '47',
           'id_campo'  => '220'
         ],
         [
           'id_panel'    => '47',
           'id_campo'  => '221'
         ],
         [
           'id_panel'    => '47',
           'id_campo'  => '222'
         ],
         [
           'id_panel'    => '47',
           'id_campo'  => '223'
         ],
         [
            'id_panel'    => '47',
            'id_campo'  => '224'
          ],
          [
            'id_panel'    => '47',
            'id_campo'  => '225'
          ],
         ];

         $this->insert('mod_panel_campos', $rows);




     }
}
