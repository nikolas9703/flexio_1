<?php
namespace Flexio\Strategy\Reportes;
use Flexio\Modulo\ReporteFinanciero\Repository\CatalogoReporteFinancieroRepository;

class CatalogoBalanceSituacion{
  protected $repositorio;

  function __construct(){
    $this->repositorio = new CatalogoReporteFinancieroRepository;
  }

  function getCatalogos(){
    return ['years'=>$this->repositorio->getYears(),'meses'=> $this->repositorio->meses()->toArray()];
  }

}
