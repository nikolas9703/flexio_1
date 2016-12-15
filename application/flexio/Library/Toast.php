<?php

namespace Flexio\Library;

use Flexio\Library\Util\FlexioSession;

class Toast
{

  protected $tipo = "success"; //information | error | warning | success
  protected $mensaje = "<b>¡Excelente! Usted se encuentra en flexio</b>";
  protected $titulo = "";
  protected $flashdata = true;
  protected $redirect = false;
  protected $url = "/";
  protected $appends = [];

  protected $FlexioSession;

  public function __construct()
  {
    $this->FlexioSession = new FlexioSession;
  }

  public function run($tipo = "success", $appends = [])
  {
    $tipo = "run".ucfirst($tipo);
    $this->$tipo();

    $this->appends = $appends;
    $mensaje = ["tipo" => $this->getTipo(), "mensaje" => $this->getMensaje(), "titulo" => $this->getTitulo()];
    $this->storeFlashdata($mensaje);
    $this->runRedirect();
  }

  private function runSuccess()
  {
    $this
    ->setTipo("success")
    ->setMensaje("<b>¡&Eacute;xito!</b> Se ha guardado correctamente</b>");
  }

  private function runError()
  {
    $this
    ->setTipo("error")
    ->setMensaje("<b>¡Error! Su solicitud no fue procesada</b>");
  }

  private function runException()
  {
    $this
    ->setTipo("error")
    ->setRedirect(true)
    ->setMensaje("<b>¡Error! Su solicitud no fue procesada</b>");
  }

  public function runVerifyPermission($tiene_permiso = false)
  {
    if(!$tiene_permiso)
    {
      $this
      ->setTipo("error")
      ->setRedirect(true);
    }
    $this->run('error',["Usted no cuenta con permiso para esta solicitud"]);
  }

  private function runRedirect()
  {
    if($this->getRedirect() === true)
    {
      redirect(base_url($this->getUrl()));
    }
  }

  public function getTipo()
  {
    return $this->tipo;
  }

  public function getMensaje()
  {
    return $this->mensaje.$this->getAppends();
  }

  private function getAppends()
  {
    return str_replace("\\"," ","<br><span class=\"\"> ".implode("<br> ", $this->appends)."</span>");
  }

  public function getTitulo()
  {
    return $this->titulo;
  }

  public function getFlashdata()
  {
    return $this->flashdata;
  }

  public function getRedirect()
  {
    return $this->redirect;
  }

  public function getUrl()
  {
    return $this->url;
  }

  public function setTipo($value)
  {
    if(!empty($value))
    {
        $this->tipo = $value;
    }
    return $this;
  }

  public function setMensaje($value)
  {
    if(!empty($value))
    {
        $this->mensaje = $value;
    }
    return $this;
  }

  public function setTitulo($value)
  {
    if(0 && !empty($value))//no va a ser true, se quita porque nos e ve bien
    {
        $this->titulo = $value;
    }
    return $this;
  }

  public function setFlashdata($value)
  {
    $this->flashdata = $value;
    return $this;
  }

  public function setRedirect($value)
  {
    $this->redirect = $value;
    return $this;
  }

  public function setUrl($value)
  {
    $this->url = $value;
    return $this;
  }

  public static function getStoreFlashdata()
  {
    $obj = new static();
    $mensaje = $obj->FlexioSession->session()->flashdata('mensaje');

    return !empty($mensaje) ? collect($mensaje) : Collect([]);
  }

  private function storeFlashdata($mensaje)
  {
    if($this->getFlashdata() === true)
    {
      $this->FlexioSession->session()->set_flashdata('mensaje', $mensaje);
    }
  }

}
