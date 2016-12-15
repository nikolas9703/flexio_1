<?php
namespace Flexio\Modulo\Contabilidad\Repository;

interface ImpuestosInterface
{
    public function find($cuenta_id);
    public function findByUuid($uuid);
}
