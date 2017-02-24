<?php

namespace Flexio\Modulo\Usuarios\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Empresa\Models\Organizacion;
//traits
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Notifications\Notifiable;

class Usuarios extends Model {

    use RevisionableTrait;
    use Notifiable;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['nombre', 'apellido', 'email', 'last_login', 'usuario', 'fecha_creacion', 'last_login_ip_address', 'ip_address', 'uuid_usuario', 'recovery_token', 'password', 'estado', 'filtro_categoria'];
    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'apellido', 'email', 'last_login', 'usuario', 'fecha_creacion', 'last_login_ip_address', 'ip_address', 'uuid_usuario', 'recovery_token', 'password', 'estado', 'filtro_centro_contable', 'filtro_categoria'];
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $hidden = array('password', 'recovery_token');

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_usuario' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
    }

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }

    //GETS
    public function getUuidUsuarioAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function roles() {
        return $this->belongsToMany('Flexio\Modulo\Roles\Models\Roles', 'usuarios_has_roles', 'usuario_id', 'role_id')
                        ->withPivot('empresa_id');
    }

    public function centros_contables() {
        return $this->belongsToMany('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'usuarios_has_centros', 'usuario_id', 'centro_id')
                        ->withPivot('empresa_id');
    }

    public function categorias_inventario() {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Categoria', 'usuarios_categorias', 'usuario_id', 'categoria_id')->withPivot('empresa_id');
    }

    public function tipos_subcontrato() {
        return $this->belongsToMany('Flexio\Modulo\Catalogos\Models\Catalogo','usuarios_tipos_subcontratos','usuario_id','tipo_subcontrato_id')
                    ->withPivot('empresa_id');
    }

    public function roles_reales(){
        return $this->belongsToMany('Flexio\Modulo\Roles\Models\Roles','usuarios_has_roles','usuario_id','role_id')
        ->whereNotIn('role_id',[2,3])->withPivot('empresa_id');
    }

    public function getNombreCompletoAttribute() {

        return $this->nombre . ' ' . $this->apellido;
    }

    public function empresas() {
        return $this->belongsToMany('Flexio\Modulo\Empresa\Models\Empresa', 'usuarios_has_empresas', 'usuario_id', 'empresa_id');
    }

    /* relacion dueño de emprea */

    public function owenerEmpresa() {
        return $this->morphedByMany('Flexio\Modulo\Empresa\Models\Empresa', 'relacion', 'relacions', 'usuario_orm_id');
    }

    public function organizacion() {
        return $this->morphedByMany(Organizacion::class, 'relacion', 'relacions', 'usuario_orm_id');
    }

    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->whereHas('empresas', function($empresa) use ($empresa_id) {
                    $empresa->where('empresas.id', $empresa_id);
                });
    }

    public function scopeVendedor($query, $empresa_id) {

        return $query->whereHas('roles', function($rol) use($empresa_id) {
                    $rol->where('roles.nombre', 'like', '%vendedor%');
                    $rol->where('roles.empresa_id', $empresa_id);
                });
    }

    public function scopeComprador($query, $empresa_id) {

        return $query->whereHas('roles', function($rol) use($empresa_id) {
                    //$rol->where('roles.nombre','like','%compra%');descomentar cuando se pase a qa
                    $rol->where('roles.empresa_id', $empresa_id);
                });
    }

    public static function registrar() {
        return new static;
    }

    public function scopeActivo($query) {
        return $query->where("estado", "Activo");
    }

    public function getActiveUsersByRol($empresa_id, $rol) {

        return Usuarios::join('usuarios_has_roles', 'usuario_id', '=', 'usuarios.id')
                        ->where('usuarios_has_roles.empresa_id', '=', $empresa_id)
                        ->where('usuarios.estado', '=', 'Activo')
                        ->whereIn('role_id', $rol)
                        ->select('usuarios.id', 'nombre', 'apellido')
                        ->get();
    }

    public function getActiveUsersByRol2($empresa_id, $rol, $id_usuario) {
        return Usuarios::join('usuarios_has_roles', 'usuario_id', '=', 'usuarios.id')
                        ->where('usuarios_has_roles.empresa_id', '=', $empresa_id)
                        ->where('usuarios.estado', '=', 'Activo')
                        ->whereIn('usuarios.id', $id_usuario)
                        ->whereIn('role_id', $rol)
                        ->select('usuarios.id', 'usuarios.nombre', 'usuarios.apellido')
                        ->get();
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_usuario', hex2bin($uuid))->first();
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Usuarios\Service\UsuarioFilters;
        return $queryFilter->apply($query, $campo);
    }
}
