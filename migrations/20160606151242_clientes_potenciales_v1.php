<?php

use \Flexio\Migration\Migration;

class ClientesPotencialesV1 extends Migration
{
    public function up()
    {
        $this->execute("UPDATE `cp_clientes_potenciales_campos` SET `atributos`='{ \"class\":\"form-control telefono\", \"data-addon-icon\":\"fa-phone\", \"data-inputmask\":\"\'mask\': \'999-9999\', \'greedy\':true\"}' WHERE `id_campo`='2'");
        $this->execute("UPDATE `cp_clientes_potenciales_campos` SET `atributos`='{\"class\":\"form-control email\",\"data-addon-text\":\"@\"}' WHERE `id_campo`='3'");
    }
    
    public function down()
    {
        //...
    }
}
