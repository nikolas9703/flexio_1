<?php
namespace Flexio\Modulo\Contabilidad\Repository;

interface CuentasInterface
{
    public function find($cuenta_id);
    public function findByUuid($uuid);
    public function get($clause, $sidx, $sord, $limit, $start);
    public function getAll($clause);
}
