<?php
namespace Flexio\Modulo\Colaboradores\Repository;

use Flexio\Modulo\Colaboradores\Models\Colaboradores as Colaboradores;

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;

class ColaboradoresRepository implements ColaboradoresInterface{
    public function find($colaborador_id){
        return Colaboradores::find($colaborador_id);
    }

    function getAll($clause)
    {
        return Colaboradores::where(function ($query) use ($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            $query->where('estado_id', '=', 1);
            if (!empty($clause['centro_contable_id']))
                $query->whereIn('centro_contable_id', $clause['centro_contable_id']);
            if (!empty($clause['departamento_id']))
                $query->whereIn('departamento_id', $clause['departamento_id']);
            if (!empty($clause['ciclo_id']))
                $query->where('ciclo_id', $clause['ciclo_id']);

            if (!empty($clause['fecha_final_planilla']))
                $query->where('fecha_inicio_labores', '<=', $clause['fecha_final_planilla']);


        })->orderBy('nombre', 'asc')->get();
    }
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $colaboradores = Colaboradores::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($colaboradores, $clause);

        if($sidx!=NULL && $sord!=NULL){$colaboradores->orderBy($sidx, $sord);}
        if($limit!=NULL){$colaboradores->skip($start)->take($limit);}
        return $colaboradores->get();
    }

    public function count($clause = array())
    {
        $colaboradores = Colaboradores::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($colaboradores, $clause);

        return $colaboradores->count();
    }

    private function _filtros($acreedores, $clause)
    {
        if(isset($clause["acreedor_id"]) and !empty($clause["acreedor_id"])){$acreedores->deAcreedor($clause["acreedor_id"]);}
    }

    public function getResponseCell($colaborador, $link_option, $hidden_options){
        $descuentosDirectos = $this->_getDescuentosDirectos($colaborador);
        $symbol="$";
        return [
            $colaborador->uuid_colaborador,
            '<a style="color:blue;" class="link" href="'. base_url('colaboradores/ver/'. $colaborador->uuid_colaborador) .'" >'.$colaborador->nombre.' '.$colaborador->apellido.'</a>',
            $colaborador->codigo,
            $colaborador->cedula,
            $this->_getCentroContable($colaborador),
            $this->_getCargo($colaborador),
            $descuentosDirectos["ciclo"],
            $symbol.$descuentosDirectos["monto_total"],
            $symbol.$descuentosDirectos["monto_por_ciclo"],
            $symbol.$descuentosDirectos["pendiente"]
        ];
    }

    private function _getCentroContable($colaborador)
    {
        return isset($colaborador->centro_contable->nombre) ? $colaborador->centro_contable->nombre : "";
    }

    private function _getCargo($colaborador)
    {
        return isset($colaborador->cargo->nombre) ? $colaborador->cargo->nombre : "";
    }

    private function _getDescuentosDirectos($colaborador)
    {
        $ciclos = [];

        foreach($colaborador->descuentos_directos as $descuento_directo)
        {
            $ciclos[] = isset($descuento_directo->ciclo->etiqueta) ? $descuento_directo->ciclo->etiqueta : "";
        }

        $descuento = $colaborador->descuentos_directos->last();

        return [
            "ciclo"             => implode(", ", $ciclos),
            "monto_total"       => number_format($descuento->monto_inicial, 2, '.', ','),
            "monto_por_ciclo"   => number_format($descuento->monto_ciclo, 2, '.', ','),
            "pendiente"         => number_format($descuento->monto_adeudado, 2, '.', ',')
        ];
    }

    private function _getPendienteDescuentosDirectos($descuentos_directos)
    {
        $total = 0;

        foreach($descuentos_directos as $dd)
        {
            $fechaActivo    = !empty($dd->updated_at) ? Carbon::createFromFormat("Y-m-d H:i:s", $dd->updated_at) : Carbon::now();
            $fechaActual    = Carbon::now();
            $diffDias       = $fechaActivo->diffInDays($fechaActual);
            $ciclo_id       = $dd->ciclo_id;
            $dias           = 0;

            if($ciclo_id == "61"){$dias = 14;}//Bi-Semanal
            elseif($ciclo_id == "62"){$dias = 30;}//Mensual
            elseif($ciclo_id == "63"){$dias = 7;}//Semanal
            elseif($ciclo_id == "64"){$dias = 15;}//Quincenal

            if($diffDias != "0")
            {
                $total += $dd->monto_adeudado - (($diffDias/$dias)*$dd->monto_ciclo);
            }

        }

        return round($total, 2);
    }

    function findByUuid($uuid) {
        return Colaboradores::where('uuid_colaborador',hex2bin($uuid))->first();
    }
    function agregarComentario($id, $comentarios) {
        $colaboradores = Colaboradores::find($id);
        $comentario = new Comentario($comentarios);
        $colaboradores->comentario_timeline()->save($comentario);
        return $colaboradores;
    }
}
