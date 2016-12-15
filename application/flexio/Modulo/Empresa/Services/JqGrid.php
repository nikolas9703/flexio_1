<?php
namespace Flexio\Modulo\Empresa\Services;

use Flexio\Modulo\Empresa\Models\Empresa;

class JqGrid{

  function listar($usuario, $sidx=null, $sord=null, $limit=null, $start=null){

    $empresas = $usuario->empresas();

    if (!is_null($sidx) && !is_null($sord)) $empresas->orderBy($sidx, $sord);
    if (!is_null($limit) && !is_null($start)) $empresas->skip($start)->take($limit);

    return  $empresas->get();
  }
}
