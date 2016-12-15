<?php
namespace Flexio\Modulo\Entradas\Repository;

interface EntradasInterface
{
    public function create($params);
    public function count($clause);
    public function find($entrada_id);
    public function findByUuid($uuid = NULL);
    public function get($clause, $sidx, $sord, $limit, $start);
    public function getColletionCampos($entrada);
    public function getColletionCell($entrada, $auth);
    public function getColletionCamposItems($items);
    public function save($entrada, $post);
}
