<?php

namespace Flexio\Library\Util;

use Flexio\Modulo\Empresa\Repository\EmpresaRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;


class FlexioSession{

  protected $codeigniter;
  protected $empresa;
  protected $usuario;

  function __construct(){
    $this->codeigniter = & get_instance();
    $this->empresa = new EmpresaRepository;
    $this->usuario = new UsuariosRepository;
  }

  public static function now(){
    return new static;
  }

  public function session()
  {
    return $this->codeigniter->session;
  }

  public function uri()
  {
    return $this->codeigniter->uri;
  }

  public function empresaUuid(){
    return $this->codeigniter->session->userdata('uuid_empresa');
  }
  public function empresaId(){
    //return $this->empresa->findByUuid($this->empresaUuid())->id;
    if (empty($this->empresaUuid())){
        return 0;
    } else {
    return $this->empresa->findByUuid($this->empresaUuid())->id ;
    }
  }

  public function usuarioUuid(){
    return $this->codeigniter->session->userdata('huuid_usuario');
  }
  public function usuarioId(){
    return $this->usuario->findByUuid($this->usuarioUuid())->id;
  }

    public function usuarioCentrosContables()
    {
        $usuario = $this->usuario->findByUuid($this->usuarioUuid());
        return ($usuario->filtro_centro_contable == 'todos') ? ['todos'] : array_pluck($usuario->centros_contables->toArray(), 'id');
    }

    public function usuarioCentrosContablesHex()
    {
        $usuario = $this->usuario->findByUuid($this->usuarioUuid());
        return ($usuario->filtro_centro_contable == 'todos') ? ['todos'] : array_pluck($usuario->centros_contables->toArray(), 'uuid_centro');
    }

}
