<?php

use \Flexio\Migration\Migration;

class ClientesV7 extends Migration
{
    public function up()
    {
        $this->execute("LOAD DATA LOCAL INFILE './migrations/distritos.csv' INTO TABLE geo_distritos CHARACTER SET UTF8 FIELDS TERMINATED BY ',' (id, nombre, provincia_id)");
    }

    public function down()
    {
        $this->execute('DELETE FROM geo_distritos');
    }
}
