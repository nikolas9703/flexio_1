<?php
namespace Flexio\Modulo\Proveedores\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Modulo\Bancos\Models\Bancos;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\Catalogos\Models\CatalogoMod;
use Illuminate\Database\Capsule\Manager as Capsule;

class Proveedores extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = false;
    protected $keepRevisionOf = ['nombre', 'telefono', 'email', 'ruc', 'estado', 'id_forma_pago', 'id_banco', 'id_tipo_cuenta', 'numero_cuenta', 'limite_credito', 'credito', 'tipo_id', 'acreedor', 'direccion', 'termino_pago_id', 'referencia', 'identificacion', 'tomo_rollo', 'folio_imagen_doc', 'asiento_ficha', 'digito_verificador', 'retiene_impuesto', 'provincia', 'letra', 'pasaporte'];
    protected $dontKeepRevisionOf = ['created_at', 'update_at'];

    protected $table        = 'pro_proveedores';
    protected $fillable     = [
        'nombre', 'telefono', 'email', 'ruc', 'estado', 'fecha_creacion', 'creado_por', 'id_empresa',
        'id_forma_pago', 'id_banco', 'id_tipo_cuenta', 'numero_cuenta', 'limite_credito', 'credito', 'tipo_id', 'acreedor',
        'direccion', 'termino_pago_id', 'referencia', 'identificacion', 'tomo_rollo', 'folio_imagen_doc', 'asiento_ficha',
        'digito_verificador', 'retiene_impuesto', 'provincia', 'letra', 'pasaporte'
    ];
    protected $guarded = ['id', 'uuid_proveedor'];
    protected $appends = ['saldo_pendiente', 'icono', 'codigo', 'enlace'];
    protected $cast = [
        'limite_credito' => 'real',
        'credito' => 'real'
    ];
    public $timestamps      = false;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_proveedor' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    //GETS
    public function getUuidProveedorAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function setLimiteCreditoAttribute($value)
    {
        $this->attributes['limite_credito'] = str_replace(",", "", $value);
    }

    public function setCreditoAttribute($value)
    {
        $this->attributes['credito'] = str_replace(",", "", $value);
    }

    public function  getFormaDePagoAttribute(){
            if(is_null($this->metodo_pagos)){
                return '';
            }
            $forma_pago = [];
            $tipos = [1 => "efectivo", 2 => "credito_favor", 3 => "cheque", 4 => "tarjeta_credito", 5 => "ach"];
            $catalogo = $this->metodo_pagos->first();

            if ($catalogo && isset($tipos[$catalogo->catalogo_id])) {
                $forma_pago = $tipos[$catalogo->catalogo_id];
            }
            return $forma_pago;
    }

    public function termino_pago(){

        return $this->belongsTo('Flexio\Modulo\Proveedores\Models\ProveedoresCat', 'termino_pago_id', 'id_cat');

    }
    public function getNombreEnlaceAttribute()
    {
        return '<a href="'.base_url("proveedores/ver/".$this->uuid_proveedor).'" class="link">'.$this->nombre.'</a>';
    }
    public function getOrdenesAbiertasAttribute()
    {
        if(!count($this->ordenes))
        {
            return [];
        }
        return $this->ordenes()->where(function($q){
            $q->where("id_estado", "2")//orden de compra por facturar
                    ->orWhere("id_estado", "3");//orden de compra facturada parcial
        });
    }

    public function getUuidProveedorBinAttribute()
    {
        return hex2bin($this->uuid_proveedor);
    }

    //Relaciones
    public function facturas()
    {
        return $this->hasMany('Flexio\Modulo\FacturasCompras\Models\FacturaCompra', 'proveedor_id');
    }

    public function facturasPorPagar(){
        return $this->hasMany('Flexio\Modulo\FacturasCompras\Models\FacturaCompra', 'proveedor_id')
                ->where(function($q){
                    $q->where('estado_id','14')//facturas por pagar
                    ->orWhere("estado_id", '15');//facturas pagadas parcial
                });
    }

    public function categorias()
    {
        return $this->belongsToMany('Flexio\Modulo\Proveedores\Models\ProveedoresCategorias', 'pro_proveedor_categoria', 'id_proveedor', 'id_categoria');
    }

    public function tipo()
    {
        return $this->belongsTo('Flexio\Modulo\Proveedores\Models\ProveedoresCat', "tipo_id", "id_cat");
    }
    public function ordenes()
    {
        return $this->hasMany('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra', 'uuid_proveedor', 'uuid_proveedor_bin');
    }

    public function pagos()
    {
        return $this->hasMany('Flexio\Modulo\Pagos\Models\Pagos', 'proveedor_id');
    }
    public function formasDePago()
    {
        return $this->belongsToMany('Catalogos_orm', 'pro_proveedores_catalogos', 'proveedor_id', 'catalogo_id');
    }


    public function metodo_pagos(){
        return $this->hasMany('Flexio\Modulo\Proveedores\Models\ProveedoresCatalogos', 'proveedor_id');
    }
    /**
     * Solo importan los pagos asociados a facturas por pagar o pagadas de forma parcial
     *
     * @return model
     */
    public function pagosAplicados()
    {
        return $this->hasMany('Flexio\Modulo\Pagos\Models\Pagos', 'proveedor_id')
            ->where("estado", "aplicado")
            ->whereHas("facturas", function($q){
                $q->where(function($r){
                    $r->where('estado_id','14')//facturas por pagar
                    ->orWhere("estado_id", '15');//facturas pagadas parcial
                });
            });
    }

    public function banco(){
        return $this->hasOne(Bancos::class, 'id', 'id_banco');
    }
    public function notaDebito()
    {
        return $this->hasMany('Flexio\Modulo\NotaDebito\Models\NotaDebito', 'proveedor_id')
            ->where("estado", "aprobado");
    }

    public function getSaldoPendienteAttribute()
    {
        $total_facturas = $this->facturasPorPagar()->sum('total');//
        $total_pagado   = $this->pagosAplicados()->sum('monto_pagado');
        return round(($total_facturas - $total_pagado), 2);
    }

    public function getSaldoPendienteMonedaAttribute()
    {
        $numero = new \Flexio\Modulo\Base\Services\Numero("moneda", $this->saldo_pendiente);
        return $numero->getSalida();
    }

    //scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("id_empresa", $empresa_id);
    }

    public function scopeDeUuids($query, $uuid_proveedores)
    {
        $aux = [];
        foreach($uuid_proveedores as $uuid)
        {
            $aux[] = hex2bin($uuid);
        }
          return $query->whereIn("uuid_proveedor", $aux);
    }
    function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function proveedores_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("proveedores/ver/".$this->uuid_proveedor);
    }
    public function getIconoAttribute(){
        return 'fa fa-shopping-cart';
    }
    public function getCodigoAttribute(){
        return $this->nombre;
    }

    public function anticipos()
    {
        return $this->morphMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'anticipable');
    }

    //apply to provider credit, card 8 from flexio 2017 board
    public function anticipos_aprobados()
    {
        return $this->morphMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'anticipable')
        ->select('atc_anticipos.*')
        ->leftJoin('empezables', function($join){
            $join->on('empezables.anticipo_id', "=", 'atc_anticipos.id');
        })
        ->whereRaw('empezables.anticipo_id IS NULL');
    }

    public function tipo_de_cuenta_banco() {
        return CatalogoMod::where('identificador', '=', 'Tipo de Cuenta')
                        ->where('id_cat', '=', $this->id_tipo_cuenta)
                        ->get()->first();
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Proveedores\Service\ProveedorFilters;
        return $queryFilter->apply($query, $campo);
    }

}
