<?php
namespace Flexio\Modulo\DevolucionesAlquiler\Repository;

use Carbon\Carbon;
use Flexio\Modulo\DevolucionesAlquiler\Models\DevolucionesAlquiler;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasDevoluciones;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquilerItems;

class DevolucionesAlquilerRepository
{

    private function _filtros($query, $clause) {
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        if(isset($clause['codigo']) and !empty($clause['codigo'])){$query->deCodigo($clause['codigo']);}
        if(isset($clause['fecha_desde']) and !empty($clause['fecha_desde'])){$query->whereDate('fecha_devolucion','>=',Carbon::createFromFormat('d/m/Y', $clause['fecha_desde']));}
        if(isset($clause['fecha_hasta']) and !empty($clause['fecha_hasta'])){$query->whereDate('fecha_devolucion','<=',Carbon::createFromFormat('d/m/Y', $clause['fecha_hasta']));}
        if(isset($clause['no_contrato']) and !empty($clause['no_contrato'])){$query->deNoContrato($clause['no_contrato']);}
        if(isset($clause['cliente_id']) and !empty($clause['cliente_id'])){$query->deCliente($clause['cliente_id']);}
        if(isset($clause['centro_facturacion_id']) and !empty($clause['centro_facturacion_id'])){$query->deCentroFacturacion($clause['centro_facturacion_id']);}
        if(isset($clause['estado_id']) and !empty($clause['estado_id'])){$query->whereEstadoId($clause['estado_id']);}
        if(isset($clause['ids']) and !empty($clause['ids'])){$query->whereIn('id',$clause['ids']);}
        if(isset($clause['uuid_devolucion_alquiler']) and !empty($clause['uuid_devolucion_alquiler'])){$query->whereUuidDevolucionAlquiler(hex2bin($clause['uuid_devolucion_alquiler']));}
        if(isset($clause['contrato_alquiler_id']) and !empty($clause['contrato_alquiler_id'])){$query->deContratoAlquiler($clause['contrato_alquiler_id']);}
        if(isset($clause['entrega_alquiler_id']) and !empty($clause['entrega_alquiler_id'])){$query->deEntregaAlquiler($clause['entrega_alquiler_id']);}
        if(isset($clause['item_id']) and !empty($clause['item_id'])){$query->deItem($clause['item_id']);}
    }

    private function _getHiddenOptions($devolucion_alquiler, $auth) {
        $hidden_options = "";

        if($auth->has_permission('acceso', 'devoluciones_alquiler/editar/(:any)'))
        {
            $hidden_options = '<a href="'. base_url('devoluciones_alquiler/editar/'. $devolucion_alquiler->uuid_devolucion_alquiler) .'" data-id="'. $devolucion_alquiler->uuid_devolucion_alquiler .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
        }

        return $hidden_options;
    }

    public function getCollectionCampo($devolucion_alquiler) {

         if( $devolucion_alquiler->tipo_contrato == 1){
             $empezar = 'Contrato de alquiler';
             $empezar_desde_id = $devolucion_alquiler->contratos[0]->id;

         }else{
             $empezar = 'entrega';
             $empezar_desde_id = $devolucion_alquiler->entregas[0]->id;
          }
           $cliente_info = Cliente::where("id","=",$devolucion_alquiler->cliente_id)->get();

          return [
            'id' => $devolucion_alquiler->id,
            'empezar_desde_type' => $empezar,
            'empezar_desde_id' => $empezar_desde_id,
            'codigo' => $devolucion_alquiler->codigo,
            'cliente_id' => $devolucion_alquiler->cliente_id,
            'vendedor_id' => $devolucion_alquiler->vendedor_id,
            'estado_id' => $devolucion_alquiler->estado_id,
            'recibido_id' => $devolucion_alquiler->recibido_id,
            'observaciones' => $devolucion_alquiler->observaciones,
            'saldo_pendiente_acumulado' => $cliente_info[0]['saldo_pendiente'],
            'credito_favor' => $cliente_info[0]['credito_favor'],
            'fecha_inicio_contrato' =>  $devolucion_alquiler->fecha_inicio_contrato->format('d/m/Y'),
            'fecha_fin_contrato' =>  $devolucion_alquiler->fecha_fin_contrato != "0000-00-00 00:00:00" && $devolucion_alquiler->fecha_fin_contrato != "" ? $devolucion_alquiler->fecha_fin_contrato->format('d/m/Y') : "",
            'fecha_devolucion' =>  $devolucion_alquiler->fecha_devolucion->format('d/m/Y H:m:s')

        ];
    }

    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null) {
        $devoluciones_alquiler = DevolucionesAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        if($sidx !== null && $sord !== null){$devoluciones_alquiler->orderBy($sidx, $sord);}
        if($limit != null){$devoluciones_alquiler->skip($start)->take($limit);}
        return $devoluciones_alquiler->get();
    }

    public function findBy($clause) {
        $devolucion_alquiler = DevolucionesAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $devolucion_alquiler->first();
    }

    public function getCollectionCell($devolucion_alquiler, $auth) {

        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $devolucion_alquiler->uuid_devolucion_alquiler .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        if($devolucion_alquiler->tipo_contrato == 0){
            $entregable = $devolucion_alquiler->entregas()->first()->entregable;
         }
        else{
            $entregable = $devolucion_alquiler->contratos->first();
         }

          return [
            $devolucion_alquiler->uuid_devolucion_alqquiler,
            $devolucion_alquiler->numero_documento_enlace,
            $devolucion_alquiler->fecha_devolucion->format('d/m/Y'),
            $entregable->numero_documento_enlace,
            $entregable->cliente->nombre_completo_enlace,
            count($entregable->centro_facturacion)?$entregable->centro_facturacion->nombre:'',
            $devolucion_alquiler->estado->nombre_span,
            $link_option,
            $this->_getHiddenOptions($devolucion_alquiler, $auth)
        ];

    }

    public function getCollectionExportar($devoluciones_alquiler) {
        $aux = [];

        foreach ($devoluciones_alquiler as $devolucion_alquiler)
        {
            $entregable = $devolucion_alquiler->entregas()->first()->entregable;
            $aux[] = [
                $devolucion_alquiler->numero_documento,
                $devolucion_alquiler->fecha_devolucion->format('d/m/Y'),
                $entregable->codigo,
                utf8_decode($entregable->cliente->nombre),
                utf8_decode($entregable->centro_facturacion->nombre),
                $devolucion_alquiler->estado->nombre,
            ];
        }

        return $aux;
    }

    public function count($clause = array()) {
        $devoluciones_alquiler = DevolucionesAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $devoluciones_alquiler->count();
    }

      private function _save($devolucion_alquiler, $post) {
        $campo = $post['campo'];
       /*  $entregables = [
            'devoluciones_Alquiler' => 'Flexio\\Modulo\\ContratosAlquiler\\Models\\Devoluciones_Alquiler'
        ];

        $entrega_alquiler->entregable_type = $entregables[$campo['empezar_desde_type']];*/

        $devolucion_alquiler->fecha_devolucion = $campo['fecha_devolucion'];
        $devolucion_alquiler->cliente_id = $campo['cliente_id'];
        $devolucion_alquiler->estado_id = $campo['estado_id'];
        $devolucion_alquiler->recibido_id = $campo['recibido_id'];
        $devolucion_alquiler->vendedor_id = $campo['vendedor_id'];
        $devolucion_alquiler->created_by = $campo['vendedor_id'];
        $devolucion_alquiler->observaciones = $campo['observaciones'];//falta en database
        $devolucion_alquiler->save();
    }

    public function create($post) {

        $campo = $post['campo'];

        if(empty($post['items'])){
          return false;
        }

        $entrega_items = new EntregasAlquilerItems();
        $this->_saveItems($entrega_items, $post['items'], $campo['empresa_id']);
        //$entrega_id = isset( $post['items'][0]['entrega_id'])?$post['items'][0]['entrega_id']:$post['empezar_desde_id'];
        $devolucion_alquiler = new DevolucionesAlquiler();

        $devolucion_alquiler->tipo_contrato = ($campo['empezar_desde_type'] == 'Contrato de alquiler')?1:0;
        $devolucion_alquiler->codigo = $campo['codigo'];
        $devolucion_alquiler->empresa_id = $campo['empresa_id'];
        $devolucion_alquiler->fecha_inicio_contrato = $campo['fecha_inicio_contrato'];

        if(!empty($campo['fecha_fin_contrato'])){
          $devolucion_alquiler->fecha_fin_contrato = $campo['fecha_fin_contrato'];
        }

        $this->_save($devolucion_alquiler, $post);
        $devolucion_alquiler->entregas()->sync([$campo['empezar_desde_id']]);

        if($campo['empezar_desde_type'] == "Contrato de alquiler")
            $this->_setItemsContratos($devolucion_alquiler, $post);
        else
            $this->_setItems($devolucion_alquiler, $post);
        return $devolucion_alquiler;
    }
    private function _saveItems($entrega_items, $post, $empresa_id) {
        $i = 0;

        foreach($post AS $row){
          $o = 0;
          foreach($row['detalles'] AS $info){
          $fieldset[$i][$o]['empresa_id'] = $empresa_id;
          $fieldset[$i][$o]['item_id'] = $row['item_id'];
          $fieldset[$i][$o]['serie'] = $info['serie'];
          $o++;
          }
          $i++;
        }

        foreach($fieldset AS $data){
          foreach($data AS $value){
            $clause = array(
              'item_id' => $value['item_id'],
              'serie' => $value['serie']
            );
            $modelos = $entrega_items->where($clause);
            $modelos->delete();
          }

        }
    }


    public function save($post) {

        $campo = $post['campo'];
        $retorno_alquiler = DevolucionesAlquiler::find($post['campo']['id']);

        $this->_save($retorno_alquiler, $post);
        //$this->_setItems($retorno_alquiler, $post);

        if($campo['empezar_desde_type'] == "Contrato de alquiler")
            $this->_setItemsContratos($retorno_alquiler, $post);
         else
             $this->_setItems($retorno_alquiler, $post);

        return $retorno_alquiler;

    }

    private function _setItemsContratos($retorno_alquiler, $post) {

        $items =  $post['items'];
        $objeto = ContratosAlquiler::where("id","=", $retorno_alquiler->contratos[0]->id)->get();
        $objeto->load('entregas','cliente', 'items', 'contratos_items')->first();
        $fecha = explode(" ", $post['campo']['fecha_devolucion']);

        foreach($objeto[0]->contratos_items  as $contrato_item)
        {
            foreach( $contrato_item->contratos_items_detalles_devoluciones as $contrato_item_detalle_devolucion)
            {
                 if($contrato_item_detalle_devolucion->operacion_type == 'Flexio\\Modulo\\DevolucionesAlquiler\\Models\\DevolucionesAlquiler' && $contrato_item_detalle_devolucion->operacion_id == $retorno_alquiler->id)
                {
                     $contrato_item_detalle_devolucion->delete();
                }
            }
        }


        //recorro los items del contrato en busca de coincidencias
        foreach ($objeto[0]->contratos_items  as $contrato_item)
        {
            foreach($items as $item)
            {
                if($contrato_item->categoria_id == $item['categoria_id'] && $contrato_item->item_id == $item['item_id'])
                {
                    $aux = [];
                    foreach($item['detalles'] as $detalle)
                    {
                        if(!empty($detalle['cantidad']))
                        {
                            //serie, cantidad, bodega, fecha
                            $aux[] = new \Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItemsDetalles([
                                'operacion_type' => get_class($retorno_alquiler),
                                'operacion_id' => $retorno_alquiler->id,
                                'cantidad' => $detalle['cantidad'],
                                'serie' => $detalle['serie'],
                                'bodega_id' => $detalle['bodega_id'],
                                'estado_item_devuelto' => $detalle['estado_item_devuelto'],
                                'fecha' =>$fecha[0]
                            ]);
                        }
                    }
                    $contrato_item->contratos_items_detalles()->saveMany($aux);
                }

            }
        }
    }

    private function _setItems($retorno_alquiler, $post) {

       $items =  $post['items'];

       $fecha = explode(" ", $post['campo']['fecha_devolucion']);
    //dd($retorno_alquiler->entregas->first()->entregable->contratos_items);

          foreach($retorno_alquiler->entregas->first()->entregable->contratos_items as $contrato_item)
        {



               foreach( $contrato_item->contratos_items_detalles_devoluciones as $contrato_item_detalle_devolucion)
            {

                /*echo "Antes de entrar";
                echo "oper_id:".$contrato_item_detalle_devolucion->operacion_id."</br>";
                echo "Ret ID:". $retorno_alquiler->id."</br></br></br>";*/
                if($contrato_item_detalle_devolucion->operacion_type == 'Flexio\\Modulo\\DevolucionesAlquiler\\Models\\DevolucionesAlquiler' && $contrato_item_detalle_devolucion->operacion_id == $retorno_alquiler->id)
                {
                     $contrato_item_detalle_devolucion->delete();
                }
             }
        }
         //recorro los items del contrato en busca de coincidencias
       foreach ($retorno_alquiler->entregas->first()->entregable->contratos_items  as $contrato_item)
       {
              foreach($items as $item)
            {
                 if($contrato_item->categoria_id == $item['categoria_id'] && $contrato_item->item_id == $item['item_id'])
                {
                     $aux = [];
                    foreach($item['detalles'] as $detalle)
                    {
                         if(!empty($detalle['cantidad']))
                        {
                             //serie, cantidad, bodega, fecha
                            $aux[] = new \Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItemsDetalles([
                                'operacion_type' => get_class($retorno_alquiler),
                                'operacion_id' => $retorno_alquiler->id,
                                'cantidad' => $detalle['cantidad'],
                                'serie' => $detalle['serie'],
                                'bodega_id' => $detalle['bodega_id'],
                                'estado_item_devuelto' => $detalle['estado_item_devuelto'],
                                'fecha' =>$fecha[0]
                               ]);
                        }
                     }
                     $contrato_item->contratos_items_detalles()->saveMany($aux);
                }

            }
        }
      }

    function lista_totales($clause = array()) {
        return DevolucionesAlquiler::where(function($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            $this->_filtros($query, $clause);
            if (isset($clause['cliente_id']))
                $query->where('cliente_id', '=', $clause['cliente_id']);
                if (isset($clause['etapa']))
                    $query->where('estado', '=', $clause['etapa']);
                    if (isset($clause['creado_por']))
                        $query->where('created_by', '=', $clause['creado_por']);
                        if (isset($clause['fecha_desde']))
                            $query->where('fecha_desde', '<=', $clause['fecha_desde']);
                            if (isset($clause['fecha_hasta']))
                                $query->where('fecha_hasta', '>=', $clause['fecha_hasta']);



        })->count();
    }

    function findByUuid($uuid) {
        return DevolucionesAlquiler::where('uuid_devolucion_alquiler',hex2bin($uuid))->first();
    }
    function agregarComentario($id, $comentarios) {
        $devolucion = DevolucionesAlquiler::find($id);
        $comentario = new Comentario($comentarios);
        $devolucion->comentario_timeline()->save($comentario);
        return $devolucion;
    }

}
