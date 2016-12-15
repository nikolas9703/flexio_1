<?php
namespace Flexio\Modulo\OrdenesCompra\Repository;

interface OrdenesCompraInterface
{
    public function create($params);
    public function find($orden_compra_id);
    public function findByUuid($uuid);
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL);
    public function getColletionCampos($registro);
    public function getCollectionCamposItems($items);
//    public function count($clause = array());
    public function save($orden, $post);
}
