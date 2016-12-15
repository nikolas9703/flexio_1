<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\Pdf;
use Dompdf\Dompdf;

class ReportePdf{
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
