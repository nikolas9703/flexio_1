<?php
namespace Flexio\Modulo\OrdenesVentas\Repository;
interface OrdenVentaInterface{
  public function find($id);
  public function findByUuid($uuid);
  public function getAll($clause);
  public function create($create);
  public function update($update);
  public function lista_totales($clause);
  public function listar($clause ,$sidx, $sord, $limit, $start);
}
