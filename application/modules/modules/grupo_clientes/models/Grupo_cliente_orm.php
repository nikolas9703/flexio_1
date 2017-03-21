<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;
use Carbon\Carbon as Carbon;

class Grupo_cliente_orm extends Model {

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'grp_grupo';

    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['id', 'uuid_grupo', 'empresa_id', 'nombre', 'descripcion', 'credito_a_favor', 'saldo_acumulado', 'deleted_at', 'id_catalog_agrupador', ''];

    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id', 'uuid_grupo'];

    //Constructor
    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_grupo' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }

    public static function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeNumero($query, $numero) {
        return $query->where("numero", "like", "%$numero%");
    }

    public function toArray() {
        $array = parent::toArray();
        $array['saldo_pendiente'] = number_format($this->total_saldo_pendiente(), 2, '.', ',');
        return $array;
    }

    public function total_saldo_pendiente() {
        //$total_facturas = $this->facturasNoAnuladas->sum('total');
        // $total_cobrado = $this->cobros_cliente->sum('monto_pagado');
        // return ($total_facturas - $total_cobrado);
    }

    public function agrupador_cliente() {
        return $this->belongsToMany('Cliente_orm', 'grp_grupo_clientes', 'uuid_cliente', 'uuid_cliente');
    }

    public function uuid() {

        return $this->uuid_grupo;
    }

    static function lista_totales($clause = array()) {
        //echo "lista_totales";
        return self::where(function($query) use($clause) {
                    if (isset($clause['ids']))
                        $query->whereIn('id', $clause['ids']);
                    if (isset($clause['empresa_id']))
                        $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                })->where(function ($query) {
                    $query->where('deleted_at', '=', NULL);
                })->count();
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_grupo', hex2bin($uuid))->first();
    }

    /**
     * Función listar y buscar.
     * */
    public static function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {

        $clientes = self::with(array('agrupador_cliente'))->where(function($query) use($clause) {

                    if (isset($clause['ids']))
                        $query->whereIn('id', $clause['ids']);
                    if (isset($clause['empresa_id']))
                        $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                })->where(function ($query) {
            $query->where('deleted_at', '=', NULL);
        });
        //   echo "lista";
        if ($sidx != NULL && $sord != NULL)
            $clientes->orderBy($sidx, $sord);
        if ($limit != NULL)
            $clientes->skip($start)->take($limit);
        return $clientes->get();
    }

    /**
     * Función listar y buscar.
     * */
    public static function listaNombreAgrupadores($clause = array()) {

        $clientes = self::where(function($query) use($clause) {

                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->where(function ($query) {
            $query->where('deleted_at', '=', NULL);
        });

        return $clientes->get();
    }

    /**
     * Función que elimina de la vista principal un grupo de cliente seleccionado. 
     * */
    public static function eliminar($id_cliente = NULL) {

        //Retorna false si $id_clientes es vacio
        if (empty($id_cliente)) {
            return false;
        }
        self::where('id', $id_cliente)
                ->update(['deleted_at' => date('Y-m-d H-i-s')]);
        return array(
            "respuesta" => true,
            "mensaje" => "Se ha eliminado " . ( count($id_cliente) > 1 ? "el grupo de  cliente satisfactoriamente." : "el grupo de cliente satisfactoriamente." )
        );
    }

    public static function selectGrupoData($id = NULL) {

        return self::select('nombre', 'descripcion', 'id_catalog_agrupador')
                        ->where('id', '=', $id)->get();
    }

    public static function guardar($id = NULL, $nombre = NULL, $descripcion = NULL, $idcat = 0) {
        self::where('id', '=', $id)
                ->update(['nombre' => $nombre]);
        self::where('id', '=', $id)
                ->update(['descripcion' => $descripcion]);
        self::where('id', '=', $id)
                ->update(['id_catalog_agrupador' => $idcat]);
        //->update(['descripcion' => $descripcion]);
        // ->update(['id_catalog_agrupador' => $idcat]);
    }

    public static function updateCreditSaldo($id = NULL, $credito = NULL, $saldo = NULL) {
        self::where('id', '=', $id)
                ->update(['credito_a_favor' => $credito]);
        self::where('id', '=', $id)
                ->update(['saldo_acumulado' => $saldo]);
    }

}

?>
