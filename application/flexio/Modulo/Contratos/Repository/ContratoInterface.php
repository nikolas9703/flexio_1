<?php
namespace Flexio\Modulo\Contratos\Repository;
interface ContratoInterface{
  public function findBy($id);
  public function findByUuid($uuid);
  public function getContratos($clause);
  public function create($create);
  public function update($update);
  public function lista_totales($clause);
  public function listar($clause ,$sidx, $sord, $limit, $start);
}
