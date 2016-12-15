<?php
namespace Flexio\Modulo\Bancos\Repository;

interface BancosInterface
{
    public function get($clause, $sidx, $sord, $limit, $start);
    public function find($banco_id);
}
