<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 16/2/17
 * Time: 2:56 PM
 */

namespace Flexio\Modulo\SubContratos\Observer;

use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Historial\Models\Historial;
use Flexio\Modulo\SubContratos\Models\SubContrato;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Catalogos\Repository\CatalogoRepository as CatalogoRepository;

class SubContratoObserver
{

    public function created($subContrato = null)
    {

        $creado_por = FlexioSession::now();
        $created = [
            'antes' => $this->antes($subContrato),
            'despues' => $this->despues($subContrato),
            'usuario_id' => $creado_por->usuarioId(),
            'descripcion' => "Estado: Por aprobar",
            'titulo' => 'Se ha creado el Subcontrato'
        ];
        $subContrato->historial()->save(new Historial($created));
    }
    public function updating($subContrato = null){

        $creado_por = FlexioSession::now();

        $updated = [
            'antes' => $this->antes($subContrato),
            'despues' => $this->despues($subContrato),
            'titulo' => 'Se ha actualizado el Subcontrato',
            'usuario_id' => $creado_por->usuarioId(),
            'descripcion' => $this->descripcion($subContrato),
            'tipo'=> 'actualizado'
        ];

        $subContrato->historial()->save(new Historial($updated));
    }

    private function antes($subContrato){
        $data = $subContrato->fresh();
        return array_intersect_key($data->toArray(), $subContrato->getDirty());
    }

    private function despues($subContrato){
        return $subContrato->getDirty();
    }

    private function descripcion($subContrato){
        $cambio = $subContrato->getDirty();
        $original = $subContrato->getOriginal();
        $catalogo = new CatalogoRepository();

        $estado_original = $catalogo->estado($original['estado'], 'subcontratos');
        $estado_nuevo = $catalogo->estado($cambio['estado'], 'subcontratos');

        $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado</b></br></br>";
        $descripcion .= "Estado actual: ".$estado_nuevo[0]->valor.'</br></br>';
        $descripcion .= "Estado anterior: ".$estado_original[0]->valor;

        return  $descripcion;
    }
}
