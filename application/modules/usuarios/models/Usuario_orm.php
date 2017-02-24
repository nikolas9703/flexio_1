<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Builder as Builder;
use Illuminate\Database\Capsule\Manager as Capsule;
class Usuario_orm extends Model{

    protected $table = 'usuarios';
    protected $fillable = ['nombre','apellido', 'email','last_login','usuario','fecha_creacion','last_login_ip_address','ip_address','uuid_usuario','recovery_token','password','estado'];
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $hidden = array('password','recovery_token');

    /**
     * Instancia de CodeIgniter
     */
    protected $Ci;


    public function __construct() {
        $this->Ci = & get_instance();
    }


    public function toArray()
    {
        $array = parent::toArray();
        $array['nombre_completo'] = $this->attributes['nombre'] . ' ' . $this->attributes['apellido'];
        return $array;
    }
    public function setUuidUsuarioAttribute($value)
    {
        $this->attributes['uuid_usuario'] = Capsule::raw("ORDER_UUID(uuid())");
    }

    public function getUuidUsuarioAttribute($value){
        return strtoupper(bin2hex($value));
    }

    public function roles(){
        $this->Ci->load->model("roles/Rol_orm");
        return $this->belongsToMany('Rol_orm','usuarios_has_roles','usuario_id','role_id')->withpivot('empresa_id');
    }

    public function centros_contables()
    {
        return $this->belongsToMany('Flexio\Modulo\CentrosContables\Models\CentrosContables','usuarios_has_centros','usuario_id','centro_id')
            ->withPivot('empresa_id');
    }

    public function tipos_subcontrato() {
        return $this->belongsToMany('Flexio\Modulo\Catalogos\Models\Catalogo','usuarios_tipos_subcontratos','usuario_id','tipo_subcontrato_id')
                    ->withPivot('empresa_id');
    }

    public function categorias_inventario()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Categoria','usuarios_categorias','usuario_id','categoria_id')->withPivot('empresa_id');
    }

    public function conversion2bin($value){
        return hex2bin($value);
    }

    public function empresas(){
        return $this->belongsToMany('Empresa_orm','usuarios_has_empresas','usuario_id','empresa_id');
    }

    public function owenerEmpresa(){
        return $this->morphedByMany('Empresa_orm', 'relacion');
    }

    public function scopeCrearEmpresa($query){
        return $this->query->roles()->where('id',2);
    }

    public function scopeDeEmpresa($query, $empresa_id){
        return $query->whereHas("empresas", function($q) use ($empresa_id){
            $q->where("usuarios_has_empresas.empresa_id", $empresa_id);
        });
    }

    public function organizacion()
    {
        return $this->morphedByMany('Organizacion_orm', 'relacion');
    }

    public function comentario(){

        return $this->morphedByMany('Comentario_orm', 'relacion');
    }

    public function comentario_recibos(){

        return $this->morphedByMany('Comentario_recibos_orm', 'relacion');

    }

    function comentarios(){

        return $this->hasMany('Comentario_orm');
        //return $this->belongsTo('Usuario_orm','usuario_id');
    }

    public function entrada_manual(){
        return $this->morphedByMany('Entrada_orm', 'relacion');
    }

    public function nombreCompleto()
    {
        return $this->nombre." ".$this->apellido;
    }


    public static function findByUuid($uuid){
        return Usuario_orm::where('uuid_usuario',hex2bin($uuid))->first();
    }
    public static function findById($id){
        return Usuario_orm::where('id',($id))->first();
    }


    public static function listar($uuid_empresa,$sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $usuarios = Empresa_orm::findByUuid($uuid_empresa)->usuarios();

        $usuarios->with(array("roles" => function($query) use($empresa){
            $query->where("usuarios_has_roles.empresa_id", $empresa->id);
        }));

        $usuarios->with(array("tipos_subcontrato" => function($query) use($empresa){
            $query->where("usuarios_tipos_subcontratos.empresa_id", $empresa->id);
        }));

        $usuarios->with(array("centros_contables" => function($query) use($empresa){
            $query->where("usuarios_has_centros.empresa_id", $empresa->id);
        }));

        $usuarios->with(["categorias_inventario" => function($query) use($empresa){
            $query->where("usuarios_categorias.empresa_id", $empresa->id);
        }]);

        //Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL) $usuarios->orderBy($sidx, $sord);

        //Si existen variables de limite
        if($limit!=NULL) $usuarios->skip($start)->take($limit);

        return $usuarios->get();

    }

    static function rolVendedor($clause){
        return self::whereHas('roles',function($query) use($clause){
            $query->where('roles.nombre','like','%vendedor%');
            $query->where('roles.empresa_id','=',$clause['empresa_id']);
        })->get();
    }


}
