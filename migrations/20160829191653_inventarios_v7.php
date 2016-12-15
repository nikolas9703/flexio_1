<?php

use \Flexio\Migration\Migration;

class InventariosV7 extends Migration
{

    public function up()
    {
        $rows = [
            ["id_cat"=>9, "id_campo"=>0, "valor"=>"estado", "etiqueta"=>"Por aprobar"]
        ];

        $this->insert("inv_inventarios_cat", $rows);
    }

    public function down()
    {
        $this->execute("DELETE FROM inv_inventarios_cat WHERE id_cat = 9");
    }

}
