<?php
namespace Flexio\Modulo\CentrosContables\Repository;

interface CentrosContablesInterface
{
    public function findByUuid($uuid_centro_contable);
    public function get($clause, $sidx, $sord, $limit, $start);
    public function find($id);
}
