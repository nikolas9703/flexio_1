<?php
 namespace  Flexio\Modulo\Talleres\Repository;

 interface Equipotrabajointerface
 {
     public function find($id);
     public function findByUuid($uuid);
     public function listar($clause, $sidx, $sord, $limit, $start);
 }