<?php
namespace Flexio\Modulo\FacturasCompras\Repository;

interface FacturaCompraInterface{
    public function count($clause);
    public function find($id);
    public function findByUuid($uuid);
    public function get($clause, $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL);
    public function getAll($clause);
    public function getCollectionCellDeItem($factura, $item_id);
    public function getCollectionExportar($facturas);
    public function getOperaciones($clause);
    public function create($create);
    public function update($update);
    public function lista_totales($clause);
    public function listar($clause ,$sidx, $sord, $limit, $start);
}
