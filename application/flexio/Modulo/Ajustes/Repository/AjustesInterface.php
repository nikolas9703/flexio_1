<?php
namespace Flexio\Modulo\Ajustes\Repository;

interface AjustesInterface
{
    public function create($params);
    public function find($ajuste_id);
    public function findByUuid($uuid_ajuste);
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL);
    public function getColletionCampos($registro);
    public function getCollectionCamposItems($items);
    public function getCollectionArticulos($items, $empresa_id);
    public function count($clause = array());
    public function save($ajuste, $post);
}
