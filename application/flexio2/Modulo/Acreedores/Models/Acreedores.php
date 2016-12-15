<?php
namespace Flexio\Modulo\Acreedores\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
class Acreedores extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     * Un acreedor es un proveedor dentro de flexio por lo cual
     * utilizamos la misma tabla solo con una etiqueta para
     * diferenciarlos
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
    protected $fillable = [
        'uuid_proveedor',
        'nombre',
        'telefono',
        'email',
        'uuid_tipo',
        'ruc',
        'estado',
        'fecha_creacion',
        'creado_por',
        'id_empresa',
        'id_forma_pago',
        'id_banco',
        'id_tipo_cuenta',
        'numero_cuenta',
        'limite_credito',
        'credito'
    ];

    protected $appends      = ['icono','codigo','enlace'];
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id'];

    public static function findByUuid($uuid){
        return self::where('uuid_proveedores',hex2bin($uuid))->first();
    }

    //transformaciones para GET
    public function getUuidProveedorAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    //transformaciones para SET

    //scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("id_empresa", $empresa_id);
    }

    public function scopeSiAcreedor($query)
    {
        return $query->where("acreedor", "SI");
    }

    public function scopeDeNombre($query, $nombre)
    {
        return $query->where("nombre", "like", "%$nombre%");
    }

    public function scopeDeTipo($query, $tipo)
    {
        return $query->where("tipo_id", $tipo);
    }

    public function scopeDeTelefono($query, $telefono)
    {
        return $query->where("telefono", "like", "%$telefono%");
    }

    public function tipo()
    {
        return $this->belongsTo(Acreedores_cat::class, "tipo_id", "id_cat");
    }

    public function descuentos()
    {
        return $this->hasMany("Descuentos_orm", "acreedor_id");
    }

    public function monto_cobrar()
    {
        return $this->descuentos->sum("monto_adeudado");
    }
    public function cantidad_colaboradores()
    {
        return count($this->descuentos);
    }

    public function categorias()
    {
        //falta crear la clase acreedores categorias
        return $this->belongsToMany(AcreedoresCategorias::class, 'pro_proveedor_categoria', 'id_proveedor', 'id_categoria');
    }

    public function tipos_pagos()
    {
        return $this->belongsToMany(Catalogos::class, 'pro_proveedores_catalogos', 'proveedor_id', 'catalogo_id');
        //falta una sentencia where
    }
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function acreedores_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("acreedores/ver/".$this->uuid_proveedor);
    }
    public function getIconoAttribute(){
        return 'fa fa-users';
    }
    public function getCodigoAttribute(){
        return $this->nombre;
    }

}
