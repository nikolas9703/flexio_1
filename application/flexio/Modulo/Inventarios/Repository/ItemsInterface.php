<?php
namespace Flexio\Modulo\Inventarios\Repository;

interface ItemsInterface
{
    public function count($clause);
    public function get($clause, $sidx, $sord, $limit, $start);
    public function getColletionCell($item, $auth);
    public function getColletionRegistro($item, $uuid_bodega);
    public function getColletionRegistros($items, $uuid_bodega = NULL);
    public function getColletionRegistrosExportar($items, $uuid_bodega = NULL);
    public function findByUuid($uuid_item);
    public function find($item_id);
}
