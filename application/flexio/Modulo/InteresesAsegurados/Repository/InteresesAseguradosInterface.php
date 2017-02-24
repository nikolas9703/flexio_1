<?php
namespace Flexio\Modulo\InteresesAsegurados\Repository;

interface InteresesAseguradosInterface
{
    public function listar($clause, $sidx, $sord, $limit, $start);
    public function count($clause);
}
