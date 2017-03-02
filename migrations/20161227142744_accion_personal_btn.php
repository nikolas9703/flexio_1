<?php

use \Flexio\Migration\Migration;

class AccionPersonalBtn extends Migration
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
        $this->execute('UPDATE `inp_incapacidades_campos` SET `atributos`=\'{"flow-btn":"", "class":"btn btn-outline {{fileClassBtn1}} btn-block fileinput-button", "ng-bind-html":"fileBtn1","ng-click":"setSelectedBtn(1)" }\' WHERE  `id_campo`=10;');
        $this->execute('UPDATE `inp_incapacidades_campos` SET `atributos`=\'{"flow-btn":"", "class":"btn btn-outline {{fileClassBtn2}} btn-block fileinput-button", "ng-bind-html":"fileBtn2","ng-click":"setSelectedBtn(2)" }\' WHERE  `id_campo`=13;');
    }
}