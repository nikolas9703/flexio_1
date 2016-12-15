<?php
namespace Flexio\Modulo\Inventarios\Repository;

interface SerialesInterface
{
    public function delete($clause);
    public function count($clause);
    public function findBy($clause);
    public function get($clause, $sidx, $sord, $limit, $start);
    public function getCollectionCellSeries($serie, $auth);
    public function save($item);
}
