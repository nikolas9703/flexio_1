<?php
namespace Flexio\Modulo\Inventarios\Repository;

//modelos
use Flexio\Modulo\Inventarios\Models\Seriales as Seriales;
use Flexio\Modulo\Inventarios\Models\SerialesLineas as SerialesLineas;
use Flexio\Modulo\Inventarios\Models\Items as Items;

//repositories
use Flexio\Modulo\Inventarios\Repository\ItemsRepository;

class SerialesRepository{
    //SerialesRepo
    protected $ItemsRepository;

    public function __construct()
    {
        $this->ItemsRepository = new ItemsRepository;
    }

    public function count($clause = array())
    {
        $seriales = Seriales::where(function($query) use ($clause){
            $this->_filtro($query, $clause);
        });
        return $seriales->count();
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $seriales = Seriales::where(function($query) use ($clause){
            $this->_filtro($query, $clause);
        });

        if($sidx!=NULL && $sord!=NULL){$seriales->orderBy($sidx, $sord);}
        if($limit!=NULL){$seriales->skip($start)->take($limit);}
        return $seriales->get();
    }

    public function findBy($clause = array())
    {
        $seriales = Seriales::where(function($query) use ($clause){
            $this->_filtro($query, $clause);
        });

        return $seriales->first();
    }

    public function getCollectionCellSeries($serie, $auth) {

        $hidden_options = "";
        $link_option    = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $serie->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

        if($auth->has_permission('acceso', 'inventarios/ver/(:any)'))
        {
            $hidden_options .= $serie->ultimo_movimiento_btn_enlace;
            $hidden_options .= $serie->trazabilidad_btn_enlace;
        }

        //Si no tiene acceso a ninguna opcion
        //ocultarle el boton de opciones
        if($hidden_options == ""){
                $link_option = "&nbsp;";
        }

        return [
            $serie->nombre,
            $serie->ubicacion,
            $serie->ultimo_movimiento_numero_documento_enlace,
            $link_option,
            $hidden_options
        ];
    }

    public function delete($clause = array())
    {
        $seriales = Seriales::deItem($clause["item_id"]);

        $this->_filtro($seriales, $clause);

        //dd($seriales->get()->toArray(), $clause);
        return $seriales->delete();
    }

    private function _filtro($seriales, $clause)
    {
        if(isset($clause["line_id"]) and !empty($clause["line_id"])){$seriales->deLinea($clause["line_id"]);}
        if(isset($clause["uuid_serial"]) and !empty($clause["uuid_serial"])){$seriales->deUuid($clause["uuid_serial"]);}
        if(isset($clause["nombre_item"]) and !empty($clause["nombre_item"])){$seriales->deNombreItem($clause["nombre_item"]);}
        if(isset($clause["categorias"]) and !empty($clause["categorias"])){$seriales->deCategorias(explode(",", $clause["categorias"]));}
        if(isset($clause["estado"]) and !empty($clause["estado"])){$seriales->whereEstado($clause["estado"]);}
        if(isset($clause["nombre"]) and !empty($clause["nombre"])){$seriales->whereNombre($clause["nombre"]);}
        if(isset($clause["item_id"]) and !empty($clause["item_id"])){$seriales->whereItemId($clause["item_id"]);}
        if(isset($clause["empresa_id"]) and !empty($clause["empresa_id"])){$seriales->whereEmpresaId($clause["empresa_id"]);}
        if(isset($clause['campo']) and !empty($clause['campo']))$seriales->DeFiltro($clause['campo']);
        if(isset($clause["buscar_en"]) and $clause["buscar_en"] == 'bodega')
        {
            if(isset($clause["bodega_id"]) and !empty($clause["bodega_id"])){$seriales->whereBodegaId($clause["bodega_id"]);}
        }
        elseif(isset($clause["buscar_en"]) and $clause["buscar_en"] == 'cliente')
        {
            if(isset($clause["clientes"]) and !empty($clause["clientes"])){$seriales->whereIn('cliente_id', explode(",", $clause['clientes']));}
            if(isset($clause["centros_facturacion"]) and !empty($clause["centros_facturacion"])){$seriales->whereIn('centro_facturacion_id', explode(",", $clause['centros_facturacion']));}
        }

    }

    public function save($item, $registro_aux = null, $estado = 'disponible'){

        SerialesLineas::where("line_id", $item["id_entrada_item"])->delete();
        $itemA = is_numeric($item["item"]) ? Items::find($item["item"]) : Items::findByUuid($item["item"]);
        if(isset($item["seriales"]))
        {
            foreach($item["seriales"] as $serial)
            {
                if(!empty($serial))
                {
                    $registro = (Seriales::where("nombre", $serial)->where("item_id", $itemA->id)->count() > 0) ? Seriales::where("nombre", $serial)->where("item_id", $itemA->id)->first() : new Seriales();
                    $registro->nombre = $serial;
                    $registro->item_id = $itemA->id;
                    $registro->empresa_id = $registro ? $registro_aux->empresa_id : 0;
                    $registro->estado = $estado;
                    //inside this method the states cab be updated....
                    $this->_setBodegaOrCliente($registro, $registro_aux);

                    $registro->save();

                    $serialLinea            = new SerialesLineas();
                    $serialLinea->line_id   = $item["id_entrada_item"];
                    $serialLinea->serial_id = $registro->id;
                    $serialLinea->save();
                }
            }
        }
    }

    private function _setBodegaOrCliente($serie, $class)
    {
        //class = salida | entrada | ajuste (class format)
        if(in_array($class->operacion->modulo, ['Orden de venta']))
        {
            $serie->cliente_id = $class->operacion->cliente_id;
            $serie->centro_facturacion_id = $class->operacion->centro_facturacion_id;
            $serie->bodega_id = 0;
        }
        else
        {
            $serie->cliente_id = 0;
            $serie->centro_facturacion_id = 0;

            $bodega = ($class->modulo !== 'Ajuste') ? $class->operacion->bodega : $class->bodega;
            $serie->bodega_id = $bodega->id;
            if($bodega->estado_items == '4'){//no disponible
                $serie->estado = 'no_disponible';
            }
        }
    }

    public function getCollectionSerie($serie)
    {
        return Collect(array_merge(
            $serie->toArray(),
            [
                'nombre_item' => $serie->items->nombre_completo,
                'adquisicion' => $serie->adquisicion,
                'otros_costos' => $serie->otros_costos,
                'depreciacion' => $serie->depreciacion,
                'valor_actual' => $serie->valor_actual,
                'estado' => $serie->catalogo_estado->valor,
                'fecha_compra' => $serie->primer_Movimiento->fecha_creacion,
                'edad' => $serie->primer_Movimiento->edad,
                'um' => [
                    'modulo' => $serie->ultimo_movimiento->modulo,
                    'numero' => $serie->ultimo_movimiento->numero_documento_enlace,
                    'nombre' => $serie->ultimo_movimiento->modulo != 'Ajuste' ? $serie->ultimo_movimiento->externo->nombre : $serie->ultimo_movimiento->ultimo_movimiento_nombre,
                    'ubicacion' => isset($serie->ultimo_movimiento->ubicacion) ? $serie->ultimo_movimiento->ubicacion->nombre : '',
                    'fecha_hora' => $serie->ultimo_movimiento->fecha_hora,
                ],
                'item' => $this->ItemsRepository->getCollectionCampo($serie->items)
            ]
        ));
    }

    public function getResponseRows($series)
    {
        $rows = [];
        foreach ($series as $i => $row){

            $hidden_options = $row->hidden_options;
            $link_option = $row->link_option;

            $rows[$i]["id"] = $row->id;
            $rows[$i]["cell"] = [
                $row->numero_documento_enlace,
                $row->items->nombre_completo,
                $row->ultimo_movimiento->numero_documento_enlace,
                $this->_get_ultimo_movimiento($row),
                $row->present()->estado,//falta migracion
                $link_option,
                $hidden_options
            ];
        }

        return $rows;
    }

    private function _get_ultimo_movimiento($row)
    {
        if(preg_match('/devoluciones_alquiler/', $row->ultimo_movimiento->enlace) && $row->ultimo_movimiento->estado_id == 2)
        {
            return count($row->bodega) ? $row->bodega->nombre : '';
        } else if(preg_match('/ajustes/', $row->ultimo_movimiento->enlace)){
            return count($row->bodega) ? $row->bodega->nombre : '';
        }

        return count($row->ultimo_movimiento->ubicacion) ? $row->ultimo_movimiento->ubicacion->nombre : 'undefined';
    }
}
