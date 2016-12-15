<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaCliente\Pdf;
use Dompdf\Dompdf;

class ReporteClienteEstadoCuenta{
  protected $dompdf;
  function __construct(){
    $this->dompdf = new Dompdf();
  }

  function render($html,$nombre){
    //$this->dompdf->set_base_path("/public/assets/default/css");
    //$this->dompdf->set_base_path('/');
    $this->dompdf->loadHtml($html);
    $this->dompdf->render();
    $this->dompdf->stream($nombre);
  }
}
