<?php
namespace Flexio\Modulo\CotizacionesAlquiler\Repository;

use Flexio\Modulo\CotizacionesAlquiler\Models\CotizacionesAlquiler;

class CotizacionesAlquilerRepository
{

    private function _filtros($query, $clause)
    {
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
//      if(isset($clause['codigo']) and !empty($clause['codigo'])){$query->deCodigo($clause['codigo']);}
//      if(isset($clause['cliente_id']) and !empty($clause['cliente_id'])){$query->whereClienteId($clause['cliente_id']);}
//      if(isset($clause['fecha_desde']) and !empty($clause['fecha_desde'])){$query->desde($clause['fecha_desde']);}
//      if(isset($clause['fecha_hasta']) and !empty($clause['fecha_hasta'])){$query->hasta($clause['fecha_hasta']);}
//		if(isset($clause['estado_id']) and !empty($clause['estado_id'])){$query->whereEstadoId($clause['estado_id']);}
		if(isset($clause['uuid_cotizacion']) and !empty($clause['uuid_cotizacion'])){
			$query->whereUuidCotizacion(hex2bin($clause['uuid_cotizacion']));
		}
        if(isset($clause['ids']) and !empty($clause['ids'])){
			$query->whereIn('id',$clause["ids"]);
		}
    }

//    private function _getHiddenOptions($cotizacion_alquiler, $auth)
//    {
//        $hidden_options = "";
//
//        if($auth->has_permission('acceso', 'cotizaciones_alquiler/editar/(:any)'))
//        {
//            $hidden_options = '<a href="'.$cotizacion_alquiler->enlace.'" data-id="'. $cotizacion_alquiler->uuid_cotizacion_alquiler .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
//        }
//
//        return $hidden_options;
//    }

    public function getCollectionCampo($cotizacion_alquiler)
    {
        $cliente_tipo = $cotizacion_alquiler->cliente_tipo;
        //dd($cotizacion_alquiler->toArray());
        return [
            'id' => $cotizacion_alquiler->id,
            'empezar_desde_type' => $cliente_tipo,
            'empezar_desde_id' => $cotizacion_alquiler->cliente_id,
            'codigo' => $cotizacion_alquiler->codigo,
            'centros_facturacion' => ($cliente_tipo == 'cliente') ? $cotizacion_alquiler->cliente->centro_facturable : [],
            'centro_facturacion_id' => ($cliente_tipo == 'cliente') ? $cotizacion_alquiler->centro_facturacion_id : '',
            'saldo' => ($cliente_tipo == 'cliente') ? $cotizacion_alquiler->cliente->saldo_pendiente : '',
            'credito' => ($cliente_tipo == 'cliente') ? $cotizacion_alquiler->cliente->credito_favor : '',
            'fecha_emision' => $cotizacion_alquiler->fecha_desde->format('d/m/Y'),
            'valido_hasta' => $cotizacion_alquiler->fecha_hasta->format('d/m/Y'),
            'vendedor_id' => $cotizacion_alquiler->creado_por,
            'lista_precio_id' => '',
            'centro_contable_id' => $cotizacion_alquiler->centro_contable_id,
            'termino_pago_id' => $cotizacion_alquiler->termino_pago,
            'estado_id' => $cotizacion_alquiler->estado,
            'observaciones' => $cotizacion_alquiler->comentario
        ];
    }

    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $cotizaciones_alquiler = CotizacionesAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        if($sidx !== null && $sord !== null){$cotizaciones_alquiler->orderBy($sidx, $sord);}
        if($limit != null){$cotizaciones_alquiler->skip($start)->take($limit);}
        return $cotizaciones_alquiler->get();
    }

    public function findBy($clause)
    {
        $cotizacion_alquiler = CotizacionesAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $cotizacion_alquiler->first();
    }

//    public function getCollectionCell($cotizacion_alquiler, $auth)
//    {
//        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $cotizacion_alquiler->uuid_cotizacion_alquiler .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
//
//        return [
//            $cotizacion_alquiler->uuid_cotizacion_alqquiler,
//            $cotizacion_alquiler->numero_documento_enlace,
//            $cotizacion_alquiler->cliente->nombre_completo_enlace,
//            count($cotizacion_alquiler->centro_facturacion) ? $cotizacion_alquiler->centro_facturacion->nombre : '',
//            $cotizacion_alquiler->fecha_inicio->format('d/m/Y'),
//            $cotizacion_alquiler->saldo_facturar_label,
//            $cotizacion_alquiler->total_facturado_label,
//            $cotizacion_alquiler->estado->nombre_span,
//            $link_option,
//            $this->_getHiddenOptions($cotizacion_alquiler, $auth)
//        ];
//
//    }

	/*public function getCollectionExportar_bo($cotizaciones_alquiler)
	{
		$aux = [];

		foreach ($cotizaciones_alquiler as $cotizacion_alquiler)
		{
			$aux[] = [
				$cotizacion_alquiler->numero_documento,
				utf8_decode($cotizacion_alquiler->cliente->nombre),
				$cotizacion_alquiler->fecha_desde,
				$cotizacion_alquiler->fecha_hasta,
				$cotizacion_alquiler->centro_contable_id,
				$cotizacion_alquiler->creado_por,
				$cotizacion_alquiler->estado,
			];
		}

		return $aux;
	}*/
	
	public function getCollectionExportar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
	//filtros
		if(is_array($clause["ids"])){
			$polizas = CotizacionesAlquiler::whereIn('id',$clause["ids"]);
		}else{
			$polizas = CotizacionesAlquiler::where('id',$clause["ids"]);
		}
	
		
		return $polizas->get();
	}

    public function count($clause = array())
    {
        $cotizaciones_alquiler = CotizacionesAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $cotizaciones_alquiler->count();
    }

    private function _setItems($cotizacion_alquiler, $items)
    {
        $aux = [];

        foreach($items as $item)
        {
            $aux[$item['item_id']] = [
                'categoria_id' => $item['categoria_id'],
                'cantidad' => $item['cantidad'],
                'ciclo_id' => $item['ciclo_id'],
                'tarifa' => $item['tarifa']
            ];
        }

        $cotizacion_alquiler->items()->sync($aux);
    }

    private function _save($cotizacion_alquiler, $post)
    {
        $campo = $post['campo'];

        $cotizacion_alquiler->cliente_id = $campo['cliente_id'];
        $cotizacion_alquiler->cliente_tipo = $campo['empezar_desde_type'];//cliente || cliente_potencial
        $cotizacion_alquiler->tipo = 'alquiler';
        $cotizacion_alquiler->centro_contable_id = $campo['centro_contable_id'];
        $cotizacion_alquiler->estado = $campo['estado_id'];
        $cotizacion_alquiler->centro_facturacion_id = $campo['centro_facturacion_id'];
        $cotizacion_alquiler->lista_precio_alquiler_id = $campo['lista_precio_alquiler_id'];
        $cotizacion_alquiler->fecha_desde = $campo['fecha_emision'];
        $cotizacion_alquiler->fecha_hasta = $campo['valido_hasta'];
        $cotizacion_alquiler->creado_por = $campo['vendedor_id'];
        $cotizacion_alquiler->item_precio_id = $campo['lista_precio_id'];
        $cotizacion_alquiler->comentario = $campo['observaciones'];//falta en database
        $cotizacion_alquiler->termino_pago = $campo['termino_pago_id'];//falta en database

        $cotizacion_alquiler->save();
    }

    public function create($post)
    {
        $campo = $post['campo'];
        $cotizacion_alquiler = new CotizacionesAlquiler();

        $cotizacion_alquiler->codigo = $campo['codigo'];
        $cotizacion_alquiler->empresa_id = $campo['empresa_id'];

        $this->_save($cotizacion_alquiler, $post);
        $this->_setItems($cotizacion_alquiler, $post['articulos']);
        return $cotizacion_alquiler;
    }

    public function save($post)
    {
        $cotizacion_alquiler = CotizacionesAlquiler::find($post['campo']['id']);

        $this->_save($cotizacion_alquiler, $post);
        $this->_setItems($cotizacion_alquiler, $post['articulos']);

        return $cotizacion_alquiler;
    }

    function getCotizacionAbierta($clause) {
        return $cotizacion = CotizacionesAlquiler::where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    $query->where('tipo', '=', 'alquiler');
                    $query->whereIn('estado', array('aprobado'));
                })->get();
    }

    function getCotizacionGandas($clause) {
        return $cotizacion = CotizacionesAlquiler::where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    $query->where('tipo', '=', 'alquiler');
                    $query->whereIn('estado', array('ganado'));
                    if(isset($clause['cotizacion_id']) && !empty($clause['cotizacion_id'])){$query->orWhere('id', $clause['cotizacion_id']);}
                })->get();
    }
    
    

     public function getCollectionCotizacionEnContrato($cotizacion){

         /* $cotizaciones->load("cliente","items");*/

           return $cotizacion->map(function($cotizacion){

             //$articulo = new \Flexio\Library\Articulos\ArticuloVenta;
            return collect(array_merge(
                    [
                        'centros_facturacion' => count($cotizacion->cliente->centro_facturable) ? $cotizacion->cliente->centro_facturable : [],
                    ],
                    $cotizacion->toArray(),
                    [
                        'articulos' =>$cotizacion->items->map(function($item){
                            return array_merge(
                                $item->toArray(),
                                [
                                    "atributos" => $item->item->atributos,
                                    "items" => [],
                                    "item_hidden" => $item->item->id,
                                    "facturado" => true
                                ]
                            );
                        }),//$articulo->get($cotizacion->items, $cotizacion),
                        'observaciones' => $cotizacion->comentario,
                        'vendedor_id' => $cotizacion->creado_por,
                        'lista_precio_alquiler_id' => $cotizacion->lista_precio_alquiler_id,
                        'saldo' => $cotizacion->cliente->saldo_pendiente,
                        'credito' => $cotizacion->cliente->credito_favor,
                        'nombre' => "{$cotizacion->codigo} - {$cotizacion->cliente->nombre}",
                        'estado' => 'abierta',//Este estado pertenece al catalogo de ordenes de venta. No es un estado de la cotizacion
                        'fecha_inicio'=> date("d/m/Y"),
                        'cliente'=> $cotizacion->cliente,
                    ]
            ));

        });

    }

}
