<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 11/1/17
 * Time: 2:32 PM
 */

namespace Flexio\Modulo\Pagos\Observer;

use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Historial\Models\Historial;
use Flexio\Modulo\Catalogos\Repository\CatalogoRepository;

class PagosObserver
{
    public function created($pagos = null)
    {

        $creado_por = FlexioSession::now();
        $created = [
            'antes' => $this->antes($pagos),
            'despues' => $this->despues($pagos),
            'usuario_id' => $creado_por->usuarioId(),
            'descripcion' => "Estado: Por aprobar",
            'titulo' => 'Se creó el pago'
        ];
        $pagos->historial()->save(new Historial($created));
    }

    public function updating($pagos = null){

        $creado_por = FlexioSession::now();

        $updated = [
            'antes' => $this->antes($pagos),
            'despues' => $this->despues($pagos),
            'titulo' => 'Se actualizó el pago',
            'usuario_id' => $creado_por->usuarioId(),
            'descripcion' => $this->descripcion($pagos),
            'tipo'=> 'actualizado'
        ];

        $pagos->historial()->save(new Historial($updated));
    }
    private function antes($pagos){
        $data = $pagos->fresh();
        return array_intersect_key($data->toArray(), $pagos->getDirty());
    }

    private function despues($pagos){
        return$pagos->getDirty();
    }

    private function descripcion($pagos){
        $cambio = $pagos->getDirty();
        $original = $pagos->getOriginal();
        //dd($cambio, $original);
        $clause = ['modulo' => 'pagos', 'tipo' => 'etapa', 'etiqueta' => $cambio['estado']];
        $clause2 = ['modulo' => 'pagos', 'tipo' => 'etapa', 'etiqueta' => $original['estado']];
        $catalogo = new CatalogoRepository();
        $estado_1 = $catalogo->get($clause2);
        $estado_2 = $catalogo->get($clause);
        $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado</b></br></br>";
        $descripcion .= "Estado actual: ".$estado_2[0]->valor.'</br></br>';
        $descripcion .= "Estado anterior: ".$estado_1[0]->valor;
        return  $descripcion;
    }
}