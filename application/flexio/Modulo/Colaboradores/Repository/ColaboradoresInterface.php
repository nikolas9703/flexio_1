<?php
namespace Flexio\Modulo\Colaboradores\Repository;

interface ColaboradoresInterface
{
    public function find($colaborador_id);
    public function get($clause, $sidx, $sord, $limit, $start);
    public function getResponseCell($colaborador, $link_option, $hidden_options);
    public function count($clause);
}
