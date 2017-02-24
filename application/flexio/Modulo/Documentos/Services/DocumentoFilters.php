<?php

namespace Flexio\Modulo\Documentos\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;


class DocumentoFilters extends QueryFilters{

    function anticipo($anticipo){
        return $this->builder->where('documentable_id', $anticipo)->where('documentable_type','Flexio\Modulo\Anticipos\Models\Anticipo');
    }

    public function serie($serie)
    {
        return $this->builder->where('documentable_id', $serie)->where('documentable_type','Flexio\Modulo\Inventarios\Models\Seriales');
    }

    public function cliente($cliente)
    {
        return $this->builder->where('documentable_id', $cliente)->where('documentable_type','Flexio\Modulo\Cliente\Models\Cliente');
    }

    public function ordenes_trabajo($ordenes_trabajo)
    {
        return $this->builder->where('documentable_id', $ordenes_trabajo)->where('documentable_type','Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo');
    }

    public function subcontrato($subcontrato)
    {       
        return $this->builder->where('documentable_id', $subcontrato)->where('documentable_type','Flexio\Modulo\SubContratos\Models\SubContrato');
    }

    
    public function contrato_alquiler($contrato_alquiler_id) {
        return $this->builder->where('documentable_type', 'Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler')
                             ->where('documentable_id', $contrato_alquiler_id)
                             ->get();
    }


    public function relacionado_a($relalionado_a)
    {
        return $this->builder->where('extra_datos', 'like', "%$relalionado_a%");
    }

    public function fecha_desde($fecha)
    {
        if(empty($fecha))return $this->builder;
        return $this->builder->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d'));
    }

    public function fecha_hasta($fecha)
    {
        if(empty($fecha))return $this->builder;
        return $this->builder->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d'));
    }

    public function tipo($tipo_id)
    {
        if(is_array($tipo_id))
        {
            if(empty(array_filter($tipo_id))){return $this->builder;}
            return $this->builder->whereIn('tipo_id', $tipo_id);
        }
        return $this->builder->whereTipoId($tipo_id);
    }

    public function centro_contable($centro_contable)
    {
        if(is_array($centro_contable))
        {
            if(empty(array_filter($centro_contable))){return $this->builder;}
            return $this->builder->whereIn('centro_contable_id', $centro_contable);
        }
        return $this->builder->whereCentroContableId($centro_contable);
    }

    public function subido_por($subido_por)
    {
        if(is_array($subido_por))
        {
            if(empty(array_filter($subido_por))){return $this->builder;}
            return $this->builder->whereIn('subido_por', $subido_por);
        }
        return $this->builder->whereSubidoPor($subido_por);
    }

    public function etapa($etapa)
    {
        if(is_array($etapa))
        {
            if(empty(array_filter($etapa))){return $this->builder;}
            return $this->builder->whereIn('etapa', $etapa);
        }
        return $this->builder->whereEtapa($etapa);
    }


}
