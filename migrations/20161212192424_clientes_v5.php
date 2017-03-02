<?php

use \Flexio\Migration\Migration;

class ClientesV5 extends Migration
{
    public function up()
    {
        $this->execute("LOAD DATA LOCAL INFILE './migrations/provincias.csv' INTO TABLE geo_provincias CHARACTER SET UTF8 FIELDS TERMINATED BY ',' (id, nombre)");
    }

    public function down()
    {
        $this->execute('DELETE FROM geo_provincias');
    }
}
