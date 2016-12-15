<?php

use \Flexio\Migration\Migration;

class PagosV10 extends Migration
{
    public function up()
    {
        $conn = $this->getAdapter()->getConnection();
        $quotedString = $conn->quote('key');
        $this->execute("UPDATE flexio_catalogos SET valor = 'Por aprobar', etiqueta = 'por_aprobar' where `key`='3' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'Por aplicar', etiqueta = 'por_aplicar' where `key`='4' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'Aplicado', etiqueta = 'aplicado' where `key`='5' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'Anulado', etiqueta = 'anulado' where `key`='6' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'Cheque en transito', etiqueta = 'cheque_en_transito' where `key`='7' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'Efectivo', etiqueta = 'efectivo' where `key`='8' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'Cr&eacute;dito a favor', etiqueta = 'credito_favor' where `key`='9' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'Cheque', etiqueta = 'cheque' where `key`='10' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'Tarjeta de cr&eacute;dito', etiqueta = 'tarjeta_credito' where `key`='11' and modulo ='pagos'");
        $this->execute("UPDATE flexio_catalogos SET valor = 'ACH', etiqueta = 'ach' where `key`='12' and modulo ='pagos'");
    }

    public function down()
    {
        //... sin rollback
    }
}
