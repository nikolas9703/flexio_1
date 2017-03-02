<?php

use \Flexio\Migration\Migration;

class DocumentosV6 extends Migration
{
    public function up()
    {
        $conn = $this->getAdapter()->getConnection();
        $q0 = $conn->quote('Flexio\Modulo\FacturasCompras\Models\FacturaCompra');
        $q1 = $conn->quote('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra');
        $q2 = $conn->quote('Flexio\Modulo\Pedidos\Models\Pedidos');
        $q3 = $conn->quote('Flexio\Modulo\NotaDebito\Models\NotaDebito');
        $q4 = $conn->quote('Flexio\Modulo\SubContratos\Models\SubContrato');
        $this->execute("UPDATE comentarios SET centro_contable_id = (
            SELECT CASE
            WHEN comentable_type = $q0 THEN (SELECT faccom_facturas.centro_contable_id FROM faccom_facturas WHERE faccom_facturas.id = comentarios.comentable_id)
            WHEN comentable_type = $q1 THEN (SELECT cen_centros.id FROM cen_centros, ord_ordenes WHERE cen_centros.uuid_centro=ord_ordenes.uuid_centro AND ord_ordenes.id = comentarios.comentable_id)
            WHEN comentable_type = $q2 THEN (SELECT cen_centros.id FROM cen_centros, ped_pedidos WHERE cen_centros.uuid_centro=ped_pedidos.uuid_centro AND ped_pedidos.id = comentarios.comentable_id)
            WHEN comentable_type = $q3 THEN (SELECT centro_contable_id FROM compra_nota_debitos WHERE compra_nota_debitos.id = comentarios.comentable_id)
            WHEN comentable_type = $q4 THEN (SELECT centro_id FROM sub_subcontratos WHERE sub_subcontratos.id = comentarios.comentable_id)
            ELSE 0 END
        )
        where id > 0;");
    }

    public function down()
    {
        $this->execute("UPDATE comentarios SET centro_contable_id = 0 WHERE id > 0");
    }
}
