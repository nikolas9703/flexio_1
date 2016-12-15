<?php
namespace Flexio\Modulo\Salidas\Repository;

interface SalidasCatInterface
{
    public function get($clause, $sidx, $sord, $limit, $start);
}
