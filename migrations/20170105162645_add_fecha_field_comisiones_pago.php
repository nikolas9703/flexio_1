<?php

use \Flexio\Migration\Migration;

class AddFechaFieldComisionesPago extends Migration
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
        $this->execute('INSERT INTO `com_comisiones_campos` (  `nombre_campo`, `etiqueta`, `longitud`, `id_tipo_campo`, `estado`, `atributos`, `agrupador_campo`, `contenedor`, `tabla_relacional`, `requerido`, `link_url`, `fecha_cracion`, `posicion`) VALUES
	( \'fecha_programada_pago\', \'Fecha programada de pago\', NULL, 22, \'activo\', \'{"data-columns":"1",  "data-addon-icon":"fa-calendar",  "class":"form-control fecha-programada-pago","data-format":"DD-MM-YYYY"}\', NULL, \'div\', NULL, NULL, NULL, \'2016-10-24 10:59:27\', 9);');
        $idcampos=$this->getAdapter()->getConnection()->lastInsertId();
        $this->execute("INSERT INTO  `mod_panel_campos` (`id_panel`, `id_campo`) VALUES ('75', '$idcampos')");
    }
}
