<?php
namespace Flexio\Modulo\ConfiguracionCompras\Repository;
interface ChequesInterface{
  public function find($id);
  public function findByUuid($uuid);
  public function getAll($clause);
  public function getCollectionCellDeItem($factura, $item_id);
  public function create($create);
  public function update($uuid,$campos);
  public function lista_totales($clause);
  public function listar($clause ,$sidx, $sord, $limit, $start);
  public function anular_cheque($cheque_id);
}
