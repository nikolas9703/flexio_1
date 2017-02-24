<?php
namespace Flexio\Modulo\Ajustes\Repository;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Inventarios\Repository\SerialesRepository as serialesRep;
use Flexio\Modulo\Inventarios\Repository\LinesItemsRepository as linesItemsRep;
use Flexio\Modulo\Entradas\Repository\EntradasRepository as entradasRep;
use Flexio\Modulo\Salidas\Repository\SalidasRepository as salidasRep;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository as centrosContablesRep;


//modelos
use Flexio\Modulo\Ajustes\Models\Ajustes as Ajustes;

class AjustesRepository implements AjustesInterface{

    private $bodegasRep;
    private $itemsRep;
    private $serialesRep;
    private $linesItemsRep;
    private $entradasRep;
    private $salidasRep;
    private $centrosContablesRep;

    //variables del entorno
    private $prefijo = "TRAS";

    public function __construct() {
        $this->bodegasRep           = new bodegasRep();
        $this->itemsRep             = new itemsRep();
        $this->serialesRep          = new serialesRep();
        $this->linesItemsRep        = new linesItemsRep();
        $this->entradasRep          = new entradasRep();
        $this->salidasRep           = new salidasRep();
        $this->centrosContablesRep  = new centrosContablesRep();
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $ajustes = Ajustes::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($ajustes, $clause);

        if($sidx!=NULL && $sord!=NULL){$ajustes->orderBy($sidx, $sord);}
        if($limit!=NULL){$ajustes->skip($start)->take($limit);}
        return $ajustes->get();
    }

    public function getColletionAjuste($registro)
    {
        $articulos = new \Flexio\Library\Articulos\ArticuloInventario;
        return Collect(array_merge(
            $registro->toArray(),
            [
                'razon_id' => $registro->razon_id ? : '',
                'uuid_bodega' => strtoupper(bin2hex($registro->uuid_bodega)),
                'articulos' => $articulos->get($registro->ajustes_items, NULL)
            ]
        ));
    }

    public function getColletionCampos($registro)
    {
        return [
            "bodega"        => strtoupper(bin2hex($registro->uuid_bodega)),
            "numero"        => $this->prefijo.$registro->numero,
            "descripcion"   => $registro->descripcion,
            "tipo_ajuste"   => $registro->tipo_ajuste_id,
            "estado"        => $registro->estado_id,
            "fecha"         => $registro->created_at,
            "total_general" => $registro->total,
            "comentarios"   => $registro->comentarios,
            "centro"        => $registro->centro_contable->id,
            "razon_id"      => $registro->razon_id
        ];
    }

    public function getCollectionCamposItems($items) {
        $aux = [];
        foreach($items as $item)
        {
            $aux[] = array(
                "item"                  => $item->uuid_item,
                //"cantidad_disponible"   => "",//no se muestra en la edicion
                "cantidad"              => $item->pivot->cantidad,
                "precio_unitario"       => $item->pivot->precio_unidad,
                "id_ajuste_item"        => $item->pivot->id
            );
        }
        return $aux;
    }

    private function _getSeriales($lista_seriales)
    {
        $aux = [];
        $seriales   = count($lista_seriales);
        $filas      = $seriales/5;
        $contador2  = 0;

        $i = 0;
        for($filas; $filas>0; $filas--)
        {
            $contador = 0;
            for($seriales; $seriales > 0; $seriales--)
            {
                $aux[$i][] = [
                    "serial"    => $lista_seriales[$contador2]["nombre"]
                ];

                $contador++;
                $contador2++;
                if($contador == 5){
                    $seriales--;
                    break;
                }
            }
            $i++;
        }

        return $aux;
    }

    public function getCollectionArticulos($items, $empresa_id) {
        $aux                    = [];
        $clause                 = [];
        $clause["empresa_id"]   = $empresa_id;
        foreach($items as $item)
        {
            $clause["categoria_id"] = $item->pivot->categoria_id;//para obtener catalogo de items por categoria
            $seriales = $this->serialesRep->get(["item_id" => $item->id, "line_id" => $item->pivot->id]);
            $aux[] = array(
                "categoria"             => $item->pivot->categoria_id,
                "items"                 => $this->itemsRep->getColletionRegistros($this->itemsRep->get($clause)),
                "item"                  => $item->id,
                "cuenta"                => $item->pivot->cuenta_id,
                "cantidad"              => $item->pivot->cantidad,
                "cantidad_disponible"   => '',
                "costo_promedio"        => $item->pivot->precio_unidad,
                "precio_total"          => $item->pivot->precio_total,
                "tipo_id"               => $item->tipo_id,
                "seriales"              => $this->_getSeriales($seriales->toArray())
            );
        }
        return $aux;
    }

    public function find($ajuste_id) {
        return Ajustes::find($ajuste_id);
    }

    public function findByUuid($uuid_ajuste) {
        return Ajustes::findByUuid($uuid_ajuste);
    }

    public function count($clause = array())
    {
        $ajustes = Ajustes::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($ajustes, $clause);

        return $ajustes->count();
    }

    private function _filtros($ajustes, $clause)
    {
        //por ahora nada
    }

    private function _create($registro, $campo)
    {
        $numero                     = $this->count($campo) + 1;

        $registro->uuid_ajuste      = Capsule::raw("ORDER_UUID(uuid())");
        $registro->numero           = str_replace($this->prefijo, "", $numero);
        $registro->empresa_id       = $campo["empresa_id"];
        $registro->created_by       = $campo["usuario_id"];

        //GUARDO EL REGISTRO
        $registro->save();
    }

    private function _save($registro, $campo)
    {
        $registro->centro_id        = $campo["centro_id"];
        $registro->uuid_bodega      = hex2bin(strtolower($campo["uuid_bodega"]));
        $registro->descripcion      = $campo["descripcion"];
        $registro->tipo_ajuste_id   = $campo["tipo_ajuste_id"];
        $registro->razon_id         = $campo["razon_id"];
        $registro->estado_id        = $campo["estado_id"];//Por defecto un ajuste se crea en estado "Por aprobar"
        $registro->comentarios      = "";//$campo["comentarios"];//Campo deshabilitado 11-01-2016
        $registro->total            = $campo["total"];

        //GUARDO EL REGISTRO
        $registro->save();
    }

    private function _syncItems($registro, $items)
    {

        $items = array_map(function($item){
            return array_merge($item, ['item' => $item['item_id'], 'id_entrada_item' => $item['id_pedido_item']]);
        }, $items);
        foreach ($items as $item)
        {

            $ajuste_item_id = (isset($item['id_pedido_item']) and !empty($item['id_pedido_item'])) ? $item['id_pedido_item'] : '';
            $ajuste_item = $registro->ajustes_items()->firstOrNew(['id'=>$ajuste_item_id]);

            $ajuste_item->item_id = $item["item_id"];
            $ajuste_item->categoria_id = $item["categoria"];
            $ajuste_item->cuenta_id = $item["cuenta"];
            $ajuste_item->cantidad = $item["cantidad"];
            $ajuste_item->cantidad2 = $item["cantidad"];
            $ajuste_item->atributo_id = isset($item['atributo_id']) ? $item['atributo_id'] : 0;
            $ajuste_item->atributo_text = isset($item['atributo_text']) ? $item['atributo_text'] : '';
            $ajuste_item->precio_unidad = str_replace(',', '', $item["precio_unidad"]);
            $ajuste_item->unidad_id = $item['unidad'];
            $ajuste_item->precio_total = str_replace(',', '', $item["precio_total"]);
            $ajuste_item->save();

        }

        //si el ajuste es aprobado registra los seriales
        foreach($registro->items as $row)
        {
            //$this->serialesRep->delete(["item_id" => $row->id, "line_id" => $row->pivot->id]);

            $aux = array_filter($items, function($value) use ($row){
                return $value["item_id"] == $row->id;
            });

            if(!empty($aux))
            {
                $llave = 0;
                foreach($aux as $key => $value)
                {
                    $llave = $key;
                }
                $aux[$llave]["id_entrada_item"] = $row->pivot->id;
                $estado = (($registro->estado_id == '4' && $registro->tipo_ajuste_id == '2') || ($registro->estado_id == '3' && $registro->tipo_ajuste_id == '1')) ? 'disponible' : 'no_disponible';
                $this->serialesRep->save($aux[$llave], $registro, $estado);
            }
        }
    }



    public function create($params)
    {
        $campo      = $params["campo"];
        $items      = $params["items"];
        $registro   = new Ajustes;

        $this->_create($registro, $campo);
        $this->_save($registro, $campo);
        $this->_syncItems($registro, $items);

        return $registro;
    }

    public function save($ajuste, $post)
    {
        $campo = $post["campo"];
        $items = $post["items"];

        $this->_save($ajuste, $campo);
        $this->_syncItems($ajuste, $items);

        if($ajuste->estado_id == "4")//Ajuste aprobado
        {
            if($ajuste->tipo_ajuste_id == "1")//ajuste de tipo Negativo
            {
                //CREO UN REGISTRO EN EL MODULO DE SALIDAS - Estado -> Enviada
                $this->salidasRep->create(array("tipo_id" => $ajuste->id, "estado_id" => 3, "tipo" => "Flexio\Modulo\Ajustes\Models\Ajustes", "empresa_id" => $campo["empresa_id"]));
            }
            elseif($ajuste->tipo_ajuste_id == "2")//ajuste de tipo Positivo
            {
                //estado_id:3 => Entrada recibida...
                $this->entradasRep->create(array("tipo_id" => $ajuste->id, "estado_id" => "3", "tipo" => "Flexio\Modulo\Ajustes\Models\Ajustes", "empresa_id" => $campo["empresa_id"]));
            }
        }
    }

}
