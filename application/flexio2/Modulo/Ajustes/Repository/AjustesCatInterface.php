<?php
namespace Flexio\Modulo\Ajustes\Repository;

interface AjustesCatInterface
{
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL);
}
