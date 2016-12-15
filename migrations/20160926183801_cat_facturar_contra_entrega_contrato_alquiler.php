<?php

use \Flexio\Migration\Migration;

class CatFacturarContraEntregaContratoAlquiler extends Migration
{
    public function up()
    {
        //Nuevos valores en catalogo
        $rows = [
            ['nombre'  => 'No', 'valor' => 'no', 'tipo' => 'pregunta_cerrada'],
            ['nombre'  => 'Si', 'valor' => 'si', 'tipo' => 'pregunta_cerrada'],
        ];
        $this->insert('conalq_contratos_alquiler_catalogos', $rows);

        //columna nueva en tabla de contrato alquiler
        $tabla = $this->table('conalq_contratos_alquiler');
        $column = $tabla->hasColumn('facturar_contra_entrega_id');
        if (!$column) {
          $tabla->addColumn('facturar_contra_entrega_id', 'integer', array('limit' => 10, 'null' => true, 'after' => 'corte_facturacion_id'))->update();
        }
    }

    public function down()
    {
       $this->query("DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor='no'");
       $this->query("DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor='si'");

       //borrar columna
       $tabla = $this->table('conalq_contratos_alquiler');
       $column = $tabla->hasColumn('facturar_contra_entrega_id');
       if ($column) {
         $tabla->removeColumn('facturar_contra_entrega_id');
       }
    }
}
