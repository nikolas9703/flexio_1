<?php
namespace Flexio\Modulo\Plantillas\Repository;
interface PlantillaInterface{
  public function find($id);
  public function findByUuid($uuid);
  public function getAll($clause);
   public function getAllGroupByTipo($clause);
  public function create($create);
  public function update($update);
  public function listar($clause ,$sidx, $sord, $limit, $start);
}
