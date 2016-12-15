<?php
namespace Flexio\Modulo\Pagos\Repository;

interface PagosInterface{
    public function get($clause, $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL);
    public function getCollectionExportar($pagos);
    public function getFacturaOperacionNumeroDocumentos($pago);
    public function findBy($clause);
    public function anular_pago($pago_id);
}
