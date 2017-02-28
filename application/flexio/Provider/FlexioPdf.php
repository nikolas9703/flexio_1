<?php
namespace Flexio\Provider;
use Dompdf\Dompdf;

class FlexioPdf{
  protected $dompdf;
  function __construct(){
    $this->dompdf = new Dompdf();
  }

  function render($html,$nombre,$config=[]){

    $this->dompdf->loadHtml($html);
    if(!empty($config)){
      $this->dompdf->setPaper($config['papel'],$config['orientacion']);
    }
    $this->dompdf->render();
    $this->dompdf->stream($nombre);
  }
}
