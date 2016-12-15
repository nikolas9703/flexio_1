<?php

use \Flexio\Migration\Migration;

class CatalogoColV3 extends Migration
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
          $this->execute("UPDATE col_colaboradores_campos SET requerido=1 where id_campo=106;");
          $this->execute("UPDATE col_colaboradores_campos SET requerido=1, atributos='{\"class\":\"no_cuenta\"}' where id_campo=109;");
          $this->execute("UPDATE col_colaboradores_campos SET requerido=1 where id_campo=108;");
          $this->execute("UPDATE col_colaboradores_campos SET atributos='{\"class\":\"chosen-select form-control\"}' where id_campo=38;");
          $this->execute("UPDATE col_colaboradores_campos SET atributos='{\"class\":\"chosen-select form-control\", \"placeholder_text_single\":\"Seleccione\"}' where id_campo=39;");
      }
}
