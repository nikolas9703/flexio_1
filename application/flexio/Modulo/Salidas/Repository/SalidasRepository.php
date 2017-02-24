<?php
namespace Flexio\Modulo\Salidas\Repository;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;

use Flexio\Modulo\Salidas\Models\Salidas as Salidas;
use Flexio\Modulo\Inventarios\Models\Unidades as Unidades;

//repositorios
use Flexio\Modulo\Inventarios\Repository\SerialesRepository as serialesRep;
use Flexio\Modulo\Inventarios\Repository\LinesItemsRepository as linesItemsRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository as cuentasRep;
use Flexio\Modulo\OrdenesVentas\Repository\OrdenVentaRepository;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepository;

class SalidasRepository implements SalidasInterface{

    private $serialesRep;
    private $linesItemsRep;
    private $itemsRep;
    private $cuentasRep;

    //variables locales
    private $prefijo = "SAL";

    public function __construct() {
        $this->serialesRep      = new serialesRep();
        $this->linesItemsRep    = new linesItemsRep();
        $this->itemsRep         = new itemsRep();
        $this->cuentasRep       = new cuentasRep();
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $salidas = Salidas::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($salidas, $clause);

        if($sidx!=NULL && $sord!=NULL){$salidas->orderBy($sidx, $sord);}
        if($limit!=NULL){$salidas->skip($start)->take($limit);}

        return $salidas->get();
    }

    public function find($entrada_id)
    {
        return Salidas::find($entrada_id);
    }

    private function _setSeriales($items, $registro)
    {
        foreach($items as $item)
        {
            $itemAux    = $this->itemsRep->findByUuid($item["item"]);
            if(!empty($itemAux->id) and !empty($item["id_entrada_item"]))
            {
                //esta linea se borra para el mismo registro, de manera que la salida se le da guardar varias
                //veces no se duplique el registro.
                //$this->serialesRep->delete(["item_id" => $itemAux->id, "line_id" => $item["id_entrada_item"]]);

                //popular items nuevamente
                $estado = $registro->estado_id == '1' ? 'disponible' : 'no_disponible';
                $this->serialesRep->save($item, $registro, $estado);
            }
            else
            {
                echo "no se han recibido todos los datos _setSeriales";
                die();
            }
        }
    }

    //falta por realizar
    public function create($clause)
    {
        $salida = Salidas::deEmpresa($clause["empresa_id"])
                ->deTipo($clause["tipo"])
                ->deTipoId($clause["tipo_id"])
                ->first();

        if(!count($salida))
        {
            $numero                     = Salidas::deEmpresa($clause["empresa_id"])->count();

            $salida                     = new Salidas;
            $salida->uuid_salida        = Capsule::raw("ORDER_UUID(uuid())");
            $salida->prefijo            = $this->prefijo;
            $salida->numero             = $numero + 1;
            $salida->empresa_id         = $clause["empresa_id"];
            $salida->operacion_id       = $clause["tipo_id"];
            $salida->operacion_type     = $clause["tipo"];
            $salida->comentarios        = "";
        }

        $salida->estado_id    = $clause["estado_id"];
        $salida->save();

        //Verifico si no tiene registros para mostrar para proceder a borrar
        if(count($salida->operacion->items) == 0)
        {
            $salida->delete();
        }
    }


    public function save($registro, $post)
    {
        //una salida
        $campo = $post["campo"];

        $registro->comentarios  = $campo["comentarios"];
        $registro->estado_id    = $campo["estado"];

        //si la salida proviene desde un traslado y esta
        //no esta en estado Recibido (3) coloco el
        //traslado en transito
        //esto es porque los traslados a una bodega automatica
        //la marcan de forma automatica como un traslado recibido (3)
        if($registro->operacion_type == "Flexio\Modulo\Traslados\Models\Traslados" and $registro->operacion->id_estado != "3")
        {
            $registro->operacion->id_estado     = 2;//Traslado en transito
            $registro->operacion->save();
        }

        //seteo los seriales y la relacion lines_items
        $this->_setSeriales($post["items"], $registro);


        return $registro->save();
    }

    public function count($clause = array())
    {
        $salidas = Salidas::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($salidas, $clause);

        return $salidas->count();
    }

    private function _filtros($salidas, $clause)
    {
        //post
        if(isset($clause["fecha_desde"]) and !empty($clause["fecha_desde"])){$salidas->deFechaDesde($clause["fecha_desde"]);}
        if(isset($clause["fecha_hasta"]) and !empty($clause["fecha_hasta"])){$salidas->deFechaHasta($clause["fecha_hasta"]);}
        if(isset($clause["destino"]) and !empty($clause["destino"])){$salidas->deDestino($clause["destino"]);}
        if(isset($clause["enviar_desde"]) and !empty($clause["enviar_desde"])){$salidas->deEnviarDesde($clause["enviar_desde"]);}
        if(isset($clause["estado"]) and !empty($clause["estado"])){$salidas->deEstado($clause["estado"]);}
        if(isset($clause["numero"]) and !empty($clause["numero"])){$salidas->deNumero($clause["numero"]);}
        if(isset($clause["tipo"]) and !empty($clause["tipo"])){$salidas->deTipo($clause["tipo"]);}
        if(isset($clause["campo"]) and !empty($clause["campo"])){$salidas->deFiltro($clause["campo"]);}
        if(isset($clause["factura_uuid"]) and !empty($clause['factura_uuid'])){
            //traer las salidas de la factura
            $factura = (new FacturaVentaRepository)->findByUuid($clause['factura_uuid']);


            $ordenes_de_venta = $factura->orden_venta;
                        $idsdeOrdenes = array();
                        $i = 0;
                        foreach ($ordenes_de_venta as $orden_venta) {
                            $idsdeOrdenes[$i] = $orden_venta->id;
                            $i++;
            }
            $salidas->whereHas("orden_venta", function($salidas) use ($idsdeOrdenes) {
                            $salidas->whereIn('id', $idsdeOrdenes);
            });
        }
        //desde subpanel
        if(isset($clause["operacion_type"]) and !empty($clause["operacion_type"])){$salidas->deTipo($clause["operacion_type"]);}
        if(isset($clause["operacion_id"]) and !empty($clause["operacion_id"])){$salidas->deTipoId($clause["operacion_id"]);}

        //otros
        if(isset($clause["estados_validos"]) and !empty($clause["estados_validos"])){$salidas->deEstadosValidos();}
        if(isset($clause["item_id"]) and !empty($clause["item_id"])){$salidas->deItem($clause["item_id"]);}
    }

    public function findByUuid($uuid = NULL) {
        if(!$uuid){die("findByUuid no puede ser null");}
        return Salidas::where("uuid_salida", hex2bin($uuid))->first();
    }

    public function getColletionCampos($salida) {
        return [
            "fecha"             => $salida->created_at,
            "bodega_salida"     => $salida->operacion->origen->uuid_bodega,
            //EL DESTINO SE LLENA DESDE EL FRONTEND (CLIENTE, COLABORADOR, BODEGA)
            "numero_salida"     => $salida->numero_salida,
            "numero_documento"  => $salida->operacion->numero_documento,
            "estado"            => $salida->estado_id,
            "comentarios"       => $salida->comentarios,
            "salida_id"         => $salida->id
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
                "tipo_id"           => $row->tipo_id,//5 y 8 se registran las series var
                "observacion"       => $row->pivot->observacion,
                "cantidad_enviada"  => $row->pivot->cantidad,
                "unidades"          => $row->unidades,
                "unidad"            => Unidades::find($row->pivot->unidad_id)->uuid_unidad,
                "cuenta"            => !empty($row->pivot->cuenta_id) ? $this->cuentasRep->find($row->pivot->cuenta_id)->uuid_cuenta : "",
                "seriales"          => $seriales->toArray(),
                "id_entrada_item"   => $row->pivot->id
            );
        }

        return $colletionsItems;
    }

    public function getColletionCell($row, $auth) {
        $hidden_options = "";
        $link_option    = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_salida .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

        if($auth->has_permission('acceso', 'salidas/ver/(:any)')){
            $hidden_options .= $row->numero_salida_btn;
        }

        if($hidden_options == ""){
            $link_option = "&nbsp;";
        }

        return [
            $auth->has_permission('acceso', 'salidas/ver/(:any)') ? $row->numero_salida_enlace : $row->numero_salida,
            $row->created_at,
            count($row->operacion) ? $row->operacion->numero_documento_enlace : "Registro Roto",
            $row->tipo,
            (count($row->operacion) and count($row->operacion->destino)) ? $row->operacion->destino->nombre_completo_enlace : "N/A",
            (count($row->operacion) and count($row->operacion->origen)) ? $row->operacion->origen->nombre_completo_enlace : "N/A",//$row->comp__origen(),
            $row->estado->etiqueta_label,
            $link_option,
            $hidden_options,
        ];
    }

    public function getColletionCellHistorialItem($row, $auth, $clause) {
        $hidden_options = "";
        $link_option    = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_salida .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

        if($auth->has_permission('acceso', 'salidas/ver/(:any)')){
            $hidden_options .= $row->numero_salida_btn;
        }

        if($hidden_options == ""){
            $link_option = "&nbsp;";
        }

        return [
            $auth->has_permission('acceso', 'salidas/ver/(:any)') ? $row->numero_salida_enlace : $row->numero_salida,
            $row->created_at,
            (count($row->operacion) and count($row->operacion->origen)) ? $row->operacion->origen->nombre_completo_enlace : "N/A",//$row->comp__origen(),
            '',//no es factible
            $row->items->filter(function($item) use ($clause){
                return $item->id == $clause['item_id'];
            })->first()->pivot->cantidad,
            $link_option,
            $hidden_options,
        ];
    }

}
