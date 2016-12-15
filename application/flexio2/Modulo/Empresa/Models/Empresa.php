<?php
namespace Flexio\Modulo\Empresa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
//models for ::class
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPorCobrar;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class Empresa extends Model
{

  protected $table      = 'empresas';
  protected $guarded = ['id', 'uuid_empresa'];
  protected $fillable   = ['nombre','empresa_id','ruc','descripcion','telefono','logo','organizacion_id','retiene_impuesto','fecha_creacion','tomo','asiento','folio','digito_verificador'];
  protected $nodos      = array();

  public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_empresa' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
  }

  public static function registrar(){
    return new static;
  }

  public function hijos(){
    return $this->hasMany('Empresa_orm', 'empresa_id');
  }

  public function contabilidad_impuesto(){
    return $this->hasMany('Impuestos_orm', 'empresa_id');
  }

  public function hijosRecursive()
{
   return $this->hijos()->with('hijosRecursive');
}
// parent
public function padres()
{
   return $this->belongsTo('Empresa_orm')->where('empresa_id', 0);
}

// all ascendants
public function padresRecursive()
{
   return $this->padres()->with('padresRecursive');
}

public function total_usuarios(){
  return $this->usuarios->count();
}

public function usuarios(){
  return $this->belongsToMany(Usuarios::class,'usuarios_has_empresas','empresa_id','usuario_id');
}

public function setFechaCreacionAttribute(){
    return $this->attributes['fecha_creacion'] = Carbon::now();
}

public function getFechaCreacionAttribute($date){
  if(empty($date))
    return '';

   return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
}

public function getCreatedAtAttribute($date){
  if(empty($date))
    return '';
   return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
}

public function getUuidEmpresaAttribute($value)
{
    return strtoupper(bin2hex($value));
}

  public static function findByUuid($uuid){
    return self::where('uuid_empresa',hex2bin($uuid))->first();
  }

    public function cuenta_por_cobrar()
    {
        return $this->hasOne(CuentaPorCobrar::class,'empresa_id');
    }

    public function cuentas_por_pagar()
    {
        //una para proveedores y otra para acreedores
        return $this->hasMany('Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPorPagar', 'empresa_id');
    }

    public function cuenta_por_pagar_proveedores()
    {
        return $this->cuentas_por_pagar()
                ->where("tipo", "proveedor");
    }

    public function cuenta_por_pagar_acreedores()
    {
        return $this->cuentas_por_pagar()
                ->where("tipo", "acreedor");
    }

    public function cuentas_abonos()
    {
        //una para proveedores y otra para clientes
        return $this->hasMany('Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaAbonar', 'empresa_id');
    }

    public function cuenta_abonar_proveedores()
    {
        return $this->cuentas_abonos()
                ->where("tipo", "proveedor");
    }

    public function cuentas_abonar_clientes()
    {
        return $this->cuentas_abonos()
                ->where("tipo", "cliente");
    }

    public function cuentas_inventario()
    {
        //una para proveedores y otra para clientes
        return $this->hasMany('Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaInventario', 'empresa_id');
    }

    public function cuenta_inventario_en_transito()
    {
        return $this->cuentas_inventario()
                ->where("tipo", "facturado_activo");
    }

    public function cuenta_inventario_en_bodega()
    {
        return $this->cuentas_inventario()
                ->where("tipo", "sin_factura_activo");
    }

    public function cuenta_inventario_por_pagar()
    {
        return $this->cuentas_inventario()
                ->where("tipo", "sin_factura_pasivo");
    }

    public function cuenta_caja_menuda()
    {
        //return $this->hasMany('Flexio\Modulo\ConfiguracionContabilidad\Models\CajaMenuda', 'empresa_id');
        return $this->hasOne('Flexio\Modulo\ConfiguracionContabilidad\Models\CajaMenuda','empresa_id');
    }
}
