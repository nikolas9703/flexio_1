<?php
namespace Flexio\Modulo\Inventarios\Repository;

use Illuminate\Http\Request;
use Flexio\Modulo\Inventarios\Models\Items as Items;
use Flexio\Modulo\Inventarios\Models\Items2 as Items2;
use Flexio\Modulo\Inventarios\Models\Categoria;

//utils
use Flexio\Library\HTML\HtmlRender;

class ItemsRepository{

    protected $HtmlRender;
    protected $request;
    protected $color_states = ['1' => '#5CB85C', '2' => '#D9534F', '9' => '#F0AD4E' ];

    public function __construct()
    {
        $this->HtmlRender = new HtmlRender;
        $this->request = Request::capture();
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $items = Items::deEmpresa($clause["empresa_id"]);

        $this->_filtros($items, $clause);

        if($sidx!=NULL && $sord!=NULL){$items->orderBy($sidx, $sord);}
        if($limit!=NULL){$items->skip($start)->take($limit);}

        /*$categoriasItem =  collect([]);
        $items->chunk(200,function($articulos) use(&$categoriasItem){
            foreach ($articulos as $articulo) {
                $categoriasItem->push($articulo);
            }
            return $categoriasItem;
        });
        return $categoriasItem;*/
        return $items->get();
    }

    public function count($clause = array())
    {
        $items = Items::deEmpresa($clause["empresa_id"]);

        $this->_filtros($items, $clause);

        return $items->count();
    }

    private function _filtros($items, $clause)
    {
        if(isset($clause["codigo"]) and !empty($clause["codigo"])){$items->deCodigo($clause["codigo"]);}
        if(isset($clause["nombre"]) and !empty($clause["nombre"])){$items->deNombre($clause["nombre"]);}
        if(isset($clause["categorias"]) and !empty($clause["categorias"])){$items->deCategorias($clause["categorias"]);}
        if(isset($clause["estado"]) and !empty($clause["estado"])){$items->deEstado($clause["estado"]);}
        //scopeDeCategoria contiente un select para evitar el (id) ambiguo....
        if(isset($clause["categoria_id"]) and !empty($clause["categoria_id"])){$items->deCategoria($clause["categoria_id"]);}
        if(isset($clause["uuid_bodega"]) and !empty($clause["uuid_bodega"])){$items->deBodega($clause["uuid_bodega"]);}
        if(isset($clause['campo']) and !empty($clause['campo']))$items->DeFiltro($clause['campo']);
        if(isset($clause['item_ids']) and !empty($clause['item_ids']))$items->whereIn("id",$clause['item_ids']);
    }

    public function getUltimosPrecios($item, $limit = 3)
    {
        return \Flexio\Modulo\FacturasCompras\Models\FacturaCompraItems::where('item_id', $item->id)
        ->orderBy('id', 'desc')->skip(0)->take($limit)->get()->map(function($factura_compra_item){
            return [
                "precio" => $factura_compra_item->precio_unidad,
                "unidad" => $factura_compra_item->unidad->nombre,
                "proveedor" => $factura_compra_item->factura->proveedor->nombre,
                "fecha" => $factura_compra_item->factura->fecha_desde
            ];
        });
    }

    private function limpiar_cuentas($cuentas)
    {
        return array_map(function($cuenta){
            $cuenta = str_replace('activo:', '', $cuenta);
            $cuenta = str_replace('ingreso:', '', $cuenta);
            $cuenta = str_replace('costo:', '', $cuenta);
            return str_replace('variante:', '', $cuenta);
        }, $cuentas);
    }

    public function getCollectionItems($items){

        return $items->map(function($item){
            $cuentas = json_decode($item->cuentas);
            if(is_array($cuentas)){$cuentas = $this->limpiar_cuentas($cuentas);}
            return [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'descripcion' => $item->descripcion,
                'unidades' => $item->unidades,
                'cuenta_id' => (is_array($cuentas) && count($cuentas) == 1) ? $cuentas[0] : '',
                'impuesto_id' => count($item->impuesto_compra) ? $item->impuesto_compra->id : '',
                'impuesto_uuid' => '',
                'precio_unidad' =>$item->costo_promedio,
                'precios' => $item->precios,
                'unidad_id' => $item->unidad_id,
                'atributos' => $item->atributos,
                'atributo_text' => '',
                'atributo_id' => '',
                'tipo_id' => $item->tipo_id,
                "categoria"=>$item->categorias,
                "codigo"=>$item->codigo,
                "cuentas" => $item->cuentas //string json con los id de cuentas para filtrar
                //"existencia" =>$item->comp_enInventario(),
            ];
        });

    }

    public function getCollectionItemsVentas($items){

        return $items->map(function($item){

            return [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'descripcion' => $item->descripcion,
                'unidades' => $item->unidades,
                'cuenta_id' => '',//colocar count
                'impuesto_id' => count($item->impuesto_venta) ? $item->impuesto_venta->id : '',
                'impuesto_uuid' => count($item->impuesto_venta) ? $item->impuesto_venta->uuid_impuesto : '',//se usa en facturas de ventas
                'precio_unidad' => 0,
                'tipo_id' => $item->tipo_id,
                'precios' => $item->precios,//estos son los precios de venta
                'unidad_id' => $item->unidad_id,
                'atributos' => $item->atributos,
                'atributo_text' => '',
                'atributo_id' => '',
                "categoria"=>$item->categorias,
                "codigo"=>$item->codigo,
                "cuentas" => $item->cuentas //string json con los id de cuentas para filtrar
                //"existencia" =>$item->comp_enInventario(),
            ];
        });

    }

    public function getColletionRegistros($items, $uuid_bodega = NULL)
    {
        $aux = [];

        foreach($items as $item)
        {
            $en_inventario = $item->comp_enInventario($uuid_bodega);
            $aux[] = array(
                "id"                    => $item->id,
                "uuid"                  => $item->uuid_item,
                "codigo"                => $item->codigo,
                "nombre"                => $item->nombre,
                //"cuenta_variante"       => $item->cuenta_variante->id,
                "cuenta_variante"       => '',
                "cantidad_disponible"   => $en_inventario["cantidadDisponibleBase"],
                "costo_promedio"        => $item->costo_promedio,
                "tipo_id"               => $item->tipo_id
            );
        }
        return $aux;
    }

    private function _getRegistroExportar($item, $serial)
    {
        return [
            //"ubicacion"     => "ubicacion", -> a revision
            "categoria"     => utf8_decode($item->categorias->implode("nombre", ", ")),
            "item"          => $item->codigo,
            "nombre"        => utf8_decode($item->nombre),
            "serie/candiad" => $serial,
            "unidad"        => utf8_decode($item->unidadBaseModel()->nombre),
            "notas"         => ""
        ];
    }

    public function getColletionRegistrosExportar($items, $uuid_bodega = NULL)
    {
        $aux = [];

        foreach($items as $item)
        {
            //si es de tipo serializado tiene otro comportameinto
            if($item->serializado)
            {
                foreach($item->comp_serialesEnBodega($uuid_bodega) as $serial){
                    $aux[] = $this->_getRegistroExportar($item, $serial);
                }
            }
            else
            {
                //$en_inventario = $item->comp_enInventario($uuid_bodega);
                $aux[] = $this->_getRegistroExportar($item, "");
            }

        }
        return $aux;
    }

    private function _getCell1Tipo($item, $auth, $hidden_options, $link_option, $categorias, $enInventario, $uuid_bodega = false)
    {

        return [
            $auth->has_permission('acceso', 'inventarios/ver/(:any)') ? $item->codigo_enlace : $item->codigo,
            $item->nombre,
            !empty($categorias) ? implode(", ", $categorias) : "",
            //$item->costo_promedio_label,
            $enInventario["cantidadPedidoBase"],
            $enInventario["cantidadDisponibleBase"],
            $enInventario["cantidadNoDisponibleBase"],
            $enInventario["cantidadDisponibleBase"] + $enInventario["cantidadNoDisponibleBase"] + $enInventario["cantidadPedidoBase"],
            $this->HtmlRender->setContent($item->state->etiqueta)->setBackgroundColor($this->color_states[$item->state->id_cat])->label(),
            $link_option,
            $hidden_options,
        ];
    }
    private function _getCell2Tipo($item, $auth, $hidden_options, $link_option, $categorias, $enInventario, $uuid_bodega = false)
    {
        return [
            !empty($categorias) ? implode(", ", $categorias) : "",
            $auth->has_permission('acceso', 'inventarios/ver/(:any)') ? $item->codigo_enlace : $item->codigo,
            $item->nombre,
            //$item->costo_promedio_label,
            $item->unidadBaseModel()->nombre,
            $enInventario["cantidadDisponibleBase"],
           // $item->state->etiqueta,
            $this->HtmlRender->setContent($item->state->etiqueta)->setBackgroundColor($this->color_states[$item->state->id_cat])->label(),
            $link_option,
            $hidden_options,
            ($item->tipo_id == "5" || $item->tipo_id == "8") ? $item->comp_serialesTablaHtmlEnBodega($uuid_bodega) : '<p>Este elemento no es serializado</p>'
        ];
    }

    public function getCollectionCampo($item){

        $i = $j = 0;//indices...
        foreach ($item->unidades as $key => $unidad) {
            if($unidad->pivot->base == 1){$i = $key;}
        }

        return Collect(
            array_merge(
                $item->toArray(),
                [
                    "categorias" => $item->categorias->map(function($categoria){
                        return $categoria->id;
                    }),
                    "item_unidades" => $item->unidades->map(function($unidad) use ($i, $j){
                        $j++;
                        return [
                            "id_unidad" => $unidad->id,
                            "factor_conversion" => $unidad->pivot->factor_conversion,
                            "base" => $j == 1 ? $i : $j-1
                        ];
                    }),
                    "atributos" => count($item->atributos) ? $item->atributos : [['id'=>'', 'nombre'=>'', 'descripcion'=>'']],
                    "precios" => $item->precios->map(function($precio){
                        return [
                            "precio" => $precio->pivot->precio
                        ];
                    }),
                    "precios_alquiler" => $item->precios_alquiler->map(function($precio_alquiler){
                       return  [
                            "id_precio" => $precio_alquiler->pivot->id_precio,
                            "id_item" => $precio_alquiler->pivot->id_item,
                            "hora" => $precio_alquiler->pivot->hora,
                            "diario" => $precio_alquiler->pivot->diario,
                            "semanal" => $precio_alquiler->pivot->semanal,
                            "mensual" => $precio_alquiler->pivot->mensual,
                            "tarifa_4_horas" => $precio_alquiler->pivot->tarifa_4_horas,
                            "tarifa_6_dias" => $precio_alquiler->pivot->tarifa_6_dias,
                            "tarifa_15_dias" => $precio_alquiler->pivot->tarifa_15_dias,
                            "tarifa_28_dias" => $precio_alquiler->pivot->tarifa_28_dias,
                            "tarifa_30_dias" => $precio_alquiler->pivot->tarifa_30_dias
                        ];
                    }),
                    "item_alquiler" => $item->item_alquiler == 1 ? true : false,
                    "uuid_activo" => "",
                    "uuid_ingreso" => "",
                    "uuid_gasto" => "",
                    "uuid_variante" => "",
                    "uuid_compra" => strtoupper(bin2hex($item->uuid_compra)),
                    "uuid_venta" => strtoupper(bin2hex($item->uuid_venta))
                ]
            )
        );

    }

    public function getColletionCell($item, $auth, $uuid_bodega = false, $tipo = "Cell1")
    {
        $hidden_options = "";
        $link_option    = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $item->uuid_item .'" data-id="'. $item->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

        if($auth->has_permission('acceso', 'inventarios/ver/(:any)'))
        {
            $hidden_options .= $item->btn_enlace;
            $hidden_options .= '<a href="javascript:" data-id="' . $item->uuid_item . '" class="exportarTablaCliente btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documento</a>';
        }

        //Si no tiene acceso a ninguna opcion
        //ocultarle el boton de opciones
        if($hidden_options == ""){
                $link_option = "&nbsp;";
        }
        $categorias = array();
        foreach($item->categorias as $categoria)
        {
            $categorias[] = $categoria->nombre;
            break;
        }
        $enInventario = $item->comp_enInventario($uuid_bodega);

        $aux = "_get".$tipo."Tipo";
        return $this->$aux($item, $auth, $hidden_options, $link_option, $categorias, $enInventario, $uuid_bodega);
    }


    public function getColletionRegistro($item, $uuid_bodega) {
        $enInventario       = $item->comp_enInventario($uuid_bodega);
        $ultimosTresPrecios = $item->comp_ultimosTresPrecios();

        $registro   = array(
            "descripcion"           => $item->descripcion,
            "uuid_gasto"            => strtoupper(bin2hex($item->uuid_gasto)),
            "uuid_compra"           => strtoupper(bin2hex($item->uuid_compra)),//Impuesto para compra predeterminado,

            //disponibilidad
            "enInventario"          => $enInventario,
            "disponible"            => $enInventario["cantidadDisponibleBase"] ? : 0,
            "noDisponible"          => $enInventario["cantidadNoDisponibleBase"] ? : 0,
            "totalDisponibilidad"   => $enInventario["cantidadDisponibleBase"] ? : 0,//no entiendo mucho el total de disponibilidad
            //ultimos 3 precios
            "precio1"               => $ultimosTresPrecios["precio1"],
            "precio2"               => $ultimosTresPrecios["precio2"],
            "precio3"               => $ultimosTresPrecios["precio3"],
            "costo_promedio"        => $item->costo_promedio
        );

        $registro["unidades"] = $this->_getUnidades($item);
        return $registro;
    }

    private function _getUnidades($item)
    {
        $aux = [];
        foreach ($item->unidades as $unidad)
        {
            $aux[] = array(
                "id"                => $unidad->id,
                "uuid_unidad"       => $unidad->uuid_unidad,
                "nombre"            => $unidad->nombre,
                "base"              => $unidad->pivot->base,
                "factor_conversion" => $unidad->pivot->factor_conversion
            );
        }
        return $aux;
    }

    public function find($item_id){
        return Items::find($item_id);
    }
    public function findByUuid($uuid_item) {
        return Items::where("uuid_item", hex2bin($uuid_item))->first();
    }
    public function findByUuid2($uuid_item) {
        return Items::where("uuid_item", hex2bin($uuid_item))->first();
    }
    //Listar por categoria
    public function findByCategoria($clause){
    	$query = Items::with(array("tipo", "unidades"))
			    	->deEmpresa($clause["empresa_id"])
			    	->deEstado(1);

    	if(!empty($clause["item_id"])){
    		$query->find($clause["item_id"]);
    	}
    	if(!empty($clause["categoria_id"])){
    		$query->deCategorias(array($clause["categoria_id"]));
    	}

    	return $query->get(array("id","codigo","nombre","descripcion","estado","tipo_id"));
    }

    //Items inventariado con serie
    public function findSerializadosByCategoria($clause){
    	$query = Items::with(array("tipo", "seriales"))
    			->deEmpresa($clause["empresa_id"])
    			->deTipo("con serie")
    			->deEstado(1)
    			->deCategorias(array($clause["categoria_id"]));

    	if(!empty($clause["item_id"])){
    		$query->find($clause["item_id"]);
    	}

    	return $query->get(array("id","codigo","nombre","descripcion","estado","tipo_id"));
    }

    //Items de servicio
    public function findCategoriasServicio($clause){
    	$query = Items::with(array("tipo", "categorias", "unidades"))
    		->deEmpresa($clause["empresa_id"])
	    	->deTipo("servicio");

    	if(!empty($clause["item_id"])){
    		$query->find($clause["item_id"]);
    	}
    	if(!empty($clause["categoria_id"])){
    		$query->deCategorias(array($clause["categoria_id"]));
    	}

    	return $query->get(array("id","codigo","nombre","descripcion","estado","tipo_id"));
    }

    public function getChunck($clause = array())
    {
        $items = Items::deEmpresa($clause["empresa_id"]);
        if(strpos($this->request->server('HTTP_REFERER'), 'pedidos') === false){$items->where('inv_items.estado', '!=', 9);}//solo items aprobados
        if(strpos($this->request->server('HTTP_REFERER'), 'alquiler') !== false){$items->where('inv_items.item_alquiler', 1);}//solo items de alquiler

        $this->_filtros($items, $clause);

        $items_categoria = [];
        $items->chunk(20,function($articulos) use(&$items_categoria){
            foreach ($articulos as $articulo) {
                $items_categoria[]= $articulo;
            }
            return $items_categoria;
        });
        return $items_categoria;
    }

    public function getCollectionVentas($items){
        $items = collect($items);

        return $items->map(function($item){

            $cuentas = json_decode($item->cuentas);
            $cuenta_id = "";
            foreach((array) $cuentas as $cuenta) {
                if(strrpos($cuenta, "ingreso") !== false) {
                    $cuenta_id = str_replace("ingreso:", "", $cuenta);
                }
            }

            return [
                "id" => $item->id,
                "nombre" => $item->nombre,
                "impuesto_id"  => count($item->impuesto_venta) ? $item->impuesto_venta->id : '', // impuesto no es requerido en el detalle de item
                "cuenta_id" => $cuenta_id, //count($item->cuenta_ingreso) ? $item->cuenta_ingreso->id : '',
                "cuentas" => $item->cuentas,//string json
                'atributos'=> $item->atributos,
                "categoria"=> $item->categorias,
                "codigo"=> $item->codigo,
                'unidades' => $item->unidades,
                'unidad_id' => $item->unidad_id,
                "precios" => $item->precios->map(function($precio){
                    return [
                        "precio" => $precio->pivot->precio
                    ];
                }),
                "precios_alquiler" => $item->precios_alquiler->map(function($precio_alquiler){
                   return  [
                        "id_precio" => $precio_alquiler->pivot->id_precio,
                        "id_item" => $precio_alquiler->pivot->id_item,
                        "hora" => $precio_alquiler->pivot->hora,
                        "diario" => $precio_alquiler->pivot->diario,
                        "semanal" => $precio_alquiler->pivot->semanal,
                        "mensual" => $precio_alquiler->pivot->mensual,
                        "tarifa_4_horas" => $precio_alquiler->pivot->tarifa_4_horas,
                        "tarifa_6_dias" => $precio_alquiler->pivot->tarifa_6_dias,
                        "tarifa_15_dias" => $precio_alquiler->pivot->tarifa_15_dias,
                        "tarifa_28_dias" => $precio_alquiler->pivot->tarifa_28_dias,
                        "tarifa_30_dias" => $precio_alquiler->pivot->tarifa_30_dias
                    ];
                }),
                //"existencia" =>$item->comp_enInventario()
            ];
        });
    }

    public function getItemConCategorias($busqueda = []){
        $items = Items::deEmpresa($busqueda["empresa_id"]);
        $items->with('categorias')->where(function($query) use($busqueda) {
            $query->where('nombre','LIKE',$busqueda["nombre"]."%");
            $query->where('item_alquiler',1);
            $query->where('estado','!=',9);
        });

        $items_categoria = [];
        $items->chunk(200,function($articulos) use(&$items_categoria){
            foreach ($articulos as $articulo) {
                $items_categoria[]= $articulo;
            }
            return $items_categoria;
        });
        return $items_categoria;
    }

    public function getItemsConCategoriasChunk($busqueda)
    {
        $items = Items::deEmpresa($busqueda["empresa_id"]);

        //filtro de categoria
        if(isset($busqueda["categoria_id"]) && !empty($busqueda["categoria_id"])){$items->deCategoria($busqueda["categoria_id"]);}
        if(isset($busqueda["activo"]) && $busqueda["activo"]){$items->where('inv_items.estado',1);}
        if(strpos($this->request->server('HTTP_REFERER'), 'pedidos') === false){$items->where('inv_items.estado', '!=', 9);}//solo items aprobados

        $items->with('categorias','atributos','unidades')->where(function($query) use($busqueda) {
            $aux = explode(" ", $busqueda["nombre"]);
            //buscar por nombre
            $query->where(function($query1) use ($aux) {
                //for para cada una de las palabras
                foreach($aux as $palabra){
                    $query1->where("nombre", "like", "%$palabra%");
                }
            });
            //buscar por codigo
            $query->orWhere(function($query2) use ($aux){
                //for para cada una de las palabras
                foreach($aux as $palabra){
                    $query2->where("codigo", "like", "%$palabra%");
                }
            });
        })->skip(0)->take(20);

        return $items->get();
    }

    public function newCatItems($busqueda)
    {
      $aux = [];
      $categoria = Categoria::find($busqueda['categoria_id']);

      $items = $categoria->items()->skip(0)->take(20)->where(function($query) use ($busqueda){
          if(isset($busqueda["item_id"]) && !empty($busqueda["item_id"])){$query->where("inv_items.id",$busqueda["item_id"]);}
          if(isset($busqueda["activo"]) && $busqueda["activo"]){$query->where('inv_items.estado',1);}
          if(strpos($this->request->server('HTTP_REFERER'), 'pedidos') === false){$query->where('inv_items.estado', '!=', 9);}//solo items aprobados
      })->get();
      $items->load('unidades','atributos');
      return $items;

    }

    public function getItemsChunk($busqueda){

        $items = Items::deEmpresa($busqueda["empresa_id"]);
        //$items->deCategoria($busqueda["categoria_id"]);
        $items->deEstado($busqueda["estado"]);
        $items->where('nombre', 'like', "%".$busqueda['nombre']."%");
        $items->skip(0)->take(20);
        $articulos = $items->get(["id", "nombre", "codigo","cuentas",'uuid_venta']);
        $articulos->load("precios","unidades","atributos");
        return $articulos;
    }


}
