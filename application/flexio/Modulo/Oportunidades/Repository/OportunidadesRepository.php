<?php
namespace Flexio\Modulo\Oportunidades\Repository;

use Flexio\Modulo\Oportunidades\Models\Oportunidades;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Oportunidades\Models\OportunidadesRelaciones;

class OportunidadesRepository
{

    private function _filtros($query, $clause)
    {

        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        if(isset($clause['nombre']) and !empty($clause['nombre'])){$query->where('nombre','like','%'.$clause['nombre'].'%');}
        if(isset($clause['asignado_a_id']) and !empty($clause['asignado_a_id'])){$query->whereAsignadoAId($clause['asignado_a_id']);}
        if(isset($clause['fecha_desde']) and !empty($clause['fecha_desde'])){$query->desde($clause['fecha_desde']);}
        if(isset($clause['fecha_hasta']) and !empty($clause['fecha_hasta'])){$query->hasta($clause['fecha_hasta']);}
        if(isset($clause['monto_desde']) and !empty($clause['monto_desde'])){$query->desdeMonto($clause['monto_desde']);}
        if(isset($clause['monto_hasta']) and !empty($clause['monto_hasta'])){$query->hastaMonto($clause['monto_hasta']);}
        if(isset($clause['etapa_id']) and !empty($clause['etapa_id'])){$query->whereEtapaId($clause['etapa_id']);}
        if(isset($clause['cotizables']) and !empty($clause['cotizables'])){$query->where('etapa_id','<',3);}
        if(isset($clause['uuid_oportunidad']) and !empty($clause['uuid_oportunidad'])){$query->whereUuidOportunidad(hex2bin($clause['uuid_oportunidad']));}
        if(isset($clause['oportunidad_id']) and !empty($clause['oportunidad_id'])){$query->whereId($clause['oportunidad_id']);}
        if(isset($clause['cliente_id']) and !empty($clause['cliente_id'])){$query->where('cliente_id', '=', $clause['cliente_id']);}
        if(isset($clause['campo']) and !empty($clause['campo'])){$query->deFiltro($clause['campo']);}
    }

    private function _getHiddenOptions($oportunidad, $auth)
    {

        $hidden_options = "";

        if($auth->has_permission('acceso', 'oportunidades/editar/(:any)'))
        {
            $hidden_options .= '<a href="'.$oportunidad->enlace.'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
        }

        if($auth->has_permission('acceso', 'cotizaciones/crear/'))
        {
            $hidden_options .= '<a href="'.base_url('cotizaciones/crear/oportunidad'.$oportunidad->id).'" class="btn btn-block btn-outline btn-success">Agregar nueva cotizaci&oacute;n</a>';
        }

        if($auth->has_permission('acceso', 'cotizaciones/crear/'))
        {
            $hidden_options .= '<a href="#" data-uuid="'. $oportunidad->uuid_oportunidad .'" data-id="'. $oportunidad->id .'" data-cliente_id="'.$oportunidad->cliente_id.'" class="btn btn-block btn-outline btn-success agregar-cotizacion">Agregar cotizaci&oacute;n</a>';
        }

        /** Cambio de estado */
        $hidden_options .= '<a href="#" data-uuid="'. $oportunidad->uuid_oportunidad .'" data-id="'. $oportunidad->id .'" data-cliente_id="'.$oportunidad->cliente_id.'" class="btn btn-block btn-outline btn-success cambio-estado">Cambio de estado</a>';


        return $hidden_options;
    }

    public function agregarComentario($ordenId, $comentarios) {

        $oportunidad = Oportunidades::find($ordenId);
        $comentario = new Comentario($comentarios);
        $oportunidad->comentario_timeline()->save($comentario);
        return $oportunidad;

    }

    public function asociarCotizacion($oportunidad, $params){

        $relacion = $oportunidad->relaciones()
                ->where('relacionable_id',$params['relacionable_id'])
                ->where('relacionable_type',$params['relacionable_type'])
                ->first();

        if(count($relacion)){
            //$relacion->delete();
            return false;
        }

        return $oportunidad->relaciones()->save(new OportunidadesRelaciones([
            'relacionable_id' => $params['relacionable_id'],
            'relacionable_type' => $params['relacionable_type']
        ]));

    }

    public function getCollectionCampo($oportunidad)
    {
        return collect([
            'id' => $oportunidad->id,
            'empezar_desde_type' => $oportunidad->cliente_tipo,
            'empezar_desde_id' => $oportunidad->cliente_id,
            'nombre' => $oportunidad->nombre,
            'codigo' => $oportunidad->codigo,
            'monto' => $oportunidad->monto,
            'fecha_cierre' => $oportunidad->fecha_cierre->format('d/m/Y'),
            'asignado_a_id' => $oportunidad->asignado_a_id,
            'etapa_id' => $oportunidad->etapa_id,
            'comentario_timeline' => $oportunidad->comentario_timeline
        ]);
    }

    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $oportunidades = Oportunidades::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        if($sidx !== null && $sord !== null){$oportunidades->orderBy($sidx, $sord);}
        if($limit != null){$oportunidades->skip($start)->take($limit);}
        return $oportunidades->get();
    }

    public function findBy($clause)
    {
        $oportunidad = Oportunidades::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $oportunidad->first();
    }

    public function getCollectionCell($oportunidad, $auth)
    {
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $oportunidad->uuid_oportunidad .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        return [
            $oportunidad->uuid_oportunidad,
            $oportunidad->numero_documento_enlace,
            isset($oportunidad->cliente)?$oportunidad->cliente->nombre_completo_enlace .' - '.$oportunidad->nombre:'',
            $oportunidad->monto_label,
            $oportunidad->created_at->format('d/m/Y'),
            $oportunidad->usuario->nombre_completo,
            $oportunidad->estado->nombre_span,
            $link_option,
            $this->_getHiddenOptions($oportunidad, $auth)
        ];

    }

    public function getCollectionExportar($oportunidades)
    {
        $aux = [];

        foreach ($oportunidades as $oportunidad)
        {
            $aux[] = [
                $oportunidad->numero_documento,
                count($oportunidad->cliente) ? utf8_decode($oportunidad->cliente->nombre .' - '.$oportunidad->nombre) : '',
                $oportunidad->monto_currency,
                $oportunidad->created_at->format('d/m/Y'),
                utf8_decode($oportunidad->usuario->nombre_completo),
                $oportunidad->estado->nombre
            ];
        }

        return $aux;
    }

    public function count($clause = array())
    {
        $oportunidades = Oportunidades::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $oportunidades->count();
    }

    private function _save($oportunidad, $post)
    {
        $campo = $post['campo'];

        $oportunidad->cliente_tipo = $campo['empezar_desde_type'];
        $oportunidad->cliente_id = $campo['cliente_id'];//empezar_desde_id
        $oportunidad->etapa_id = $campo['etapa_id'];
        $oportunidad->fecha_cierre = $campo['fecha_cierre'];
        $oportunidad->asignado_a_id = $campo['asignado_a_id'];
        $oportunidad->nombre = $campo['nombre'];
        $oportunidad->monto = $campo['monto'];

        $oportunidad->save();
    }

    public function create($post)
    {
        $campo = $post['campo'];
        $oportunidad = new Oportunidades();

        $oportunidad->codigo = $campo['codigo'];
        $oportunidad->empresa_id = $campo['empresa_id'];

        $this->_save($oportunidad, $post);

        return $oportunidad;
    }

    public function save($post)
    {
        $oportunidad = Oportunidades::find($post['campo']['id']);

        $this->_save($oportunidad, $post);

        return $oportunidad;
    }

}
