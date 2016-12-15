<?php
namespace Flexio\Modulo\Bodegas\Repository;
interface BodegasInterface{
    public function find($bodega_id);
    public function findBy($clause);
    public function get($clause, $sidx, $sord, $limit, $start);
}
