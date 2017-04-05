<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Catalogos\Models\Catalogo;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;

class Movimiento_retiros_orm extends Model
{

    protected $table = 'mov_retiro_dinero';
    protected $fillable = ['uuid_retiro_dinero', 'codigo', 'narracion', 'created_at', 'updated_at', 'fecha_inicio', 'empresa_id', 'cliente_id', 'proveedor_id', 'aseguradora_id', 'estado', 'cuenta_id', 'tipo_pago_id', 'incluir_narracion', 'numero_cheque', 'nombre_banco_cheque', 'numero_tarjeta', 'numero_recibo', 'banco_proveedor', 'numero_cuenta_proveedor'];
    protected $guarded = ['id'];

    public function getTipoPagoAttribute() {
        $info = $this->metodo_pago()->first();
        return !empty($info) && !empty($info->toArray()) ? $info->etiqueta : "";
    }


public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
	$query = self::with(array('cliente', 'proveedor', 'aseguradora', 'items'))->where("empresa_id", $clause['id_empresa']);

    if($clause!=NULL && !empty($clause) && is_array($clause))
    {
       $query->where("estado", "=", "1");

           if(!empty($clause['cliente']))
            {

             if($clause['cliente']=="1"){

              $query->whereHas('proveedor',function($query) use($clause){
              $query->where('id','=',$clause['nombre']);
              })->get();


             }elseif($clause['cliente']=="2"){

              $query->whereHas('cliente',function($query) use($clause){
              $query->where('id','=',$clause['nombre']);
              })->get();


             }elseif($clause['cliente']=="3"){
                $query->whereHas('aseguradora',function($query) use($clause){
                $query->where('id','=',$clause['nombre']);
                })->get();
             }
            }

           if(!empty($clause['narracion']))
            {
            $valor_narracion = $clause['narracion'][1];
            $query->where("narracion", "LIKE", "%$valor_narracion%");
            }

           if(!empty($clause['monto_desde']))
           {
            $query->where("debito", ">=", $clause['monto_desde']);
           }
           if(!empty($clause['monto_hasta']))
           {
            $query->where("debito", "<=", $clause['monto_hasta']);
           }
           if(!empty($clause['fecha_desde']))
           {
            $fecha_desde = $clause['fecha_desde'];
            $query->where("fecha_inicio", ">=", $fecha_desde);
           }
           if(!empty($clause['fecha_hasta']))
           {
            $fecha_hasta = $clause['fecha_hasta'];
            $query->where("fecha_inicio", "<=", $fecha_hasta);
           }

    }

	if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
	if($limit!=NULL) $query->skip($start)->take($limit);

  return $query->get();
  }

public function cliente()
{
    return $this->hasOne('Cliente_orm','id','cliente_id');
}

public function proveedor()
{

    return $this->hasOne('Proveedores_orm', 'id', 'proveedor_id');

}

public function aseguradora(){
    return $this->hasOne(Aseguradoras::class, 'id', 'aseguradora_id');
}

public function metodo_pago()
{
    return $this->belongsTo(Catalogo::class, 'tipo_pago_id', 'id');
}

public static function findByUuid($uuid){
    return self::where('uuid_retiro_dinero',hex2bin($uuid))->first();
  }

public function items()
	{
		return $this->hasMany('Items_retiros_orm', 'id_retiro');
	}
public function transacciones()
	{
		return $this->hasMany('Items_retiros_orm', 'id_retiro');
	}
public function cuentas()
{
    return $this->hasMany('Cuentas_orm', 'id');
}
function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

}
