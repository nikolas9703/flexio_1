<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPorCobrar as CuentaPorCobrar;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPorPagar as CuentaPorPagar;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaAbonar;



class Empresa_orm extends Model{
  protected $table = 'empresas';
  protected $guarded = ['id'];
  protected $fillable = ['nombre','uuid_empresa', 'empresa_id','fecha_creacion','ruc','description','telefono','logo','id_organizacion','modules_hidden'];
  protected $nodos = array();
  protected $appends  = ['imagenlogo'];
  protected $casts = [
      'modules_hidden' => 'array',
  ];


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
    return $array;
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
   // which is equivalent to:
   // return $this->hasMany('Survey', 'parent')->with('childrenRecursive);
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

  public function setUuidEmpresaAttribute($value)
  {
    $this->attributes['uuid_empresa'] = Capsule::raw("ORDER_UUID(uuid())");
  }

  public function setFechaCreacionAttribute($value)
  {
    $this->attributes['fecha_creacion'] = date("Y-m-d H:i:s");
  }

  public function organizacion(){
    //return $this->hasMany('Organizacion_orm','organizacion_id');
    return $this->belongsTo('Organizacion_orm','organizacion_id');
  }
  public function usuarios(){
    return $this->belongsToMany('Usuario_orm','usuarios_has_empresas','empresa_id','usuario_id');
  }

  public function roles(){
      $this->Ci->load->model("roles/Rol_orm");
  	return $this->belongsToMany('Rol_orm','empresas_has_roles','empresa_id','rol_id');
  }

  public function cuentas(){
    return $this->hasMany('Cuentas_orm','empresa_id');
  }

  public function centros_contables(){
    return $this->hasMany('Centros_orm','empresa_id');
  }

  public function getUuidEmpresaAttribute($value)
  {
    return strtoupper(bin2hex($value));
  }

  public static function findByUuid($uuid){
    return Empresa_orm::where('uuid_empresa',hex2bin($uuid))->first();
  }
  public function getImagenlogoAttribute(){

      /* $attrs = [
       "class" => "fa fa-shopping-cart",
       ];
      $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
      return  $html->setType("htmlI")->setAttrs($attrs)->setHtml('')->getSalida();
      */

      return "sedes.png";
  }
  public static function guardar_usuario_empresa($campo){

    if(!empty($campo))
    {
    	/**
    	 * Inicializar Transaccion
    	 */
    	/*Capsule::beginTransaction();

    	try {*/

    	$empresa = new Empresa_orm;

    	$empresa->nombre = $campo['nombre'];
	      $empresa->uuid_empresa = 'default';
	      $empresa->fecha_creacion = 'default';
	      $empresa->empresa_id = $campo['empresa_id'];
	      $empresa->ruc = $campo['ruc'];
	      $empresa->descripcion = $campo['descripcion'];
	      $empresa->telefono = $campo['telefono'];
	      $empresa->logo = $campo['logo'];
	      $empresa->organizacion_id = $campo['organizacion_id'];
	      $empresa->save();

	      $emp = Usuario_orm::find($campo['usuario_id']);
	      $roles_empresa = $emp->roles()->where('usuarios_has_roles.empresa_id', 0)->first();

	      if($roles_empresa->exists)
	      {
	        	$roles_empresa->pivot->empresa_id = $empresa->id;
	        	$roles_empresa->pivot->save();
	      }else{
	        	$emp->roles()->attach($emp->roles->id,array('empresa_id'=>$empresa->id));
	      }

	      if($emp->empresas()->count() == 0){
	     		 $emp->empresas()->attach($empresa->id,array('default'=>1));
	      }else{
	       		$emp->empresas()->attach($empresa->id);
	      }

	      $emp->owenerEmpresa()->save($empresa);

	/* } catch(ValidationException $e){

    	// Rollback
    	Capsule::rollback();

    	//Guardar mensaje de error
    	log_message("error", "MODELO ORM: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");

    	 return false;
      }*/
	  if($emp){
      	return true;
	  }else{
	  	return false;
	  }
   }

  }

  public static function actualizar_empresa($campos){

    $empresa = Empresa_orm::find($campos['id']);
    $empresa->nombre = $campos['nombre'];
    $empresa->empresa_id = $campos['empresa_id'];
    $empresa->ruc = $campos['ruc'];
    $empresa->descripcion = $campos['descripcion'];
    $empresa->telefono = $campos['telefono'];
    if(isset($campos['logo']))$empresa->logo = $campos['logo'];
    if($empresa->save()){
      return true;
    }else{
      return false;
    }

  }

  private function recursiva($parent,$level){



  }

  public static function jqgrip($usuario,$page,$limit){

    $empresas = $usuario->empresas;

    $lista_empresas = array();

    $jqgrip = array();

    $count = count($empresas);

    $total_pages = ($count > 0 ? ceil($count/$limit) : 0);
    if ($page > $total_pages) $page = $total_pages;
    $start = $limit * $page - $limit;
    if($start < 0) $start = 0;
    $rows = array();

    foreach($empresas as  $data){

      $hidden_options = "";
      $hidden_options .= '<a href="'. base_url().'roles/listar/" class="btn btn-block btn-outline btn-success">Ver Roles</a>';
      $hidden_options .= '<a href="'. base_url()."usuarios/agregar_usuarios/".$data['uuid_empresa'].'"  class="btn btn-block btn-outline btn-success">Ver Usuarios</a>';
      $hidden_options .= '<a href="'. base_url()."usuarios/editar_empresa/".$data['uuid_empresa'].'"  class="btn btn-block btn-outline btn-success">Editar</a>';
      array_push($rows, array('id' => $data['id'],'cell' =>array(
         'id'=> $data['id'],
         'nombre' => $data['nombre'],
         'fecha_creacion' => $data['created_at'],
         'total_usuario' => self::find($data['id'])->usuarios->count(),
         'opciones' => '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $data['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>',
         'link' => $hidden_options,
        'level' =>(string)$data['empresa_id']==0?0:1,
        'parent' => ($data['empresa_id'] == 0)? null: (string)$data["empresa_id"],
         'isLeaf' => $data['empresa_id'] == 0? false: true,
        'expanded' => $data['empresa_id'] == 0? true: false,
         'loaded'=>true,
         'icon' => 'fa fa-building'
     )));
    }

    $jqgrip = array('total' => $total_pages, 'record' => $count, 'page' => $page, 'rows' => $rows);
    return $jqgrip;
  }


 public static function jqgrip_nombre_columnas(){

   $colnames = array();
   $colmodel = array();
   array_push($colnames,'Nombre', 'Fecha de Creación' , 'Cantidad de Usuarios', 'Opciones');
   array_push($colmodel, array('name' => 'nombre', 'index'=> 'nombre', 'sorttype'=> 'string'));
   array_push($colmodel, array('name' => 'created_at', 'index'=> 'created_at', 'sorttype'=> 'string'));
   array_push($colmodel, array('name' => 'created_at', 'index'=> 'created_at', 'sorttype'=> 'string'));
   return json_encode(array('colNames' => $colnames, 'colModel' => $colmodel));
 }

 public function cuenta_por_cobrar(){
   return $this->hasOne(CuentaPorCobrar::class,'empresa_id');
 }

    public function cuenta_por_pagar(){
        return $this->hasOne(CuentaPorPagar::class,'empresa_id');
    }

    public function cuentas_abonos()
    {
        //una para proveedores y otra para clientes
        return $this->hasMany(CuentaAbonar::class, 'empresa_id');
    }

    public function cuenta_abonar_proveedores()
    {
        return $this->cuentas_abonos
                ->where("tipo", "proveedor");
    }

    public function cuentas_abonar_clientes()
    {
        return $this->cuentas_abonos->where("tipo", "cliente")->first();
    }

 public function tieneCuentaCobro(){
   $tiene = false;
   if ($this->cuenta_por_cobrar()->count() > 0){
     $tiene = true;
   }
   return $tiene;
 }


}
