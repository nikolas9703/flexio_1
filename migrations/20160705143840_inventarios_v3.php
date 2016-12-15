<?php

use \Flexio\Migration\Migration;

class InventariosV3 extends Migration
{
    
    public function up(){
        
        $this->execute("UPDATE inv_inventarios_cat SET valor = 'estado' WHERE id_cat = '1'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = 'estado' WHERE id_cat = '2'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = '1' WHERE id_cat = '3'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = 'tipo' WHERE id_cat = '4'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = 'tipo' WHERE id_cat = '5'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = 'tipo' WHERE id_cat = '6'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = 'tipo' WHERE id_cat = '7'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = 'tipo' WHERE id_cat = '8'");
        
    }
    
    public function down(){
        
        $this->execute("UPDATE inv_inventarios_cat SET valor = '' WHERE id_cat = '1'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = '' WHERE id_cat = '2'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = '1' WHERE id_cat = '3'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = '' WHERE id_cat = '4'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = '' WHERE id_cat = '5'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = '' WHERE id_cat = '6'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = '' WHERE id_cat = '7'");
        $this->execute("UPDATE inv_inventarios_cat SET valor = '' WHERE id_cat = '8'");
        
    }
    
}
