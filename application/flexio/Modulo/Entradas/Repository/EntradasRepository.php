<?php
namespace Flexio\Modulo\Entradas\Repository;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;

use Flexio\Modulo\Entradas\Models\Entradas as Entradas;
use Flexio\Modulo\Inventarios\Models\Unidades as Unidades;

//repositorios
use Flexio\Modulo\Inventarios\Repository\SerialesRepository as serialesRep;
use Flexio\Modulo\Inventarios\Repository\LinesItemsRepository as linesItemsRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Entradas\Transaccion\TransaccionFactura as transacciones;



class EntradasRepository implements EntradasInterface{

    private $serialesRep;
    private $linesItemsRep;
    private $itemsRep;
    private $transacciones;

    public function __construct() {
        $this->serialesRep      = new serialesRep();
        $this->linesItemsRep    = new linesItemsRep();
        $this->itemsRep         = new itemsRep();
        $this->transacciones         = new transacciones();
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $entradas = Entradas::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($entradas, $clause);

        if($sidx!=NULL && $sord!=NULL){$entradas->orderBy($sidx, $sord);}
        if($limit!=NULL){$entradas->skip($start)->take($limit);}

        return $entradas->get();
    }

    public function find($entrada_id)
    {
        return Entradas::find($entrada_id);
    }

    private function _setItems($items)
    {
        foreach ($items as $item)
        {
            $aux            = $this->linesItemsRep->find($item["id_entrada_item"]);
            $aux->cantidad2 = $this->_getCantidadRecibida($item, $item["cantidad_recibida"]);

            $aux->save();
        }
       }

    private function _getCantidadRecibida($item, $cantidad_recibida)
    {
            $cantidad_recibida_aux = 0;
            if(isset($item["seriales"]))
            {
                foreach ($item["seriales"] as $serial)
                {
                    $cantidad_recibida_aux += (!empty($serial)) ? 1 : 0;
                }
                $cantidad_recibida = $cantidad_recibida_aux;
            }
        return $cantidad_recibida;
    }

    private function _setSeriales($items, $desde_traslado = false, $registro)
    {
        foreach($items as $item)
        {
            if(!$desde_traslado)
            {
                //con calma programar una mejor logica
                //$itemAux    = $this->itemsRep->findByUuid($item["item"]);
                //$this->serialesRep->delete(["item_id" => $itemAux->id, "line_id" => $item["id_entrada_item"]]);
            }

            //popular items nuevamente
            $this->serialesRep->save($item, $registro);
        }
    }

    public function create($clause)
    {
        $entrada = Entradas::deEmpresa($clause["empresa_id"])
                ->deTipo($clause["tipo"])
                ->deTipoId($clause["tipo_id"])
                ->first();

        if(!count($entrada))
        {
            $numero                     = Entradas::deEmpresa($clause["empresa_id"])->count();

            $entrada                   = new Entradas;
            $entrada->uuid_entrada     = Capsule::raw("ORDER_UUID(uuid())");
            $entrada->codigo           = $numero + 1;
            $entrada->empresa_id       = $clause["empresa_id"];
            $entrada->operacion_id     = $clause["tipo_id"];
            $entrada->operacion_type   = $clause["tipo"];
            $entrada->comentarios      = "";
        }

        $entrada->estado_id    = $clause["estado_id"];
        $entrada->save();
        //Verifico si no tiene registros para mostrar para proceder a borrar
        if(count($entrada->operacion->items) == 0)
        {
            $entrada->delete();
        }
    }


    public function save($registro, $post)
    {
        $completo = 1;
        $registro->comentarios  = $post["campo"]["comentarios"];

        //seteo los seriales y la relacion lines_items
        $desde_traslado = ($registro->operacion_type == "Flexio\\Modulo\\Traslados\\Models\\Traslados") ? true : false;
        $this->_setSeriales($post["items"], $desde_traslado, $registro);

        //seteo los items
        $this->_setItems($post["items"], $registro);

        //seteo la relacion de seriales y la entrada

        foreach ($post["items"] as $item)
        {
            if($this->_getCantidadRecibida($item, $item["cantidad_recibida"]) < $item["cantidad_esperada"]){$completo = 0;}
        }

        //3 completo || 2 parcial
        $registro->estado_id = ($completo == 1) ? "3" : "2";

        //Actualiza la fecha de entrega del traslado.
        if($registro->operacion_type == "Flexio\Modulo\Traslados\Models\Traslados")
        {
            if($registro->estado_id == "3")//entrada completa
            {
                $registro->operacion->id_estado     = 3;//Traslado recibido
            }
            $registro->operacion->fecha_entrega = date('d/m/Y', time());
            $registro->operacion->save();
        }

        $registro->save();
        return $registro;
    }

    public function count($clause = array())
    {
        $entradas = Entradas::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($entradas, $clause);

        return $entradas->count();
    }

    private function _filtros($entradas, $clause)
    {
        if(isset($clause["fecha_desde"]) and !empty($clause["fecha_desde"])){$entradas->deFechaDesde($clause["fecha_desde"]);}
        if(isset($clause["fecha_hasta"]) and !empty($clause["fecha_hasta"])){$entradas->deFechaHasta($clause["fecha_hasta"]);}
        if(isset($clause["recibir_en"]) and !empty($clause["recibir_en"])){$entradas->withRecepcionEn($clause["recibir_en"]);}
        if(isset($clause["estado"]) and !empty($clause["estado"])){$entradas->deEstado($clause["estado"]);}
        if(isset($clause["referencia"]) and !empty($clause["referencia"])){$entradas->withReferencia($clause["referencia"]);}
        if(isset($clause["numero"]) and !empty($clause["numero"])){$entradas->withNumeroDocumento($clause["numero"]);}
        if(isset($clause["origen"]) and !empty($clause["origen"])){$entradas->deOrigen($clause["origen"]);}
        if(isset($clause["tipo"]) and !empty($clause["tipo"])){$entradas->deTipo($clause["tipo"]);}
        if(isset($clause["tipo_id"]) and !empty($clause["tipo_id"])){$entradas->deTipoId($clause["tipo_id"]);}
        //subpanels
        if(isset($clause["factura_compra_id"]) and !empty($clause["factura_compra_id"])){$entradas->deFacturaDeCompra($clause["factura_compra_id"]);}

        //faltan los siguientes:
        if(isset($clause["item_id"]) and !empty($clause["item_id"])){$entradas->deItem($clause["item_id"]);}
        if(isset($clause["campo"]) and !empty($clause["campo"])){$entradas->deFiltro($clause["campo"]);}
        if(isset($clause["serie_id"]) and !empty($clause["serie_id"])){$entradas->deSerie($clause["serie_id"]);}



//            if(!empty($numero)){
//                $numero             = str_replace("OC", "", $numero);
//                $numero             = str_replace("TRAS", "", $numero);
//
//                $registros->withNumeroDocumento($numero);
//            }
    }

    public function findByUuid($uuid = NULL) {
        if(!$uuid){die("findByUuid no puede ser null");}
        return Entradas::where("uuid_entrada", hex2bin($uuid))->first();
    }

    public function getColletionCampos($entrada) {
        return [
            "fecha"             => $entrada->created_at,
            "centro_contable"   => $entrada->uuid_centro_contable,
            "recibido_en"       => $entrada->uuid_bodega,
            "estado"            => $entrada->estado_id,
            "numero_orden"      => $entrada->numero_documento,
            "numero_traslado"   => "",//Campo inactivo por cambio
            "numero_factura"    => array(),//Campo inactivo por cambio
            "comentarios"       => $entrada->comentarios,
            "entrada_id"        => $entrada->id
        ];
    }

    public function getColletionCamposItems($items) {
        $colletionsItems = [];
        foreach ($items as $row)
        {
            $seriales = $this->serialesRep->get(["item_id" => $row->id, "line_id" => $row->pivot->id]);
            $colletionsItems[] = array(
                "item"              => $row->uuid_item,
                "descripcion"       => $row->descripcion,
                "tipo_id"           => $row->tipo_id,//5 y 8 se registran las series
                "observacion"       => $row->pivot->observacion,
                "cantidad_esperada" => $row->pivot->cantidad,
                "cantidad_recibida" => $row->pivot->cantidad2,
                "unidades"          => $row->unidades,
                "unidad"            => Unidades::find($row->pivot->unidad_id)->uuid_unidad,
                "id_entrada_item"   => $row->pivot->id,
                "seriales"          => $seriales->toArray()
            );
        }

        return $colletionsItems;
    }

    public function getColletionCell($row, $auth) {
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_entrada .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

        if($auth->has_permission('acceso', 'entradas/ver/(:any)')){
            $hidden_options .= '<a href="'.base_url('entradas/ver/'. $row->uuid_entrada).'" class="btn btn-block btn-outline btn-success">Ver Entrada</a>';
        }


        if($hidden_options == ""){$link_option = "&nbsp;";}


        //DOCUMENTO
        //$documento_enlace   = $operacion->uuid_orden ? base_url('ordenes/ver/'. $operacion->uuid_orden) : base_url('traslados/ver/'. $operacion->uuid_traslado);
        return [
            $auth->has_permission('acceso', 'entradas/ver/(:any)') ? $row->numero_entrada_enlace : $row->numero_entrada,
            $row->created_at,
            $row->operacion->numero_documento_enlace,
            $row->tipo,
            $row->origen,
            $row->operacion->referencia,
            $row->operacion->bodega->nombre,
            $row->estado->etiqueta_label,
            $link_option,
            $hidden_options,
        ];
    }

    public function getColletionCellHistorialItem($row, $auth, $clause) {
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_entrada .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

        if($auth->has_permission('acceso', 'entradas/ver/(:any)')){
            $hidden_options .= '<a href="'.base_url('entradas/ver/'. $row->uuid_entrada).'" class="btn btn-block btn-outline btn-success">Ver Entrada</a>';
        }

        if($hidden_options == ""){$link_option = "&nbsp;";}

        $aux = $row->items->filter(function($item) use ($clause){
            return $item->id == $clause['item_id'];
        });
        return [
            $auth->has_permission('acceso', 'entradas/ver/(:any)') ? $row->numero_entrada_enlace : $row->numero_entrada,
            $row->created_at,
            $row->operacion->bodega->nombre,
            '',//pendiente por desarrollar //serie
            count($aux) ? $aux->first()->pivot->cantidad : 1,
            $link_option,
            $hidden_options,
        ];
    }
}
