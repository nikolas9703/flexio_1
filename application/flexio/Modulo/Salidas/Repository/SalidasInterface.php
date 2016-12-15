<?php
namespace Flexio\Modulo\Salidas\Repository;

interface SalidasInterface
{
    public function create($params);
    public function count($clause);
    public function find($salida_id);
    public function findByUuid($uuid = NULL);
    public function get($clause, $sidx, $sord, $limit, $start);
    public function getColletionCampos($salida);
    public function getColletionCell($salida, $auth);
    public function getColletionCamposItems($items);
    public function save($salida, $post);
}
