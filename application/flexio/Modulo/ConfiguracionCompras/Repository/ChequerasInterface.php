<?php
namespace Flexio\Modulo\ConfiguracionCompras\Repository;

interface ChequerasInterface
{
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL);
    public function incrementa_secuencial($chequera_id);
}
