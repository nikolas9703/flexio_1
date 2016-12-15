<?php

namespace Flexio\Modulo\Politicas\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

use Flexio\Modulo\Politicas\Models\Politicas;

class GuardarPolitica{

    protected $request;
    protected $session;

    protected $modulo = [
        'orden_compra' => '',
        'pedido' => '',
        'pago' => '',
        'factura_compra' => ''
    ];


    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;

    }

    function guardar(){

          $politica = FormRequest::data_formulario($this->request->input('campo'));
          $politica["estado_id"] = isset($politica["estado_id"])?$politica["estado_id"]:0;

         $categorias = $this->categorias();

          if(isset($politica['id'])){
              return $this->actualizar($politica, $categorias);
          }

         return  $this->crear($politica, $categorias);
    }

    function crear($campos, $categorias){
            return  Capsule::transaction(function() use($campos, $categorias){
                $politica = Politicas::create($campos);
                $politica->categorias()->attach($categorias);
                return $politica;
        });
    }

    function actualizar($campos, $categorias){

        return  Capsule::transaction(function() use($campos, $categorias){
        $politica = Politicas::find($campos['id']);
        $politica->update($campos);
        $politica->categorias()->sync($categorias);

        return $politica;
    });
    }

    function categorias(){

        if($this->request->has('categorias')){
            $categoria = $this->request->input('categorias');
            if(in_array('todas',$categoria)){
                return $this->categorias_todas();
            }
            return $this->categoriaConIds();
        }
        return [];
    }

    function categorias_todas(){
        $objCategoriaItem = new \Flexio\Modulo\Inventarios\Repository\CategoriasRepository;
        $clause = ['empresa_id' => $this->session->empresaId(),'conItems' => true];
        $cat = $objCategoriaItem->get($clause);
        $resultado = $cat->pluck('id')->all();
        return $resultado;
    }

    function categoriaConIds(){
        $categoria = $this->request->input('categorias');
        $categorias1 = collect($categoria)->flatten(1);
        $resultado = $categorias1->values()->all();
        return $resultado;
    }

}
