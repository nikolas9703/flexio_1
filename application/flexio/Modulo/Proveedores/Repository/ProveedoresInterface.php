<?php
namespace Flexio\Modulo\Proveedores\Repository;

interface ProveedoresInterface
{
    public function get($clause, $sidx, $sord, $limit, $start);
    public function getCollectionExportar($proveedores);
    public function restar_credito($proveedor_id, $monto);
    public function sumar_credito($proveedor_id, $monto);
}
