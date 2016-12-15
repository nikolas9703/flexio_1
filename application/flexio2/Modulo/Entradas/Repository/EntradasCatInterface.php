<?php
namespace Flexio\Modulo\Entradas\Repository;

interface EntradasCatInterface
{
    public function get($clause, $sidx, $sord, $limit, $start);
}
