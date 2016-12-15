<?php
namespace Flexio\Modulo\Inventarios\Repository;

interface UnidadesInterface
{
    public function find($cuenta_id);
    public function findByUuid($uuid);
}
