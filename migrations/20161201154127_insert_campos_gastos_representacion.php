<?php

use Phinx\Migration\AbstractMigration;

class InsertCamposGastosRepresentacion extends AbstractMigration
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
        // inserting only one row
        $singleRow = [
            'nombre_campo'    => 'gasto_de_representacion',
            'etiqueta'    => 'Gasto de Representaci&oacuten',
            'id_tipo_campo'    => '22',
            'estado'    => 'activo',
            'atributos'    => '{"data-addon-text":"$","class":"form-control salario_mensual"}',
            'contenedor'    => 'div',
            'posicion'  => '60'
        ];
        
        

        $table = $this->table('col_colaboradores_campos');
        $table->insert($singleRow);
        $table->saveData();
        $last_id = $this->adapter->getConnection()->lastInsertId();
        $panel_id = $this->fetchRow("SELECT id_panel FROM mod_paneles where panel = 'Datos Profesionales'")[0];
        
        $panel_campos = [
            'id_panel' => $panel_id,
            'id_campo' => $last_id
        ];
        
        $table2 = $this->table('mod_panel_campos');
        $table2->insert($panel_campos);
        $table2->saveData();
        

    }


}
