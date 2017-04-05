<?php
namespace Flexio\Modulo\ContratosAlquiler\Repository;

use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerHistorial;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItems;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerCatalogosRepository;
//Para traer nombres
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class ContratosAlquilerRepository
{

    protected $ContratosAlquilerCatalogosRepository;

    public function __construct()
    {
        $this->ContratosAlquilerCatalogosRepository = new ContratosAlquilerCatalogosRepository();
    }

    private function _filtros($query, $clause)
    {

        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        //falta relacion de contratos y if(isset($clause['cotizacion_alquiler_id']) and !empty($clause['cotizacion_alquiler_id'])){$query->whereEmpresaId($clause['cotizacion_alquiler_id']);}
        if(isset($clause['codigo']) and !empty($clause['codigo'])){$query->deCodigo($clause['codigo']);}
        if(isset($clause['estado_id']) and !empty($clause['estado_id'])){$query->whereEstadoId($clause['estado_id']);}
        if(isset($clause['uuid_contrato_alquiler']) and !empty($clause['uuid_contrato_alquiler'])){$query->whereUuidContratoAlquiler(hex2bin($clause['uuid_contrato_alquiler']));}
        if(isset($clause['id']) and !empty($clause['id'])){$query->where("id", $clause['id']);}
        if(isset($clause['creado_por']) and !empty($clause['creado_por'])){$query->where("created_by", $clause['creado_por']);}
        if(isset($clause['centro_contable_id']) and !empty($clause['centro_contable_id'])){$query->where("centro_contable_id", $clause['centro_contable_id']);}

		if(isset($clause['fecha_desde']) and !empty($clause['fecha_desde'])){
			//$query->desde($clause['fecha_desde']);
			$query->whereDate("fecha_inicio",">=",$clause['fecha_desde']);
		}
        if(isset($clause['fecha_hasta']) and !empty($clause['fecha_hasta'])){
			//$query->hasta($clause['fecha_hasta']);
			$query->whereDate("fecha_fin","<=",$clause['fecha_hasta']);
		}
        if(isset($clause['cliente_id']) and !empty($clause['cliente_id'])){
			if(is_array($clause["cliente_id"]) and count($clause["cliente_id"])>0 ){
				if($clause["cliente_id"][0]!=""){
					$query->whereIn("cliente_id",$clause['cliente_id']);
				}
			}
		}
        if(isset($clause['categoria']) and !empty($clause['categoria'])){
			if(is_array($clause["categoria"]) and count($clause["categoria"])>0 ){
				if($clause["categoria"][0]!=""){
					$categs = implode(",",$clause["categoria"]);
					$query->whereRaw("id in (SELECT contratable_id FROM contratos_items WHERE contratable_type = 'Flexio\\\Modulo\\\ContratosAlquiler\\\Models\\\ContratosAlquiler' AND categoria_id IN (".$categs.")) ");
				}
			}
		}
        if(isset($clause['centro_contable_id']) and !empty($clause['centro_contable_id'])){
			$query->where("centro_contable_id",$clause['centro_contable_id']);
		}

    }

    private function _getHiddenOptions($contrato_alquiler, $auth)
    {
        $hidden_options = "";

        if($auth->has_permission('acceso', 'contratos_alquiler/editar/(:any)'))
        {
            $hidden_options = '<a href="'.$contrato_alquiler->enlace.'" data-id="'. $contrato_alquiler->uuid_contrato_alquiler .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
        }
        //Poner persmiso para dcumentos
        //if($auth->has_permission('acceso', 'contratos_alquiler/veer/(:any)'))
        //{
        $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="'.$contrato_alquiler->id.'" data-codigo="'. $contrato_alquiler->codigo .'" >Subir documentos</a>';
        //}

        //if($auth->has_permission('acceso', 'contratos_alquiler/veer/(:any)')) //Aqui Permiso para bitacora
        //{
        $hidden_options .= '<a href="'.$contrato_alquiler->enlace_bitacora.'" data-id="'. $contrato_alquiler->uuid_contrato_alquiler .'" class="btn btn-block btn-outline btn-success">Ver bit&aacute;cora</a>';
        //}

        //Crear Orden de Venta
        if(preg_match('/(vigente)/i', $contrato_alquiler->estado->nombre)){
          $hidden_options .= '<a href="'. base_url('ordenes_alquiler/crear/contrato_alquiler/'. $contrato_alquiler->id) .'"  class="btn btn-block btn-outline btn-success">Crear Orden de Venta</a>';
        }
        if(preg_match('/(vigente)/i', $contrato_alquiler->estado->nombre)){
        $hidden_options .= '<a href="#" data-id="'. $contrato_alquiler->uuid_contrato_alquiler .'" class="btn btn-block btn-outline btn-success facturar">Facturar</a>';
        }
        return $hidden_options;
    }

    public function getCollectionCampo($contrato_alquiler)
    {
        $articulos = new \Flexio\Library\Articulos\ArticuloAlquiler;

        return Collect([
            'id' => $contrato_alquiler->id,
            'empezar_desde_type' => 'cliente',
            'cliente_id' => $contrato_alquiler->cliente_id,
            'empezar_desde_id' => $contrato_alquiler->cliente_id,
            'codigo' => $contrato_alquiler->codigo,
            'referencia' => $contrato_alquiler->referencia,
            'centros_facturacion' => $contrato_alquiler->cliente->centro_facturable,
            'centro_facturacion_id' => $contrato_alquiler->centro_facturacion_id,
            'facturar_contra_entrega_id' => $contrato_alquiler->facturar_contra_entrega_id,
            'saldo' => $contrato_alquiler->cliente->saldo_pendiente,
            'credito' => $contrato_alquiler->cliente->credito_favor,
            'fecha_inicio' => $contrato_alquiler->fecha_inicio->format('d/m/Y'),
            'fecha_fin' => $contrato_alquiler->fecha_fin != "0000-00-00 00:00:00" && $contrato_alquiler->fecha_fin != "" ? $contrato_alquiler->fecha_fin->format('d/m/Y') : "",
            'corte_facturacion_id' => $contrato_alquiler->corte_facturacion_id,
            'calculo_costo_retorno_id' => $contrato_alquiler->calculo_costo_retorno_id,
            'lista_precio_alquiler_id' => $contrato_alquiler->lista_precio_alquiler_id,
            'dia_corte' => $contrato_alquiler->dia_corte ? : '0',
            'vendedor_id' => $contrato_alquiler->created_by,
            'estado_id' => $contrato_alquiler->estado_id,
            'observaciones' => $contrato_alquiler->observaciones,
            'centro_contable_id' => $contrato_alquiler->centro_contable_id,
            'articulos' => $articulos->get($contrato_alquiler->contratos_items)
        ]);
    }

    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $contratos_alquiler = ContratosAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        if($sidx !== null && $sord !== null){$contratos_alquiler->orderBy($sidx, $sord);}
        if($limit != null){$contratos_alquiler->skip($start)->take($limit);}
        return $contratos_alquiler->get();
    }

    public function getCollectionEmpezarDesde($clause){

      $contratos = collect($this->getContratosValidos($clause)->toArray());
      return $collection = $contratos->map(function ($item, $key) {

          $id = !empty($item["id"]) ? $item["id"] : "";
          $cliente = !empty($item["cliente"]["nombre"]) ? " - ".$item["cliente"]["nombre"] : "";
          $codigo = !empty($item["codigo"]) ? $item["codigo"] : "";
          $cliente_id = !empty($item["cliente_id"]) ? $item["cliente_id"] : "";

          return array(
            "id" => $id,
            "nombre" => $codigo . $cliente,
            "cliente_id" => $cliente_id
          );
      });
    }

    //Listar contratos de alquiler
    //con estado vigente que puedan
    //ser facturados.
    function getContratosValidos($clause) {
    	return ContratosAlquiler::with(array("cliente"))->deEmpresa($clause)->estadoValido()->get();
    }

    public function findBy($clause)
    {
        $contrato_alquiler = ContratosAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $contrato_alquiler->first();
    }
    function find($id) {
        return ContratosAlquiler::find($id);
    }
    public function getCollectionCell($contrato_alquiler, $auth)
    {
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $contrato_alquiler->uuid_contrato_alquiler .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

		$centro = CentrosContables::find($contrato_alquiler->centro_contable_id);
		$usuario = Usuarios::find($contrato_alquiler->created_by);

        return [
            $contrato_alquiler->uuid_contrato_alqquiler,
            $contrato_alquiler->numero_documento_enlace,
            !empty($contrato_alquiler) && !empty($contrato_alquiler->cliente) ? $contrato_alquiler->cliente->nombre_completo_enlace : "",
            count($contrato_alquiler->centro_facturacion) ? $contrato_alquiler->centro_facturacion->nombre : '',
            $contrato_alquiler->fecha_inicio->format('d/m/Y'),
            ($contrato_alquiler->fecha_fin != "") ? $contrato_alquiler->fecha_fin->format('d/m/Y') : "",
            $contrato_alquiler->saldo_facturar_label,
            $contrato_alquiler->total_facturado_label,
            $centro->nombre,
            $usuario->nombre." ".$usuario->apellido,
            $contrato_alquiler->estado->nombre_span,
            $link_option,
            $this->_getHiddenOptions($contrato_alquiler, $auth),
         ];

    }

    public function getCollectionCell2($contrato_alquiler, $auth)
    {
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $contrato_alquiler->uuid_contrato_alquiler .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

        return [
            $contrato_alquiler->uuid_contrato_alqquiler,
            $contrato_alquiler->numero_documento_enlace,
            $contrato_alquiler->fecha_inicio->format('d/m/Y'),
            $contrato_alquiler->items->count(),
            $contrato_alquiler->saldo_facturar_label,
            $contrato_alquiler->total_facturado_label,
            $link_option,
            $this->_getHiddenOptions($contrato_alquiler, $auth)
        ];

    }
    public function getItemFromAlquiler($uuid){

     $data= ContratosAlquilerItems::leftJoin("contratos_items","conalq_contratos_alquiler.id","=","contratos_items.contratable_id")
     ->leftJoin("inv_categorias","inv_categorias.id","=","contratos_items.categoria_id")
     ->leftJoin ("contratos_items","contratos_items.id","=","contratos_items.item_id") 
     ->where("contratos_items.uuid_contrato_alquiler" ,hex2bin($uuid))
     ->get();
     return $data;
   }
    public function getCollectionExportar($contratos_alquiler)
    {
        $aux = [];

        foreach ($contratos_alquiler as $contrato_alquiler)
        {
            $aux[] = [
                $contrato_alquiler->numero_documento,
                utf8_decode($contrato_alquiler->cliente->nombre),
                count($contrato_alquiler->centro_facturacion) ? utf8_decode($contrato_alquiler->centro_facturacion->nombre) : '',
                count($contrato_alquiler->centro_contable) ? utf8_decode($contrato_alquiler->centro_contable->nombre) : '',
                $contrato_alquiler->fecha_inicio->format('d/m/Y'),
                ($contrato_alquiler->fecha_fin!="") ? $contrato_alquiler->fecha_fin->format('d/m/Y') : "",
                $contrato_alquiler->saldo_facturar_currency,
                $contrato_alquiler->total_facturado_currency,
                count($contrato_alquiler->centro_contable) ? utf8_decode($contrato_alquiler->centro_contable->nombre) : '',
                utf8_decode($contrato_alquiler->usuario->nombre." ".$contrato_alquiler->usuario->apellido),
                utf8_decode($contrato_alquiler->estado->nombre)
            ];
        }

        return $aux;
    }

    public function count($clause = array())
    {
        $contratos_alquiler = ContratosAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $contratos_alquiler->count();
    }

    private function _setItems($contrato_alquiler, $items)
    {
         $ciclos_tarifarios = $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'tarifa']);

         $contrato_alquiler->contratos_items()->whereNotIn('id',array_pluck($items,'id'))->delete();

         foreach($items as $item){

                 $contrato_item_id = (isset($item['id']) and !empty($item['id'])) ? $item['id'] : '';
                 $contrato_item = $contrato_alquiler->contratos_items()->firstOrNew(['id'=>$contrato_item_id]);

                 $contrato_item->categoria_id = $item["categoria_id"];
                 $contrato_item->item_id = $item["item_id"];
                 $contrato_item->cantidad = $item["cantidad"];
                 //duplicado pero con tipos distintos
                 $contrato_item->ciclo_id = $ciclos_tarifarios->where('valor',$item["periodo_tarifario"])->first()->id;
                 $contrato_item->periodo_tarifario = $item["periodo_tarifario"];
                 //duplicado
                 $contrato_item->tarifa = $item["precio_unidad"];
                 $contrato_item->precio_unidad = $item["precio_unidad"];
                 //...
                 $contrato_item->atributo_id = (isset($item['atributo_id']) && !empty($item['atributo_id'])) ? $item['atributo_id'] : '';
                 $contrato_item->atributo_text = (isset($item['atributo_text']) && !empty($item['atributo_text'])) ? $item['atributo_text'] : '';
                 //duplicado
                 $contrato_item->impuesto = $item["impuesto_id"];
                 $contrato_item->impuesto_id = $item["impuesto_id"];
                 //...
                 $contrato_item->descuento = $item["descuento"];//migrate to decimal 10,2
                 $contrato_item->cuenta_id = $item["cuenta_id"];
                 $contrato_item->comentario = $item["comentario"];
                 $contrato_item->impuesto_total = $item["impuesto_total"];
                 $contrato_item->descuento_total = $item["descuento_total"];

                 $contrato_item->save();
         }
    }

    private function _save($contrato_alquiler, $post)
    {
        $campo = $post['campo'];

        $contrato_alquiler->cliente_id = $campo['cliente_id'];
        $contrato_alquiler->estado_id = $campo['estado_id'];
        $contrato_alquiler->referencia = $campo['referencia'];
        $contrato_alquiler->centro_facturacion_id = $campo['centro_facturacion_id'];
        $contrato_alquiler->corte_facturacion_id = $campo['corte_facturacion_id'];
        $contrato_alquiler->facturar_contra_entrega_id = $campo['facturar_contra_entrega_id'];
        $contrato_alquiler->calculo_costo_retorno_id = $campo['calculo_costo_retorno_id'];
        $contrato_alquiler->lista_precio_alquiler_id = $campo['lista_precio_alquiler_id'];
        if(isset($campo['dia_corte'])){
        $contrato_alquiler->dia_corte = $campo['dia_corte'];
        }
        $contrato_alquiler->fecha_inicio = $campo['fecha_inicio'];
        $contrato_alquiler->fecha_fin = $campo['fecha_fin'];
        $contrato_alquiler->created_by = $campo['vendedor_id'];
        $contrato_alquiler->observaciones = $campo['observaciones'];//falta en database
        $contrato_alquiler->centro_contable_id = $campo['centro_contable_id'];//falta en database

        $contrato_alquiler->save();
    }

    public function create($post)
    {
        $campo = $post['campo'];
        $contrato_alquiler = new ContratosAlquiler();

        $contrato_alquiler->codigo = $campo['codigo'];
        $contrato_alquiler->empresa_id = $campo['empresa_id'];
        $contrato_alquiler->tipo = ($post['empezable_type'] == 'cliente')?'cliente':'cotizacion-'.$post['empezable_id'];
        $contrato_alquiler->tipoid = $post['empezable_id'];
        $this->_save($contrato_alquiler, $post);
        $this->_setItems($contrato_alquiler, $post['items_alquiler']);

        return $contrato_alquiler;
    }

    public function save($post)
    {
        $contrato_alquiler = ContratosAlquiler::find($post['campo']['id']);

        $this->_save($contrato_alquiler, $post);
        $this->_setItems($contrato_alquiler, $post['items_alquiler']);

        return $contrato_alquiler;
    }

    function agregarComentario($ordenId, $comentarios) {
        $contrato = ContratosAlquiler::find($ordenId);
     	  $comentario = new Comentario($comentarios);
        $contrato->comentario_timeline()->save($comentario);
        return $contrato;
    }
      function addHistorial($objetoModulo = array(),  $objeto_comentario ){

        $descripcion = $objeto_comentario['comentario'];
        $tipo = $objeto_comentario['tipo'];
        $titulo =($tipo=='comentario')?"<b style='color:#0080FF; font-size:15px;'>Comentó</b></br>":"<b style='color:#0080FF; font-size:15px;'>Documentos</b></br>";
        $create = [
          'codigo' => $objetoModulo->codigo,
          'usuario_id' => $objetoModulo->created_by,
          'empresa_id' => $objetoModulo->empresa_id,
          'contrato_id'=> $objetoModulo->id,
          'tipo'   => $tipo,
          'descripcion' => $titulo.$descripcion
      ];
        ContratosAlquilerHistorial::create($create);
   }

   static function findByUuid($uuid) {
       return ContratosAlquiler::where('uuid_contrato_alquiler',hex2bin($uuid))->first();
   }
   
  

}
