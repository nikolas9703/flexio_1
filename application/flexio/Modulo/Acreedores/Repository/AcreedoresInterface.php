<?php
namespace Flexio\Modulo\Acreedores\Repository;

interface AcreedoresInterface
{
    public function get($clause, $sidx, $sord, $limit, $start);
    public function getTotalAPagar($totalAPagar);
    public function getTipos();
    public function getAcreedoresCategorias($empresa_id);
    public function count($clause);
    public function save($post, $usuario_id, $empresa_id);
    public function find($acreedor_id);
    public function findByUuid($uuid);
    public function getColletionCampos($acreedor);
}
