 <?php
/**
 * Roles Model
 *
 * Descripcion
 *
 * @package    PensaApp
 * @subpackage Model
 * @category   Models
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 *
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;
class Roles extends CRM_Controller {
    /**
     * @var
     */
    private $cache;
    /**
     * @var int
     */
    protected $empresa_id;
    protected $empresa;
    function __construct() {
        parent::__construct();
        $this->load->model('roles_model');
        $this->load->model('Rol_orm');
        $this->load->model('Permiso_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->helper('database');
        //HMVC Load Modules
        $this->load->module(array('modulos'));
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        //Inicializar variable cache
        $this->cache = Cache::inicializar();

        $this->load->model('Roles_menu_orm');
        $this->load->model('menu_orm'); 

    }
    public function listar() {
        if(!in_array(2,$this->session->userdata('roles' )))
        {
            redirect('/');
        }
        /*if($_POST){
            var_dump($_POST);
         $sesion = array();
         $uuid = $this->input->post('uuid');
         $sesion['uuid_empresa'] = $uuid;
         $this->session->set_userdata($sesion);
         $this->cache->delete("usuario-roles-". $this->session->userdata('huuid_usuario'));
     }*/

        $data = array();
        

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/awesome-bootstrap-checkbox.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/awesome-bootstrap-checkbox.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/modules/roles/listar.js'
        ));


        //$this->session->set_flashdata('mensaje', $mensaje);
        //var_dump($this->session->set_flashdata('mensaje'));

        if(!empty( $this->session->flashdata('mensaje')) ){
            $mensaje = $this->session->flashdata('mensaje');
        }else{
            $mensaje = '';
        }
        $data = array(
            "estados" => field_enums('roles', 'estado'),
            "message" => $mensaje
        );
        $this->getSessionEmpresa();
        $this->template->agregar_titulo_header('Listado de Roles');
        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-cogs"></i> Roles '. $this->empresa->nombre,
        ));
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }
    public function ajax_listar() {
        //Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $this->getSessionEmpresa();
        $clause = array(
            "empresa_id" => $this->empresa_id
        );

        $nombre = $this->input->post('nombre', true);
        $descripcion = $this->input->post('descripcion', true);
        $estado = $this->input->post('estado', true);
        $id_rol = $this->input->post('id_rol', true);
        if (!empty($nombre)) {
            $clause["nombre"] = array('LIKE', "%$nombre%");
        }
        if (!empty($descripcion)) {
            $clause["descripcion"] = array('LIKE', "%$descripcion%");
        }
        if (is_numeric($estado)) {
            $clause["estado"] = (int) $estado;
        }
        if (!empty($id_rol)) {
            $clause["id"] = $id_rol;
        }
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Rol_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = Rol_orm::listar($clause, $sidx, $sord, $limit, $start);
        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;
        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-rol="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="#" id="editar_rol" data-rol="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Editar Rol</a>';
                //mostrar opcion de permisos si no es super usuario
                if ($row["superuser"] == 0) {
                    $hidden_options .= '<a href="' . base_url("roles/editar-permisos/" . $row['id']) . '" class="btn btn-block btn-outline btn-success">Editar Permisos</a>';
                }
                $hidden_options .= '<a href="#" id="duplicar_rol" data-rol="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Duplicar</a>';
                $activar_desactivar_btn_id = $row["estado"] == "Activo" ? "desactivar_rol" : "activar_rol";
                $activar_desactivar_btn_text = $row["estado"] == "Activo" ? "Desactivar" : "Activar";
                $activar_desactivar_btn_status = $row["estado"] == "Activo" ? 0 : 1;
                $activar_desactivar_btn_msg = $row["estado"] == "Activo" ? "desactivado" : "activado";
                $hidden_options .= '<a href="#" id="' . $activar_desactivar_btn_id . '" data-rol="' . $row['id'] . '" data-status="' . $activar_desactivar_btn_status . '" data-msg="' . $activar_desactivar_btn_msg . '" class="btn btn-block btn-outline btn-success">' . $activar_desactivar_btn_text . '</a>';
                //$default = $row["default"] == 1 ? '&nbsp;<span class="label label-primary">Default</span>' : '';
                if ($row['id'] == 1 || $row['id'] == 2 || $row['id'] == 3) {
                    $link_option = "";
                    $hidden_options = "";
                }

                //var_dump($row);

                $hidden_options .= '<a class="btn btn-block btn-outline btn-success" data-rol="'. $row['id'] .'" id="menuSuperior" >Menu</a>';

                $menus_permisos = Roles_menu_orm::where(['id_rol' => $row['id']])->get();
                $arraymenu = array();
                $j = 0;
                if(count($menus_permisos)){
                    foreach ($menus_permisos as $key => $value) {
                       $arraymenu[$j] = $value['nombre_menu'];
                       $j++;
                    }
                }

                $opciones_menu  = '<form action="'.base_url().'roles/menu_superior/" id="menuSuperior" method="POST" autocomplete="off"><input type="hidden" name="erptkn" id="tkn_menu_'.$row['id'].'" value=""><input type="hidden" name="empresa_id" id="empresa_id" value="'.$row['empresa_id'].'"><input type="hidden" name="rol_id" id="rol_id" value="'.$row['id'].'">';
                $menus = Menu_orm::lista_menu_superior();
                foreach ($menus as $key => $menu) {
                    if(in_array($menu['grupo'], $arraymenu)){
                        $opciones_menu .= '<div class="col-xs-12 col-sm-4 col-md-4"><div class="checkbox checkbox-primary"><input type="checkbox" name="menu[]" id="ventas" class="menu2" checked value="'.$menu['grupo'].'" /><label for="'.$key.'">'.$menu['grupo'].'</label></div></div>';
                    }else{
                        $opciones_menu .= '<div class="col-xs-12 col-sm-4 col-md-4"><div class="checkbox checkbox-primary"><input type="checkbox" name="menu[]" id="ventas" class="menu2" value="'.$menu['grupo'].'" /><label for="'.$key.'">'.$menu['grupo'].'</label></div></div>';
                    }
                    
                }
                $opciones_menu .= '<div class="form-group col-xs-12 col-sm-12 col-md-12" style="text-align: right;"> <a class="btn btn-w-m btn-default cancelar_menu">Cancelar</a> <button type="submit" class="btn btn-w-m btn-primary submit">&nbsp;Guardar</button> </div></form>';

                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    $row["id"],
                    $row["nombre"],
                    $row["descripcion"],
                    $row["superuserValue"],
                    ucfirst($row["estado"]),
                    $row["superuser"],
                    $row["default"],
                    $link_option,
                    $hidden_options,
                    $opciones_menu
                );
                $i++;
            }
        }
        echo json_encode($response);
        exit;
    }


    public function menu_superior(){

        $menu = $this->input->post('menu');
        $id_empresa = $this->input->post('empresa_id');
        $id_rol = $this->input->post('rol_id');
        
        if($menu != null){

            $menus = Roles_menu_orm::where(['id_rol' => $id_rol])->get();
            foreach ($menus as $key => $value) {
               $value->delete();
            }
            
            foreach ($menu as $key => $value) {
                $arraymenu['id_empresa'] = $id_empresa;
                $arraymenu['id_rol'] = $id_rol;
                $arraymenu['nombre_menu'] = $value;
                $menus = Roles_menu_orm::create($arraymenu);
            }
        }else{

            $menus = Roles_menu_orm::where(['id_rol' => $id_rol])->get();
            foreach ($menus as $key => $value) {
               $value->delete();
            }
        }

        if($menus != null){
            $mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente';
        }else{
            $mensaje = '<b>¡&Error!</b>No Se guardado correctamente'; 
        }
        
        //$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Menu Superios ');
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('roles/listar'));  
    }
    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/roles/tabla.js'
        ));
        $this->load->view('tabla');
    }
    public function ajax_crear_rol() {
        $rol_id = $this->input->post('rol_id', true);
        $nombre = $this->input->post('nombre', true);
        $descripcion = $this->input->post('descripcion', true);
        $superusuario = $this->input->post('superusuario', true);
        $default = $this->input->post('defaultRol', true);
        /**
         * Inicializar Transaccion
         */
        $this->getSessionEmpresa();
        Capsule::beginTransaction();
        try {
            //Obtener el id_empresa de session
            $uuid_empresa = $this->session->userdata('uuid_empresa');
            $empresa = Empresa_orm::findByUuid($uuid_empresa)->toArray();
            $empresa_id = $empresa["id"];
            //Verificar si el id_rol existe
            //Si exite tenemos que actualizar
            //la informacion del rol.
            $rol = Rol_orm::find($rol_id);
            if (empty($rol)) {
                $fieldset = array(
                    "empresa_id" => $this->empresa_id,
                    "nombre" => $nombre,
                    "descripcion" => $descripcion,
                    "superuser" => ($superusuario == true || $superusuario == 1 ? 1 : 0),
                    "default" => ($default == true || $default == 1 ? 1 : 0)
                );
                $rol = Rol_orm::create($fieldset);
                $emp = Empresa_orm::find($empresa_id);
                $emp->roles()->attach($rol->id);
                //Desmarcar los otros roles como default
                //si el rol actual ya fue seleccionado.
                if ($default == true || $default == 1) {
                    $roles = Rol_orm::whereHas('empresas', function($q) use ($empresa_id) {
                                $q->where('empresas_has_roles.empresa_id', '=', $empresa_id);
                            })->where('id', '<>', $rol->id)->get(array('id'))->toArray();
                    $roles_id = (!empty($roles) ? array_map(function($roles) {
                                        return $roles["id"];
                                    }, $roles) : "");
                    Rol_orm::whereIn("id", $roles_id)->update(array('default' => '0'));
                }
            } else {
                //Desmarcar los otros roles como default
                //si el rol actual ya fue seleccionado.
                if ($default == true || $default == 1) {
                    $roles = Rol_orm::whereHas('empresas', function($q) use ($empresa_id) {
                                $q->where('empresas_has_roles.empresa_id', '=', $empresa_id);
                            })->where('id', '<>', $rol_id)->get(array('id'))->toArray();
                    $roles_id = (!empty($roles) ? array_map(function($roles) {
                                        return $roles["id"];
                                    }, $roles) : "");
                    Rol_orm::whereIn("id", $roles_id)->update(array('default' => '0'));
                }
                $rol->empresa_id = $this->empresa_id;
                $rol->nombre = $nombre;
                $rol->descripcion = $descripcion;
                $rol->superuser = ($superusuario == true || $superusuario == 1 ? 1 : 0);
                $rol->default = ($default == true || $default == 1 ? 1 : 0);
                $rol->save();
            }
        } catch (ValidationException $e) {
            // Rollback
            Capsule::rollback();
            echo json_encode(array(
                "id" => false,
                "mensaje" => "Hubo un error tratando de guardar el cargo."
            ));
            exit;
        }
        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();
        echo json_encode(array(
            "id" => $rol->id,
            "mensaje" => "Se ha guardado el rol satisfactoriamente."
        ));
        exit;
    }
    public function editar_permisos($id_rol = NULL) {
        if ($_POST && !empty($_POST['modulo'])) {
            $response = $this->roles_model->guardar_permisos();
            if ($response == true) {
                //Limpiar Cache
                $this->cache->clean();
                redirect(base_url() . 'roles/editar-permisos/' . $id_rol);
            }
        }
			
        //Build the data array
        $data = array(
            "rol_id" => $id_rol,
            "grupo_modulos" => $this->modulos->comp_listar_modulos_activos(),
            "rol_info" => $this->roles_model->seleccionar_rol($id_rol)
        );
        
        $this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/awesome-bootstrap-checkbox.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/modules/roles/editar_permisos.js'
        ));
        $nombre_rol = isset($data['rol_info']['nombre']) ? $data['rol_info']['nombre'] : '';
        $this->template->agregar_titulo_header('Listado de Roles');
        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-cogs"></i> Roles: ' . $nombre_rol,
        ));
        if ($this->session->userdata('permisos_creados')) {
            //Mostrar mensaje de que los permisos fue guardada con exito.
            $data['message'] = 'Los permisos se actualizaron satisfactoriamente.';
            //Borrar la variable de session
            $this->session->unset_userdata('permisos_creados');
        }
        //dd($data, $this->session->all_userdata());
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }
    public function ajax_duplicar_rol() {
        //Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $response = $this->roles_model->duplicar_rol();
        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        exit;
    }
    public function ajax_eliminar_permiso() {
        //Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $result = $this->roles_model->eliminar_permiso();
        $response = array("deleted" => $result);
        //Limpiar Cache
        $this->cache->clean();
        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        exit;
    }
    public function ajax_activar_desactivar_rol() {
        //Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $response = $this->roles_model->activar_desactivar_rol();
        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        exit;
    }
    /**
     * Functions to Share with other modules.
     */
    /**
     * Obtener los roles existentes
     *
     * @return array id, nombtre del rol
     */
    public function seleccionar_roles($clause = array()) {
        return $this->roles_model->seleccionar_roles($clause);
    }

    private function getSessionEmpresa(){
        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa = $empresa;
        $this->empresa_id = is_object($empresa) ? $empresa->id : "";
    }
}
?>
