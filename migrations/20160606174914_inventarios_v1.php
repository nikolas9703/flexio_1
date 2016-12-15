<?php

use \Flexio\Migration\Migration;

class InventariosV1 extends Migration
{
    public function up()
    {
        $this->execute("UPDATE `inv_inventarios_campos` SET `etiqueta`='Unidad de Medida' WHERE `id_campo`='7';");
    }
    
    public function down()
    {
        //...
    }
}
