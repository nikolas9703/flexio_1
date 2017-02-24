<?php
namespace Flexio\Modulo\ClientesAbonos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\ClientesAbonos\Models\ClienteAbonoMetodo;


class ClienteAbono extends Model
{

    protected $table = 'cab_clientes_abonos';

    protected $fillable = ['codigo','cliente_id','empresa_id','fecha_abono','estado','monto_abonado','cuenta_id','referencia'];

    protected $guarded = ['id','uuid_cobro'];

    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_abono' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }

    public function getFechaPagoAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }

    public function getUuidPagoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function metodo_abono()
    {
        return $this->hasMany(ClienteAbonoMetodo::class,'abono_id');
    }

    public function catalogo_estado()
    {
        return $this->belongsTo('Clientes_abonos_catalogos_orm','estado','etiqueta')->where('tipo','=','etapa3');
    }

    public function cliente()
    {
        return $this->belongsTo('Cliente_orm', 'cliente_id');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeFechaDesde($query, $fechaDesde){
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
                ->orWhere("formulario", "cliente");
            });
        }

    }

    public function scopeDeBanco($query, $banco){
        return $query->whereHas("metodo_abono", function($q) use ($banco){
            $q->where(Capsule::raw('CONVERT(referencia USING utf8)'), "like", "%\"nombre_banco_ach\":\"$banco\"%");
        });
    }


    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\ClientesAbonos\Services\ClienteAbonoFilters;
        return $queryFilter->apply($query, $campo);
    }


}
