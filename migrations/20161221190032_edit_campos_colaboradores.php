<?php

use \Flexio\Migration\Migration;

class EditCamposColaboradores extends Migration
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
    public function up() {
        // inserting only one row
        $singleRow = [
            'nombre_campo'    => 'digito_verificador',
            'etiqueta'    => 'Digito Verificador',
            'id_tipo_campo'    => '14',
            'estado'    => 'activo',
            'atributos'    => '{\"data-addon-text\":\"$\",\"class\":\"form-control\",\"codigo_verificador\" \"}',
            'contenedor'    => 'div',
            'posicion'  => '20'
        ];
        
        $last_id = $this->fetchAll('SELECT * FROM col_colaboradores_campos ORDER BY id_campo DESC LIMIT 1');        
        $total_id = intval($last_id[0]['id_campo'])+1;
        $panel_id = '46';
       
         $panel_campos = [
             'id_panel' => $panel_id,
             'id_campo' => $total_id
             ];

        $table = $this->table('col_colaboradores_campos');
        $table->insert($singleRow);
        $table->saveData();
        
        $table2 = $this->table('mod_panel_campos');
        $table2->insert($panel_campos);
        $table2->saveData();
    }
}
