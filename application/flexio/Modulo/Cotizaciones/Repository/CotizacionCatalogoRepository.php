<?php
namespace Flexio\Modulo\Cotizaciones\Repository;
use Flexio\Modulo\Cotizaciones\Models\CotizacionCatalogo as CotizacionCatalogo;

class CotizacionCatalogoRepository{

    public function getEtapas(){

        return CotizacionCatalogo::where('tipo','etapa')->orderBy("orden", "asc")->get(array('id','etiqueta','valor'));

    }

  function getTerminoPago(){
    return CotizacionCatalogo::where('tipo','termino_pago')->get(array('id','etiqueta','valor'));
  }
}
