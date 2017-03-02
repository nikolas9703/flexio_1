<?php

use \Flexio\Migration\Migration;

class ClientesV9 extends Migration
{
    public function up()
    {
        $this->execute("LOAD DATA LOCAL INFILE './migrations/corregimientos.csv' INTO TABLE geo_corregimientos CHARACTER SET UTF8 FIELDS TERMINATED BY ',' (id, nombre, distrito_id)");
    }

    public function down()
    {
        $this->execute('DELETE FROM geo_corregimientos');
    }
}
