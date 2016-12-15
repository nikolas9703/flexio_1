<?php
namespace Flexio\Modulo\Inventarios\Repository;

interface ItemsCatInterface
{
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL);
}
