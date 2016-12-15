<?php
namespace Flexio\Modulo\SubContratos\Repository;

interface SubContratoInterface
{
    public function findBy($id);
    public function findByUuid($uuid);
    public function getSubContratos($clause);
    public function create($create);
    public function update($update);
    public function lista_totales($clause);
    public function listar($clause ,$sidx, $sord, $limit, $start);
}
