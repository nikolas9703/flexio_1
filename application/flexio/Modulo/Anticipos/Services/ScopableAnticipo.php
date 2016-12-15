<?php
namespace Flexio\Modulo\Anticipos\Services;



trait ScopableAnticipo
{
  ///clase para agrupar todos los scope
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeFechaAnticipo($query, $fechaDesde){
        return $query->whereDate("fecha_abono", ">=", date("Y-m-d", strtotime($fechaDesde)));
    }

    public function scopeDeFechaHasta($query, $fechaHasta){
        return $query->whereDate("fecha_abono", "<=", date("Y-m-d", strtotime($fechaHasta)));
    }

    public function scopeDeProveedor($query, $proveedor){
        return $query->where("proveedor_id", $proveedor);
    }

    public function scopeDeEstado($query, $estado){
        return $query->where("estado", $estado);
    }

    public function scopeDeMontoMin($query, $montoMin){
        return $query->where("monto_abonado", ">=", $montoMin);
    }

    public function scopeDeMontoMax($query, $montoMax){
        return $query->where("monto_abonado", "<=", $montoMax);
    }

    public function scopeDeFormaAbono($query, $formaPago){
        return $query->whereHas("metodo_abono", function($q) use ($formaPago){
            $q->where("tipo_abono", $formaPago);
        });
    }

    public function scopeDeTipo($query, $tipo){
        if($tipo === "planilla")
        {
            return $query->where("formulario", $tipo);
        }
        else
        {
            return $query->where(function($q){
                $q->where("formulario", "factura")
                ->orWhere("formulario", "proveedor");
            });
        }
    }

    public function scopeDeBanco($query, $banco){
        return $query->whereHas("metodo_abono", function($q) use ($banco){
            $q->where(Capsule::raw('CONVERT(referencia USING utf8)'), "like", "%\"nombre_banco_ach\":\"$banco\"%");
        });
    }    
}
