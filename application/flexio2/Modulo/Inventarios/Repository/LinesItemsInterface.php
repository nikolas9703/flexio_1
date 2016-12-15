<?php
namespace Flexio\Modulo\Inventarios\Repository;

interface LinesItemsInterface
{
    public function get($clause, $sidx, $sord, $limit, $start);
}
