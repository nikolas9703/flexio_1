<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\NotaDebito\Models\NotaDebito as NotaDebito;
use Flexio\Modulo\SubContratos\Models\SubContrato as SubContrato;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;


class Proveedores_orm extends Model
{

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'pro_proveedores';


    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Indica el formato de la fecha en el modelo
     * en caso de que aplique
     *
     * @var string
     */
    protected $dateFormat = 'U';


    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['nombre', 'telefono', 'email', 'uuid_categoria', 'ruc', 'id_estado', 'id_empresa'];


    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * Instancia de CodeIgniter
     */
    protected $Ci;


    public function __construct() {
        $this->Ci = & get_instance();

        //Instancio mis modelos
        $this->Ci->load->model("proveedores/Proveedores_tipos_orm");
        $this->Ci->load->model("proveedores/Proveedores_categorias_orm");
        $this->Ci->load->model("cobros/Cobro_catalogo_orm");
        $this->Ci->load->model("modulos/Catalogos_orm");
    }

    public function toArray(){
        $array = parent::toArray();
        $array['saldo_pendiente'] = number_format($this->total_saldo_pendiente(), 2, '.', ',');
        return $array;
    }


    /**
     * Obtiene uuid_pedido
     *
     * Se convierte la data binaria en una representacion
     * hexadecimal
     *
     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getUuidProveedorAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function ordenesAbiertas()
    {
        return  Ordenes_orm
            ::where("id_estado", "=", "2")//2.- Abierto
            ->where("uuid_proveedor", "=", hex2bin(strtolower($this->uuid_proveedor)))
            ->count();
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('id_empresa', $empresa_id);
    }

    public function scopeConFacturasParaPagos($query)
    {
        return  $query->whereHas('facturas', function($q){
            $q->where(function($q2){
                $q2->where("estado_id", "14")//por facturar
                ->orWhere("estado_id", "15");//facturado paracial
            });
        });
    }

    public function facturas()
    {
        return $this->hasMany("Facturas_compras_orm", "proveedor_id", "id");
    }

    public function facturasCrear(){
        return  $this->hasMany('Facturas_compras_orm','proveedor_id')
            ->where(function($q){
                $q->where('estado_id','14')//facturas por pagar
                ->orWhere("estado_id", '15');//facturas pagadas parcial
            });
    }

    public function pagos_proveedor(){
        return $this->hasMany('Pagos_orm','proveedor_id')
            ->where("estado", "aplicado");//solo cuentan los pagos aplicados
    }

    public function pagos_proveedor_facturas_no_pagadas(){
        return $this->hasMany('Pagos_orm','proveedor_id')
            ->where("estado", "aplicado")
            ->whereHas("facturas", function($q){
                $q->where(function($r){
                    $r->where('estado_id','14')//facturas por pagar
                    ->orWhere("estado_id", '15');//facturas pagadas parcial
                });
            });
    }

    public function notaDebito()
    {
        return $this->hasMany('Flexio\Modulo\NotaDebito\Models\NotaDebito', 'proveedor_id')
            ->where("estado", "aprobado");
    }

    public function total_saldo_pendiente(){
        $total_facturas = $this->facturasCrear()->sum('total');//
        $total_pagado   = $this->pagos_proveedor_facturas_no_pagadas()->sum('monto_pagado');
        return round(($total_facturas - $total_pagado), 2);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
    /**
     * Obtiene fecha de creacion formateada

     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getFechaCreacionAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }

    /**
     * Obtiene el registro del tipo asociado con el pedido.
     */
    public function tipo()
    {
        return $this->belongsTo('Proveedores_tipos_orm', 'uuid_tipo', 'uuid_tipo');
    }

    public function categorias()
    {
        return $this->belongsToMany('Proveedores_categorias_orm', 'pro_proveedor_categoria', 'id_proveedor', 'id_categoria');
    }

    public function formasDePago()
    {
        return $this->belongsToMany('Catalogos_orm', 'pro_proveedores_catalogos', 'proveedor_id', 'catalogo_id');
    }

    public function formasDePagoCobros()
    {
        return $this->belongsToMany('Cobro_catalogo_orm', 'pro_proveedores_catalogos', 'proveedor_id', 'catalogo_id');
    }

    public function proveedor_anterior()
    {
        return  Proveedores_orm
            ::where("id_empresa", "=", $this->id_empresa)
            ->where("id", "<", $this->id)
            ->orderBy("id", "desc")
            ->first();
    }

    public function proveedor_siguiente()
    {
        return  Proveedores_orm
            ::where("id_empresa", "=", $this->id_empresa)
            ->where("id", ">", $this->id)
            ->orderBy("id", "asc")
            ->first();
    }

    public static function lista($id_empresa=NULL)
    {
        return self::where('id_empresa', $id_empresa)->get()->toArray();
    }

    /**
     * ------------------------------------------------
     * Proveedores con Subcontratos
     * ------------------------------------------------
     */
    public function subcontrato(){
        return $this->hasMany(SubContrato::class, 'proveedor_id');
    }

    public function proveedoresConSubcontratos($clause = []){
        return self::whereHas('subcontrato', function($query) use ($clause){
            $query->where('id_empresa', '=' , $clause['empresa_id']); //empresa_id o id_empresa??????
        })->get();
    }

    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        // $ordenes_id = !empty($clause["id"]) ? $clause["id"] : array();
        $query = self::with(array('categorias' => function($query) use($sidx, $sord){
            if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
                $query->orderBy("nombre", $sord);
            }
        }));
        foreach($clause AS $field => $value)
        {

            //Verificar si el campo tiene el simbolo @ y removerselo.
            if(preg_match('/@/i', $field)){
                $field = str_replace("@", "", $field);
            }

            //verificar si valor es array
            if(is_array($value)){

                if(preg_match("/(fecha)/i", $field)){
                    $query->where($field, $value[0], $value[1]);
                }else{
                    $query->whereIn("uuid_proveedor", $value);
                }



            }else{
                $query->where($field, '=', $value);
            }
        }
        return $query->get();
    }

    function nota_debito(){
        return $this->hasMany(NotaDebito::class, 'proveedor_id');
    }

    function proveedoresConNotaDebito($clause = []){
        return self::whereHas('nota_debito',function($query) use($clause){
            $query->where('empresa_id','=',$clause['empresa_id']);
        })->get();
    }

    function agregarComentario($id, $comentarios) {
        $proveedor = Proveedores_orm::find($id);
        $comentario = new Comentario($comentarios);
        $proveedor->comentario_timeline()->save($comentario);
        return $proveedor;
    }
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function proveedores_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }


}
