<?php

namespace Flexio\Modulo\NotaDebito\Validators;

class NotaDebitoValidator
{

  public function post_validate($post)
  {
    $nota_debito = $post;

    if(!isset($nota_debito["proveedor_id"]) || empty($nota_debito["proveedor_id"])){throw new \Exception('El proveedor asociado a la nota de debito no esta activo (Proveedores/Detalle)');}
    if(!isset($nota_debito["creado_por"]) || empty($nota_debito["creado_por"])){throw new \Exception('No se puede determinar el usuario quien gener&oacute; la nota de debito');}
  }

}
