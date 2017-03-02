<?php

use \Flexio\Migration\Migration;

class CuentaGastoRepresentacion extends Migration
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
        $this->execute("INSERT INTO `col_colaboradores_campos` (`id_campo`, `nombre_campo`, `etiqueta`, `longitud`, `id_tipo_campo`, `estado`, `atributos`, `agrupador_campo`, `contenedor`, `tabla_relacional`, `requerido`, `link_url`, `fecha_cracion`, `posicion`) VALUES (225, 'cuenta_gasto_representacion_id', 'Cuenta Gasto de Representación', NULL, 18, 'activo', '{\"class\":\"chosen-select form-control\", \"placeholder_text_single\":\"Seleccione\"}', NULL, 'div', 'contab_cuentas', NULL, NULL, '2017-02-10 14:12:34', 60);");

        $table =$this->table("col_colaboradores");
        if(!$table->hasColumn("cuenta_gasto_representacion_id")){
            $table
                ->addColumn("cuenta_gasto_representacion_id", "integer")
                ->update();
        }
    }
}
