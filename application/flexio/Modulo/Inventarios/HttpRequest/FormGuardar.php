<?php

namespace Flexio\Modulo\Inventarios\HttpRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

//utils
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Inventarios\HttpRequest\FormMutator;

//models
use Flexio\Modulo\Inventarios\Models\Items;
use Flexio\Modulo\Inventarios\Models\LinesItems;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItems;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompraItems;
use Flexio\Modulo\Pedidos\Models\PedidosItems;


class FormGuardar{

    protected $request;
    protected $session;
    protected $FormMutator;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->FormMutator = new FormMutator;
    }

    public function guardar()
    {
          $item = $this->FormMutator->item(FormRequest::data_formulario($this->request->input('campo')));
          $item["cuentas"] = $this->request->input('cuentas');

          $this->_codigo_duplicado($item);

          //editar item
          if(isset($item['id'])){
              return $this->_update($item);
          }

          //crear item -> solo si no se esta editando
          return $this->_create($item);
    }

    private function _codigo_duplicado($item)
    {
        $registro = Items::where(function($query) use ($item) {
            $query->whereCodigo($item["codigo"]);
            $query->whereEmpresaId($this->session->empresaId());
            if(isset($item["id"]) && !empty($item["id"])){$query->where("id", "!=", $item["id"]);}
        });

        if(count($registro->first()))
        {
            throw new \Exception('Este n&uacute;mero de item ya existe, favor ingrese otro');
        }
    }

    private function _actualizarTablasRelacionadas($categoria_nueva, $item )
    {
              $categoria_actual = $item->categorias_items->first()->id_categoria;

              LinesItems::where('categoria_id', $categoria_actual)->where('item_id', $item->id)->update(['categoria_id' => $categoria_nueva]);
              ContratosAlquilerItems::where('categoria_id', $categoria_actual)->where('item_id', $item->id)->update(['categoria_id' => $categoria_nueva]);
              FacturaCompraItems::where('categoria_id', $categoria_actual)->where('item_id', $item->id)->update(['categoria_id' => $categoria_nueva]);
              PedidosItems::where('categoria_id', $categoria_actual)->where('id_item', $item->id)->update(['categoria_id' => $categoria_nueva]);
     }

    private function _relationships($item)
    {

        $campo = $this->request->input('campo');

        $categorias = $campo["categorias"]; //Se cambio el select a simple y no multiples categorias

        $unidades = $this->request->input('unidades');
        $atributos = $this->request->input('atributos') ? : [];
        $precios = $this->request->input('precios') ? : [];
        $precio_alquiler= $this->request->input('precio_alquiler') ? : []; //****

        //Cambio de Kimi para el card 1189 si ha habido un cambio en la categora del item cambiar las tablas donde aparezca la categoria
        if($item->categorias_items->first()->id_categoria != current($categorias)){
          //nuvo id de la categoria current($categorias)
            $item->categorias()->sync($categorias);
            $this->_actualizarTablasRelacionadas(current($categorias), $item); //valor Nuevo, Valor Viejo
        }

        $item->unidades()->sync($this->FormMutator->unidades($unidades));
        $item->precios()->sync($this->FormMutator->precios($precios));
        $item->precios_alquiler()->sync($this->FormMutator->precios_alquiler($precio_alquiler));

        //metodo privado para guardar los atributos
        $this->_syncAtributos($item, $atributos);
    }

    private function _syncAtributos($item, $atributos)
    {
        $item->atributos()->whereNotIn('atributable_id',array_pluck($atributos,'id'))->delete();
        foreach($atributos as $atributo){

            if(!empty($atributo["nombre"]))
            {
                $atributo_id = (isset($atributo['id']) and !empty($atributo['id'])) ? $atributo['id'] : '';
                $atributo_item = $item->atributos()->firstOrNew(['id'=>$atributo_id]);

                $atributo_item->nombre = $atributo["nombre"];
                $atributo_item->descripcion = $atributo["descripcion"];
                $atributo_item->save();
            }

        }
    }

    private function _create($campos)
    {
        return  Capsule::transaction(function() use($campos){
            $item = Items::create($campos);
            $this->_relationships($item);

            //registro categorias
            //registro unidades
            //registro atributos
            //registro precios


            return $item;
        });
    }

    private function _update($campos)
    {
        return  Capsule::transaction(function() use($campos){
            $item = Items::find($campos['id']);
            $item->update($campos);
            $this->_relationships($item);

            return $item;
        });
    }

}
