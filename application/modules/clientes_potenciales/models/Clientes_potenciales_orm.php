<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \Illuminate\Database\Eloquent\Model as Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;
use Carbon\Carbon as Carbon;


/**
 * Description of clientes_potenciales_orm
 *
 * @author Ivan Cubilla
 */
class Clientes_potenciales_orm extends Model {
    //use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cp_clientes_potenciales';

    /**
     * Campos de la tabla de la base de datos.
     *
     * @var array
     */
    protected $fillable = ['uuid_cliente_potencial', 'nombre', 'telefono', 'correo', 'empresa_id', 'compania', 'id_cargo', 'id_toma_contacto', 'descripcion_toma_contacto', 'referido_por', 'comentario', 'fecha_creacion', 'creado_por', 'deleted_at'];

    /**
     * Campos que no cambian.
     *
     * @var array
     */
    protected $guarded = ['id_cliente_potencial', 'uuid_cliente_potencial'];

    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_cliente_potencial' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
    }

    public static function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }
    public static function scopeDeNombre($query, $nombre) {
        return $query->where("nombre",$nombre);
    }
    public function uuid() {
        return $this->uuid_cliente_potencial;
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_cliente_potencial', hex2bin($uuid))->first();
    }

    public function toma_contacto() {
        return $this->hasOne('Catalogo_toma_contacto_orm', 'id_cat', 'id_toma_contacto');
    }

    public function getUuidClientePotencialAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function getEnlaceAttribute()
    {
        return base_url('clientes_potenciales/editar/'.$this->uuid_cliente_potencial);
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
            'href'  => $this->enlace,
            'class' => 'link'
        ];

        $html = new Flexio\Modulo\Base\Services\Html(new Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType('HtmlA')->setAttrs($attrs)->setHtml($this->nombre)->getSalida();
    }




    /**
     * Función listar y buscar.
     * */
    public static function listar_clientes_potenciales($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {


        $clientes = self::with(array('toma_contacto','telefonos_asignados','correos_asignados'))->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                })->where(function ($query) {
            $query->where('deleted_at', '=', NULL)
                    ->Where('estado', '=', NULL);
        });

        if ($sidx != NULL && $sord != NULL)
            $clientes->orderBy($sidx, $sord);
        if ($limit != NULL)
            $clientes->skip($start)->take($limit);

        return $clientes->get();
    }

    public static function lista($clause = array()) {

        $ids_clientes = !empty($clause["id_cliente_potencial"]) ? $clause["id_cliente_potencial"] : array();

        $clientes = self::with(array('toma_contacto'))->where(function($query) use($clause) {
            $query->whereIn("id_cliente_potencial", $clause['id_cliente_potencial'])->where('deleted_at', '=', NULL);
        });
        return $clientes->get();
    }

    /**
     * Devuelve el conteo de paginación.
     * * */
    static function contar_clientes_potenciales($clause = array()) {
        // echo "lista_totales";
        return self::where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['compania']))
                        $query->where('compania', 'like', "%" . $clause['compania'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                })->where(function ($query) {
                    $query->where('deleted_at', '=', NULL)
                            ->Where('estado', '=', NULL);
                })->count();
    }

    /**
     * Guardar formulario de crear Cliente Potencial.
     *
     * @return boolean
     */
    function guardar_cliente_potencial($empresa_id = NULL) {
        if (Util::is_array_empty($_POST)) {
            return false;
        }

        //Init Fieldset variable
        $fieldset = array();

        //Recorrer arreglo e insertar los valores que no estan vacios
        //en el fieldset
        foreach ($_POST["campo"] AS $fieldname => $fieldvalue) {
            if (empty($fieldvalue)) {
                continue;
            }

            //check if is an array
            if (is_array($fieldvalue)) {
                foreach ($fieldvalue AS $name => $value) {
                    if ($value != "") {
                        $fieldset[$name] = $this->security->xss_clean($value);
                    }
                }
            } else {
                $fieldset[$fieldname] = $fieldvalue;
            }
        }

        //Verificar si esto se corrigio con la funcion, no tomar en cuenta los botones
        unset($fieldset['guardar']);

        //Si el $fieldset es vacio
        if (Util::is_array_empty($fieldset)) {
            return false;
        }

        //
        // Begin Transaction
        // docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
        //
        $this->db->trans_start();

        //Campos adicionales
        $this->db->set('uuid_cliente_potencial', 'ORDER_UUID(uuid())', FALSE);
        //$this->db->set("empresa_id", "UNHEX('$this->uuid_usuario')", FALSE);
        $fieldset["creado_por"] = $this->session->userdata('id_usuario');
        $fieldset["fecha_creacion"] = date('Y-m-d H-i-s');
        $fieldset['empresa_id'] = $empresa_id;
        //Guardar Cliente Potencial
        $this->db->insert('cp_clientes_potenciales', $fieldset);
        $idClientePotencial = $this->db->insert_id();

        //---------------------------------------
        //End Transaction
        $this->db->trans_complete();

        // Managing Errors
        if ($this->db->trans_status() === FALSE) {

            log_message("error", "MODULO: Clientes Potenciales --> No se pudo guadar los datos del cliente potencial en DB.");
            return false;
        } else {
            //Util::is_array_empty(array());
            /* Notifications::guardar_notificacion(
              array(
              "tipo_notificacion" => 'creacion',
              "modulo" => "clientes_potenciales",
              "id" => $idClientePotencial
              )); */

            log_message("error", "MODULO: Clientes Potenciales --> Se guadaron los datos del cliente potencial en la DB.");
            //guardar el id en variable de session
            $this->session->set_userdata('idClientePotencial', $idClientePotencial);

            return true;
        }
    }

    public static function select_cliente_potencial($cliente = array()) {

        /* $clientes_pot = self::with(array('toma_contacto'))->where(function($query) use($cliente) {
          $query->where('id_cliente_potencial', '=', $cliente['id']);
          }); */

        $clientes_pot = self::where(function($query) use($cliente) {
                    $query->where('id_cliente_potencial', '=', $cliente['id']);
                });
        return $clientes_pot->first();
    }

    /**
     * Actualiza campo que sirve de referencia para no mostrar elementos en la vista.
     *
     * @return void
     */
    public static function upDateClientePotencial($id = NULL) {
        self::where('id_cliente_potencial', $id)
                ->update(['deleted_at' => date('Y-m-d H-i-s')]);
    }

    public static function eliminar($id_cliente = NULL) {


        //Retorna false si $id_clientes es vacio
        if (empty($id_cliente)) {
            return false;
        }
        self::whereIn('id_cliente_potencial', $id_cliente)
                ->update(['estado' => 'Eliminado']);
        return array(
            "respuesta" => true,
            "mensaje" => "Se ha eliminado " . ( count($id_cliente) > 1 ? "los clientes seleccionados satisfactoriamente." : "el cliente seleccionado satisfactoriamente." )
        );
    }

}
