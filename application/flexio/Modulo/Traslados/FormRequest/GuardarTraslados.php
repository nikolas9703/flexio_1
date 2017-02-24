<?php

namespace Flexio\Modulo\Traslados\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Traslados\Models\Traslados;

//repositories
use Flexio\Modulo\Bodegas\Repository\BodegasRepository;
use Flexio\Modulo\Entradas\Repository\EntradasRepository;
use Flexio\Modulo\Salidas\Repository\SalidasRepository;

class GuardarTraslados
{
    protected $request;
    protected $session;

    //repositories
    protected $BodegasRepository;
    protected $EntradasRepository;
    protected $SalidasRepository;

    public function __construct()
    {
        $this->BodegasRepository = new BodegasRepository;
        $this->EntradasRepository = new EntradasRepository;
        $this->SalidasRepository = new SalidasRepository;
    }

    public function save($data)
    {
        if(array_get($data, 'campo.id', '') !='')
        {
            return $this->actualizar($data);
        }
        return $this->crear($data);
    }

    public function crear($campo)
    {

        return  Capsule::transaction(function() use ($campo) {
            $campo["campo"]['numero'] = Traslados::whereIdEmpresa($campo["campo"]['id_empresa'])->count() + 1;
            $traslado = Traslados::create($campo["campo"]);

            $this->_syncItems($traslado, $campo["items"]);

            if($this->BodegasRepository->find($traslado->bodega->id)->raiz->entrada_id == 1)// 1 -> entrada manual : 2 -> entrada automatica
            {
                $traslado->fecha_entrega = date("d/m/Y", time());
                //CREO UN REGISTRO EN EL MODULO DE SALIDA
                $this->SalidasRepository->create(array("tipo_id" => $traslado->id, "estado_id" => 1, "tipo" => "Flexio\Modulo\Traslados\Models\Traslados", "empresa_id" => $campo["campo"]["id_empresa"]));
                //CREO UN REGISTRO EN EL MODULO DE ENTRADA
                $this->EntradasRepository->create(array("tipo_id" => $traslado->id, "estado_id" => 1, "tipo" => "Flexio\Modulo\Traslados\Models\Traslados", "empresa_id" => $campo["campo"]["id_empresa"]));
            }

            return $traslado;
        });

    }

    public function actualizar($campo)
    {
        return  Capsule::transaction(function() use ($campo) {
            $traslado = Traslados::find($campo["campo"]["id"]);
            $traslado->update($campo['campo']);
            
            $this->_syncItems($traslado, $campo["items"]);
            return $traslado;
        });

    }

    private function _syncItems($traslado, $items)
    {

        $traslado->lines_items()->whereNotIn('id',array_pluck($items,'id_pedido_item'))->delete();
        foreach ($items as $ti) {

            $traslado_item_id = (isset($ti['id_pedido_item']) and !empty($ti['id_pedido_item'])) ? $ti['id_pedido_item'] : '';
            $traslado_item = $traslado->lines_items()->firstOrNew(['id'=>$traslado_item_id]);
            $traslado_item->categoria_id = $ti["categoria"];
            $traslado_item->item_id = $ti["item_id"];
            $traslado_item->cantidad = $ti["cantidad"];
            $traslado_item->unidad_id = $ti["unidad"];
            $traslado_item->precio_unidad = 0;//verificar si esto afecta el precio promedio, si afecta sacar del calculo
            $traslado_item->descuento = 0;
            $traslado_item->comentario = (isset($ti['comentario']))?$ti['comentario']:'';
            //opcionales
            $traslado_item->atributo_id = (isset($ti['atributo_id']) and !empty($ti['atributo_id'])) ? $ti['atributo_id'] : 0;
            $traslado_item->atributo_text = (isset($ti['atributo_text']) and !empty($ti['atributo_text'])) ? $ti['atributo_text'] : '';
            $traslado_item->save();

        }

    }

}
