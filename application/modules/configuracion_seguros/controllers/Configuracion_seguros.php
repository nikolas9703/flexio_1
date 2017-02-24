<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Flexio\Modulo\Ramos\Models\RamosRoles as RamosRoles;
use Flexio\Modulo\Ramos\Models\RamosUsuarios as RamosUsuarios;
use Flexio\Modulo\Ramos\Models\Ramos as Ramos;
use Flexio\Modulo\Solicitudes\Models\Solicitudes;
use Flexio\Modulo\Roles\Models\Roles as Roles;

class Configuracion_seguros extends CRM_Controller
{

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;


    function __construct() {

        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        $this->load->model('catalogos/aseguradoras_orm');
        $this->load->model('Comisiones_orm');
        $this->load->model('Contabilidad/tipo_cuentas_orm');
        $this->load->model('Contabilidad/Cuentas_orm');
        $this->load->model('catalogos/Ramos_orm');
        $this->load->model('contactos/Contacto_orm');
        $this->load->model('catalogos/Planes_orm');
        $this->load->model('catalogos/Coberturas_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('catalogos/Catalogo_tipo_poliza_orm');
        $this->load->model('catalogos/Catalogo_tipo_intereses_orm');
        $this->load->model('contabilidad/Impuestos_orm');

        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->id_empresa = $this->empresaObj->id;
    }

    public function ocultotabla_ramos() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_seguros/tabla_ramos.js'
        ));//'public/assets/js/modules/aseguradoras/tabla_ramos.js'

        $this->load->view('tabla_ramos');
    }
	
	public function ocultotabla_planes() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_seguros/tabla_planes.js'
        ));//'public/assets/js/modules/aseguradoras/tabla_ramos.js'

        $this->load->view('tabla_planes');
    }

    public function ajax_listar_ramos() {
        //Just Allow ajax request

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $tipo = $this->input->post('tipo');
        $nombre = (string)$this->input->post('nombre');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //fix count
        $count = Ramos_orm::where('empresa_id', $empresa->id)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $clause = array('empresa_id' => $empresa->id);
        if (!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
        //if(!empty($nombre)) $clause['nombre'] = array('like',"%$nombre%");

        $cuentas = Ramos_orm::listar($clause, $nombre, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->record = $count;
        $i = 0;

        if (!empty($cuentas)) {
            foreach ($cuentas as $row) {

                $tituloBoton = ($row['estado'] != 1) ? 'Habilitar' : 'Deshabilitar';
                $estado = ($row['estado'] == 1) ? 0 : 1;
                $hidden_options = '';
                $link_option="";
                if($this->auth->has_permission('acceso','editar Ramos')){
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    $hidden_options = '<a href="javascript:" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarRamoBtn">Editar Ramo</a>';

                    $hidden_options .= '<a href="javascript:" data-id="' . $row['id'] . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
                }
                $modalstate ='<a href="javascript:" data-id="' . $row['id'] . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
                $level = substr_count($row['nombre'], ".");
                $spanStyle = ($row['estado'] == 1) ? '' : 'color:red;';
                $labelClass = ($row['estado'] == 1) ? 'successful' : 'danger';
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'nombre' => "<span style='".$spanStyle."'>".$row['nombre']."</span>",
                    'descripcion' => "<span style='".$spanStyle."'>".$row['descripcion']."</span>",
                    'codigo' => $row['codigo_ramo'],
                    'tipo_interes' => $row['interes_asegurado']["nombre"],
                    'tipo_poliza' => $row['tipo_poliza']["nombre"],
                    'estado' => "<a class='test' data-id='".$row['id']."' ><label class='test label label-".$labelClass."'>".(($row['estado'] == 1) ? 'Habilitado' : 'Deshabilitado')."</label></a>",
                    'opciones' => $link_option,
                    'link' => $hidden_options,
                    "level" => isset($row["level"]) ? $row["level"] : "0", //level
                    'parent' => $row["padre_id"] == 0 ? "NULL" : (string)$row["padre_id"], //parent
                    'isLeaf' => (Ramos_orm::is_parent($row['id']) == true) ? false : true, //isLeaf
                    'expanded' => false, //expended
                    'loaded' => true, //loaded
                    'modalstate'=>$modalstate,
                    'estadoReal'=>$row['estado']

                    ));
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    function ajax_cambiar_estado_ramo() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $response = array();
        $estado = $this->input->post('estado');
        $id = $this->input->post('id');


        $total = Ramos_orm::cambiar_estado($id, $estado);

        if ($total > 0) {
            $response = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> La actualizaci&oacute;n de estado');
        } else {
            $response = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Su solicitud no fue Procesada');
        }
        echo json_encode($response);
        exit;
    }

    function ajax_buscar_ramo() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $id = $this->input->post('id');
        $cuenta = Ramos::with('roles','user')->find($id);
        $hasRequest = Solicitudes::where('ramo',$cuenta->nombre)->count() >0 ? true : false;
        $response = array();


        $response['id'] = $cuenta->id;
        $response['codigo'] = $cuenta->id;
        $response['nombre'] = $cuenta->nombre;
        $response['descripcion'] = $cuenta->descripcion;
        $response['padre_id'] = $cuenta->padre_id;
        $response['codigo_ramo'] = $cuenta->codigo_ramo;
        $response['interes_asegurado'] = $cuenta->id_tipo_int_asegurado;
        $response['tipo_poliza'] = $cuenta->id_tipo_poliza;
        $response['roles'] = $cuenta->roles;
        $response['usuarios'] = $cuenta->user;
        $response['agrupador'] = $cuenta->agrupador;
        $response['hasRequest'] = $hasRequest;
        echo json_encode($response);
        exit;

    }

    public function ajax_listar_ramos_tree() {
        /*if (!$this->input->is_ajax_request()) {
            return false;
        }
*/
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $tipo = $this->input->post('tipo');
        $clause = array('empresa_id' => $empresa->id);
        if (!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
        $cuentas = Ramos_orm::listar_cuentas($clause); 
        //Constructing a JSON
        $response = new stdClass();
        $response->plugins = ["contextmenu"];
        $response->core->check_callback[0] = true;
        
        $i = 0;
        if (!empty($cuentas)) {
            foreach ($cuentas as $row) {
                $spanStyle = ($row['estado'] == 1) ? '' : 'color:red;';
                $response->core->data[$i] = array(
                    'id' => (string)$row['id'],
                    'parent' => $row["padre_id"] == 0 ? "#" : (string)$row["padre_id"],
                    'text' => "<span id='labelramo' style='".$spanStyle."'>".$row["nombre"]."</span>",
                    'icon' => 'fa fa-folder',
                    'codigo' => $row["id"]
                    //'state' =>array('opened' => true)
                    );

                $i++;
            }

        }

        echo json_encode($response,JSON_PRETTY_PRINT);
        exit;

    } 

    function ajax_guardar_ramos() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        
        
        $response = new stdClass();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $codigo_ramo = $this->input->post('codigo_ramo');
        $tipo_interes_ramo = $this->input->post('tipo_interes_ramo');
        $tipo_poliza_ramo = $this->input->post('tipo_poliza_ramo');
        $form_solicitud = $this->input->post('form_solicitud');
        $padre_id = $this->input->post('codigo');
        $cuenta_id = $this->input->post('cuenta_id');
        $usuarios = $this->input->post('usuarios');
        $roles  =  $this->input->post('roles');
        if(count($roles)){
           if(in_array("todos", $roles)){   
               $roles = array();
               $test =Roles::where("empresa_id","=",$this->id_empresa)
               ->select("id") 
               ->get()->toArray();
               foreach ($test as $key => $value) {
             # code...
                 array_push($roles, $value['id']);   
             }
         }
     }
     $agrupador  =(isset($_POST['agrupador'])) ? 1 : 0;

     
     if (!isset($id)) {
        $clause = array(
            "codigo_ramo" => strtoupper($codigo_ramo),
            "empresa_id" => $empresa->id
            );
        $existe = Ramos_orm::findCodigo($clause);
        if($existe && $codigo_ramo != ''){
            $response->clase = "danger";
            $response->estado = 200;
            $response->mensaje = '<b>Error</b> Codigo ya existe.';
            echo json_encode($response);
            exit;
        }else{
            $datos = array();
            $datos['nombre'] = $nombre;
            $datos['descripcion'] = $descripcion;
            $datos['codigo_ramo'] = strtoupper($codigo_ramo);
            $datos['id_tipo_int_asegurado'] = $tipo_interes_ramo;
            $datos['id_tipo_poliza'] = $tipo_poliza_ramo;
            $datos['empresa_id'] = $empresa->id;
            $datos['padre_id'] = $padre_id;
            $datos['agrupador']=$agrupador;
            $impuesto_save = Ramos_orm::create($datos);
            if(count($roles)>0){

                foreach ($roles as $key => $value) {
                    $ramoRol=new RamosRoles();
                    $ramoRol->id_rol=$value;
                    $ramoRol->id_ramo=$impuesto_save->id;
                    
                    $ramoRol->save();
                    
                }
            }   
            if (count($usuarios)>0){
                foreach ($usuarios as $key => $value) {     

                    $usuariosramos= new RamosUsuarios();
                    $usuariosramos->id_usuario=$value;
                    $usuariosramos->id_ramo=$impuesto_save->id;
                    $usuariosramos->save();

                }
            }
            $response->clase = "success";
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente  ' . $impuesto_save->nombre;
            
        }

    } else {
        $impuesto_save = Ramos_orm::find($id);

        if($impuesto_save->codigo_ramo != strtoupper($codigo_ramo)){
            $clause = array(
                "codigo_ramo" => strtoupper($codigo_ramo),
                "empresa_id" => $empresa->id
                );
            $existe = Ramos_orm::findCodigo($clause);
            if($existe){
                $response->clase = "danger";
                $response->estado = 200;
                $response->mensaje = '<b>Error</b> Codigo ya existe.';
                echo json_encode($response);
                exit;
            }
        }
        $impuesto_save->nombre = $nombre;
        $impuesto_save->descripcion = $descripcion;
        $impuesto_save->codigo_ramo = strtoupper($codigo_ramo);
        $impuesto_save->id_tipo_int_asegurado = $tipo_interes_ramo;
        $impuesto_save->id_tipo_poliza = $tipo_poliza_ramo;
        $impuesto_save->agrupador=$agrupador;
        $impuesto_save->padre_id = $padre_id;
        $impuesto_save->save();
        $response->clase = "success";
        $response->estado = 200;
        


        if(count($roles)>0){

            RamosRoles::where('id_ramo', $id)->delete();
            foreach ($roles as $key => $value) {
                $ramoRol=new RamosRoles();
                $ramoRol->id_rol=$value;
                $ramoRol->id_ramo=$impuesto_save->id;
                $ramoRol->save();

            }
        }   
        if (count($usuarios)>0){
            RamosUsuarios::where('id_ramo', $id)->delete();
            foreach ($usuarios as $key => $value) {     

                $usuariosramos= new RamosUsuarios();
                $usuariosramos->id_usuario=$value;
                $usuariosramos->id_ramo=$impuesto_save->id;
                $usuariosramos->save();

            }
        }
        $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha actualizado correctamente  ' . $impuesto_save->nombre;
        $response->roles=$roles;
    }

    echo json_encode($response);
    exit;
}

public function ajax_verifica_padres_ramo(){
	$id_ramo = $this->input->post('id_ramo');
	$cont = 0;
	
	$Ramos1 = Ramos::select("padre_id")->where("id",$id_ramo)->get()->toArray();
	if($Ramos1[0]["padre_id"] > 0){
		$cont += 1;
		$Ramos2 = Ramos::select("padre_id")->where("id",$Ramos1[0]["padre_id"])->get()->toArray();
		if($Ramos2[0]["padre_id"] > 0){
			$cont += 1;
			$Ramos3 = Ramos::select("padre_id")->where("id",$Ramos2[0]["padre_id"])->get()->toArray();
			if($Ramos3[0]["padre_id"] > 0){
				$cont += 1;
				/*$Ramos4 = Ramos::select("padre_id")->where("id",$Ramos3[0]["padre_id"])->get()->toArray();
				if($Ramos4[0]["padre_id"] > 0){
					$cont += 1;
				}*/
			}
		}
	}
	
	if($cont==3){
		$inf["permitido"] = 0;
	}else{
		$inf["permitido"] = 1;
	}
	$inf["hfin"] = date("his");
	$inf["count"] = $cont;
	die(json_encode($inf));
}

public function test(){
    Capsule::beginTransaction();
    try {
        $cuenta = Ramos::with('roles','user')->find(249);
        foreach ($cuenta->user as $key => $value) {
                # code...
            print $value->id_usuario;   
        }
        

    } catch (Exception $e) {

        print $e ;
        Capsule::rollback();

    }

    Capsule::commit();

}

/*public function ajax_listar_ramos_subgrid() {

    $cuentas= Capsule::select('CALL get_child_ramos(?)',array($_POST['parent']));
    for ($i=0; $i <count($cuentas) ; $i++) { 
            # code...
        $spanStyle = ($cuentas[$i]->estado == 1) ? 'successful' : 'danger';
        $tituloBoton = ($cuentas[$i]->estado != 1) ? 'Habilitar' : 'Deshabilitar';
        $estado = "<a class='test' data-id='".$cuentas[$i]->id."' data-estado='".$cuentas[$i]->estado."'><label  style='cursor:pointer' class='label label-".$spanStyle."'>".(($cuentas[$i]->estado == 1) ? 'Habilitado' : 'Deshabilitado')."</label></a>";
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $cuentas[$i]->id. '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        if($this->auth->has_permission('acceso','Configuracion_seguros/ajax_cambiar_estado_ramo')){
            $hidden_options .= '<a href="javascript:" data-id="' . $cuentas[$i]->id . '" class="btn btn-block btn-outline btn-success editarRamoBtn">Editar Ramo</a>';
        }
        $hidden_options .= '<a href="javascript:" data-id="' . $cuentas[$i]->id . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
        $modalStateOption= '<a href="javascript:" data-id="' . $cuentas[$i]->id . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
        $cuentas[$i]->link=$hidden_options;
        $cuentas[$i]->modalstate=  $modalStateOption= '<a href="javascript:" data-id="' . $cuentas[$i]->id . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
        $cuentas[$i]->opciones=$link_option;
        $cuentas[$i]->estado=$estado;
        //print $cuentas;
    }
    print json_encode($cuentas);
}
public function ajax_listar_ramos_grid() {

    $cuentas= Capsule::table('seg_ramos')->where('padre_id', 0)
    ->where('empresa_id',$this->id_empresa)
    ->select('nombre', 'id','descripcion','padre_id','estado') 
    ->orderBy('nombre', 'asc')   
    ->get();
    for ($i=0; $i <count($cuentas) ; $i++) { 
            # code...
        $spanStyle = ($cuentas[$i]->estado == 1) ? 'successful' : 'danger';
        $tituloBoton = ($cuentas[$i]->estado != 1) ? 'Habilitar' : 'Deshabilitar';
        $estado = "<a class='test' data-id='".$cuentas[$i]->id."' data-estado='".$cuentas[$i]->estado."'><label  style='cursor:pointer' class='label label-".$spanStyle."'>".(($cuentas[$i]->estado == 1) ? 'Habilitado' : 'Deshabilitado')."</label></a>";
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $cuentas[$i]->id. '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        if($this->auth->has_permission('acceso','Configuracion_seguros/ajax_cambiar_estado_ramo')){
            $hidden_options .= '<a href="javascript:" data-id="' . $cuentas[$i]->id . '" class="btn btn-block btn-outline btn-success editarRamoBtn">Editar Ramo</a>';
        }
        $hidden_options .= '<a href="javascript:" data-id="' . $cuentas[$i]->id . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
        $modalStateOption= '<a href="javascript:" data-id="' . $cuentas[$i]->id . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
        $cuentas[$i]->link=$hidden_options;
        $cuentas[$i]->modalstate=  $modalStateOption= '<a href="javascript:" data-id="' . $cuentas[$i]->id . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
        $cuentas[$i]->opciones=$link_option;
        $cuentas[$i]->estado=$estado;
        //print $cuentas;
    }
    print json_encode($cuentas);
}
 

*/
}
