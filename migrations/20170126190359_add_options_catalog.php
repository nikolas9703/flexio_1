<?php

use \Flexio\Migration\Migration;

class AddOptionsCatalog extends Migration
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
        $count = $this->execute("INSERT INTO mod_catalogos(identificador, valor, etiqueta, orden, activo) VALUES ('Parentesco', 'companero', '". utf8_decode('Compañero') ."(a)', 8, 1),('Parentesco', 'novio', 'Novio(a)', 9, 1)");
        $last_id = $this->adapter->getConnection()->lastInsertId();
        $rows = [];
        $modulo_id = $this->fetchRow("SELECT ID FROM modulos WHERE nombre = 'Colaboradores'");
        $parentesco_id = $this->fetchAll("SELECT * FROM col_colaboradores_campos WHERE nombre_campo = 'parentesco_id'");
        for ($i=0; $i<$count; $i++){
            foreach ($parentesco_id as $parent_id){
            $rows[] = [
                        'id_cat' => $last_id,
                        'id_modulo' => $modulo_id['ID'],
                        'id_campo'  => $parent_id['id_campo'],
                    ];
            }
            $last_id++;
        }
        
       $this->insert('mod_catalogo_modulos', $rows) ;
        
        
        
        
    }
}
