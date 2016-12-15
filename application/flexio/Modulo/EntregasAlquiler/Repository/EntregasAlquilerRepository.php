<?php
namespace Flexio\Modulo\EntregasAlquiler\Repository;

use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquilerItems;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItemsDetalles;
class EntregasAlquilerRepository
{

    private function _filtros($query, $clause)
    {
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        if(isset($clause['codigo']) and !empty($clause['codigo'])){$query->deCodigo($clause['codigo']);}
        if(isset($clause['cliente_id']) and !empty($clause['cliente_id'])){$query->whereClienteId($clause['cliente_id']);}
        if(isset($clause['fecha_desde']) and !empty($clause['fecha_desde'])){$query->desde($clause['fecha_desde']);}
        if(isset($clause['fecha_hasta']) and !empty($clause['fecha_hasta'])){$query->hasta($clause['fecha_hasta']);}
        if(isset($clause['estado_id']) and !empty($clause['estado_id'])){$query->whereEstadoId($clause['estado_id']);}
        if(isset($clause['uuid_entrega_alquiler']) and !empty($clause['uuid_entrega_alquiler'])){$query->whereUuidEntregaAlquiler(hex2bin($clause['uuid_entrega_alquiler']));}
        if(isset($clause['contrato_alquiler_id']) and !empty($clause['contrato_alquiler_id'])){$query->deContratoAlquiler($clause['contrato_alquiler_id']);}
        if(isset($clause['no_contrato']) and !empty($clause['no_contrato'])){$query->deNoContrato($clause['no_contrato']);}
        if(isset($clause['centro_facturacion_id']) and !empty($clause['centro_facturacion_id'])){$query->deCentroFacturacion($clause['centro_facturacion_id']);}
        if(isset($clause['item_id']) and !empty($clause['item_id'])){$query->deItem($clause['item_id']);}
     }

    private function _getHiddenOptions($entrega_alquiler, $auth)
    {
        $hidden_options = "";

        if($auth->has_permission('acceso', 'entregas_alquiler/editar/(:any)'))
        {
            $hidden_options = '<a href="'. base_url('entregas_alquiler/editar/'. $entrega_alquiler->uuid_entrega_alquiler) .'" data-id="'. $entrega_alquiler->uuid_entrega_alquiler .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
        }

        return $hidden_options;
    }

    public function getCollectionCampo($entrega_alquiler)
    {
        return [
            'id' => $entrega_alquiler->id,
            'empezar_desde_type' => 'contrato_alquiler',
            'empezar_desde_id' => $entrega_alquiler->entregable->id,
            'cliente_id' => $entrega_alquiler->cliente_id,
            'saldo_cliente' => $entrega_alquiler->cliente->saldo_pendiente,
            'credito_cliente' => $entrega_alquiler->cliente->credito,
            'fecha_inicio_contrato' => $entrega_alquiler->contrato_alquiler->fecha_inicio->format('d/m/Y'),
            'fecha_fin_contrato' => $entrega_alquiler->contrato_alquiler->fecha_fin != "0000-00-00 00:00:00" && $entrega_alquiler->contrato_alquiler->fecha_fin != "" ? $entrega_alquiler->contrato_alquiler->fecha_fin->format('d/m/Y') : "",
            'fecha_fin' => $entrega_alquiler->fecha_fin != "0000-00-00 00:00:00" && $entrega_alquiler->fecha_fin != "" ? $entrega_alquiler->fecha_fin->format('d/m/Y') : "",
            'codigo' => $entrega_alquiler->codigo,
            'fecha_entrega' => $entrega_alquiler->fecha_entrega->format('d/m/Y H:i'),
            'centro_facturacion_id' => '',
            'created_by' => $entrega_alquiler->created_by,//creador de la entrega
            'vendedor_id' => $entrega_alquiler->entregable->created_by,
            'estado_id' => $entrega_alquiler->estado_id,
            'observaciones' => $entrega_alquiler->observaciones
        ];
    }

    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $entregas_alquiler = EntregasAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        if($sidx !== null && $sord !== null){$entregas_alquiler->orderBy($sidx, $sord);}
        if($limit != null){$entregas_alquiler->skip($start)->take($limit);}
        return $entregas_alquiler->get();
    }

    public function getEntregados($clause=array()) {
    	return EntregasAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        })->estadoEntregado()->get();
    }

    function find($id) {
        return EntregasAlquiler::find($id);
    }
    public function findBy($clause)
    {
        $entrega_alquiler = EntregasAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $entrega_alquiler->first();
    }

    public function getCollectionCell($entrega_alquiler, $auth)
    {
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $entrega_alquiler->uuid_entrega_alquiler .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

        return [
            $entrega_alquiler->uuid_entrega_alqquiler,
            $entrega_alquiler->numero_documento_enlace,
            $entrega_alquiler->fecha_entrega->format('d/m/Y'),
            //'16/01/2016',
            $entrega_alquiler->entregable->numero_documento_enlace,
            $entrega_alquiler->cliente->nombre_completo_enlace,
            count($entrega_alquiler->entregable->centro_facturacion) ? $entrega_alquiler->entregable->centro_facturacion->nombre : '',
            $entrega_alquiler->estado->nombre_span,
            $link_option,
            $this->_getHiddenOptions($entrega_alquiler, $auth)
        ];

    }

    public function getCollectionExportar($entregas_alquiler)
    {
        $aux = [];

        foreach ($entregas_alquiler as $entrega_alquiler)
        {
            $aux[] = [
                $entrega_alquiler->numero_documento,
                $entrega_alquiler->fecha_entrega->format('d/m/Y'),
                $entrega_alquiler->entregable->numero_documento,
                utf8_decode($entrega_alquiler->cliente->nombre),
                count($entrega_alquiler->entregable->centro_facturacion) ? utf8_decode($entrega_alquiler->entregable->centro_facturacion->nombre) : '',
                $entrega_alquiler->estado->nombre,
            ];
        }

        return $aux;
    }

    public function count($clause = array())
    {
        $entregas_alquiler = EntregasAlquiler::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $entregas_alquiler->count();
    }

    private function _setItems($entrega_alquiler, $items)
    {

        foreach($entrega_alquiler->entregable->contratos_items as $contrato_item)
        {
            foreach($contrato_item->contratos_items_detalles_entregas as $contrato_item_detalle_entrega)
            {
                if($contrato_item_detalle_entrega->operacion_type == 'Flexio\\Modulo\\EntregasAlquiler\\Models\\EntregasAlquiler' && $contrato_item_detalle_entrega->operacion_id == $entrega_alquiler->id)
                {
                    $contrato_item_detalle_entrega->delete();
                }
            }
        }

        //recorro los items del contrato en busca de coincidencias
        foreach ($entrega_alquiler->entregable->contratos_items as $contrato_item)
        {

            foreach($items as $item)
            {

                if($contrato_item->categoria_id == $item['categoria_id'] && $contrato_item->item_id == $item['item_id'] && $contrato_item->ciclo_id == $item['ciclo_id'])
                {
                    $aux = [];
                    foreach($item['detalles'] as $detalle)
                    {

                        if(!empty($detalle['cantidad']))
                        {
                            //serie, cantidad, bodega, fecha
                            $aux[] = new \Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItemsDetalles([
                                'operacion_type' => get_class($entrega_alquiler),
                                'operacion_id' => $entrega_alquiler->id,
                                'cantidad' => $detalle['cantidad'],
                                'serie' => $detalle['serie'],
                                'bodega_id' => $detalle['bodega_id'],
                                'fecha' => $detalle['fecha'],
                                'atributo_id' => isset($detalle['atributo_id']) && !empty($detalle['atributo_id']) ? $detalle['atributo_id'] : 0,
                                'atributo_text' => isset($detalle['atributo_text']) && !empty($detalle['atributo_text']) ? $detalle['atributo_text'] : 0
                            ]);
                        }

                    }

                    $contrato_item->contratos_items_detalles()->saveMany($aux);

                }

            }

        }

    }

    private function _save($entrega_alquiler, $post)
    {
        $campo = $post['campo'];

        $entregables = [
            'contrato_alquiler' => 'Flexio\\Modulo\\ContratosAlquiler\\Models\\ContratosAlquiler'
        ];

        $entrega_alquiler->entregable_id = $campo['empezar_desde_id'];
        $entrega_alquiler->entregable_type = $entregables[$campo['empezar_desde_type']];
        $entrega_alquiler->fecha_entrega = $campo['fecha_entrega'];
        $entrega_alquiler->cliente_id = $campo['cliente_id'];
        $entrega_alquiler->estado_id = $campo['estado_id'];
        $entrega_alquiler->created_by = $campo['created_by'];//quien realiza la entrega
        $entrega_alquiler->centro_facturacion_id = 0;//por desarrollar
        $entrega_alquiler->observaciones = $campo['observaciones'];//falta en database

        $entrega_alquiler->save();
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
            $datos['empresa_id'] = $value['empresa_id'];
            $datos['item_id'] = $value['item_id'];
            $datos['serie'] = $value['serie'];

            $entrega_items->create($datos);
          }

        }

    }

    public function create($post) {
        $campo = $post['campo'];
        $entrega_alquiler = new EntregasAlquiler();
        $entrega_items = new EntregasAlquilerItems();
        $entrega_alquiler->codigo = $campo['codigo'];
        $entrega_alquiler->empresa_id = $campo['empresa_id'];

        $this->_save($entrega_alquiler, $post);
        $this->_setItems($entrega_alquiler, $post['articulos']);
        $this->_saveItems($entrega_items, $post['articulos'], $campo['empresa_id']);
        return $entrega_alquiler;
    }

    private function _eliminarItems($entrega_items, $post, $empresa_id) {
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

    public function save($post)
    {
        $entrega_alquiler = EntregasAlquiler::find($post['campo']['id']);
        $entrega_items = new EntregasAlquilerItems();
        $this->_save($entrega_alquiler, $post);
        $this->_setItems($entrega_alquiler, $post['articulos']);

        if($post['campo']['estado_id'] == '3'){
        $this->_eliminarItems($entrega_items, $post['articulos'], $post['campo']['empresa_id']);
        }

        return $entrega_alquiler;

    }
    function findByUuid($uuid) {
        return EntregasAlquiler::where('uuid_entrega_alquiler',hex2bin($uuid))->first();
    }
    function agregarComentario($id, $comentarios) {
        $entregas = EntregasAlquiler::find($id);
        $comentario = new Comentario($comentarios);
        $entregas->comentario_timeline()->save($comentario);
        return $entregas;
    }

}
