<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\FormRequest\CrearEmpresaRequest;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;

//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Toast;

class Usuarios extends CRM_Controller {

    private $roles_sistema = array(2, 3);
    protected $empresa_request;
    protected $userRepo;
    protected $CentrosContablesRepository;
    protected $cache;

    //utils
    protected $FlexioAssets;
    protected $Toast;

    function __construct() {
        parent::__construct();

        $config = Array(
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE
        );

        $this->load->helper(array('file', 'string', 'util'));
        $this->load->model('usuarios_model');
        $this->load->model('roles_usuarios_orm');
        $this->load->model('relacion_orm');
        $this->load->model('organizacion_orm');
        $this->load->model('usuario_orm');
        $this->load->model('empresa_orm');
        $this->load->model('entrada_manual/comentario_orm');
        $this->load->model('roles/rol_orm');

        $this->load->library(array('encrypt'));
        $this->load->library('email', $config);

        //Encriptar el texto
        $this->encrypt->set_cipher(MCRYPT_RIJNDAEL_256);

        $this->load->module(array('roles'));
        $this->userRepo = new UsuariosRepository;
        $this->CentrosContablesRepository = new CentrosContablesRepository;
        $this->cache = Cache::inicializar();

        //utils
        $this->FlexioAssets = new FlexioAssets;
        $this->Toast = new Toast;
    }

    /* LISTAR EMPRESA */

    public function listar_empresa() {

        if(!in_array(2,$this->session->userdata('roles')))
        {
            redirect('/');
        }

        $this->template->agregar_titulo_header('Listado de Empresas');
        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-building"></i> Empresas ',
            "filtro" => false,
            "menu" => array(
                "nombre" => "Crear",
                "url" => "usuarios/crear-empresa",
                "opciones" => array()
        )));
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        ));

        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/modules/usuarios/routes.js',
            'public/assets/js/modules/usuarios/empresa.js'
        ));

        $data = array();
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_listar_empresas() {
        if ($this->input->is_ajax_request()) {

            //variable de jqgrip
            $page = (int)$this->input->post('page', true);
            $limit = (int)$this->input->post('rows', true);
            $sidx = $this->input->post('sidx', true);
            $sord = $this->input->post('sord', true);
            //$uuid_organizacion = $this->input->post('organizacion', true);
            //$uuid_organizacion = $this->session->userdata('uuid_organizacion');
            //se debe consegir el id del usuario por session
            $jqgrid = new Flexio\Modulo\Empresa\Services\JqGrid;
            $uuid_usuario = $this->session->userdata('huuid_usuario');

            $usuario = $this->userRepo->findByUuid($uuid_usuario);
            $uuid_empresa = $this->session->userdata('uuid_empresa');

            list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
            $count = $usuario->empresas->count();
            list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
            $empresas = $jqgrid->listar($usuario, $sidx, $sord, $limit, $start);

            $response = new stdClass();
            $response->page = $page;
            $response->total = $total_pages;
            $response->records = $count;

            //($usuario->empresas->toArray());
            //$organizacion = Organizacion_orm::findByUuid($uuid_organizacion);
            $i=0;
            foreach($empresas as  $row){

              $hidden_options = "";
              $hidden_options .= '<a id="empresaVerRoles" data-empresa="'. $row->uuid_empresa .'" href="javascript:"  class="btn btn-block btn-outline btn-success">Ver Roles</a>';
              $hidden_options .= '<a id="empresaUsuarios" data-empresa="'. $row->uuid_empresa .'"  href="javascript:" class="btn btn-block btn-outline btn-success">Ver Usuarios</a>';
              $hidden_options .= '<a href="'. base_url("notificaciones/crear/". $row->uuid_empresa) .'" class="btn btn-block btn-outline btn-success">Administraci&oacute;n de notificaciones</a>';
              $hidden_options .= '<a href="'. base_url()."usuarios/editar_empresa/".$row->uuid_empresa.'"  class="btn btn-block btn-outline btn-success">Editar</a>';

              //Que solo me muestre las politicas donde estoy logueado
              if($row->uuid_empresa == $uuid_empresa){
                    $hidden_options .= '<a href="'. base_url()."politicas/listar/".$row->uuid_empresa.'"  class="btn btn-block btn-outline btn-success">Ver políticas de transacciones</a>';
              }

              $response->rows[$i]["id"] = $row->uuid_empresa;
              $response->rows[$i]["cell"] = array(
                 $row->id,
                 $row->nombre,
                 $row->created_at,
                 $row->total_usuarios(),
                '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_empresa .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>',
                 $hidden_options
             );
             $i++;
            }

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
            exit;
        }
    }

    /* CREAR EMPRESA */

    public function crear_empresa() {

        if(!in_array(2,$this->session->userdata('roles')))
        {
            redirect('/');
        }

        $this->template->agregar_titulo_header('Empresa: Crear');

        $this->form_validation->set_rules('campo[nombre]', 'Nombre de Compañia', 'trim|required');
        $this->form_validation->set_rules('campo[descripcion]', 'Dirección', 'trim|required');
        $this->form_validation->set_rules('campo[telefono]', 'Teléfono', 'trim|required');
        $this->form_validation->set_rules('campo[retiene_impuesto]', 'Retiene Impuestos', 'required');
        $this->form_validation->set_rules('campo[tomo]', 'Tomo', 'required');
        $this->form_validation->set_rules('campo[asiento]', 'Asiento', 'required');
        $this->form_validation->set_rules('campo[folio]', 'Folio', 'required');
        $this->form_validation->set_rules('campo[digito_verificador]', 'Digito Verificador', 'required');
        $this->form_validation->set_message('required', 'Campo es requerido.');

        //$uuid_organizacion = $this->session->userdata('uuid_organizacion');
        //$organizacion = Organizacion_orm::findByUuid($uuid_organizacion);

        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-building"></i>Empresa: Crear',
        ));

        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/empresa.css',
        ));
        $uuid_usuario = $this->session->userdata('huuid_usuario');

        $usuario = $this->userRepo->findByUuid($uuid_usuario);

        //Seleccionar empresas padre
        $empresas = $usuario->empresas()->where(array('empresas.empresa_id' => 0))->get();
        $empresasA = $empresas->map(function($emp) {
            return $emp->id;
        });

        /*$data = array(
            'empresas' => Empresa_orm::where('organizacion_id', $organizacion->id)->whereIn('id', $empresasA->toArray())->get()->toArray()
        );*/
        $data=['empresas'=>[]];
        $this->template->agregar_contenido($data);

        if ($this->form_validation->run() == FALSE) {
            $this->template->visualizar();
        } else {
            if (!empty($_FILES['logo']['name'])) {
                $files = $_FILES;
                $carpeta = './public/logo/';
                if (!file_exists($carpeta)) {
                    //continue;
                    if (!mkdir($carpeta, 0777, true)) {
                       // continue;
                        throw new Exception('Fallo al crear las carpetas.');
                    }
                }

                $file_name = $files['logo']['name'];
                $extension = "." . end(explode('.', $files['logo']['name']));
                $config['upload_path'] = $carpeta;
                $config['file_name'] = $file_name;
                $config['allowed_types'] = 'gif|jpg|png';

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload("logo")) {
                    $fileINFO = $this->upload->data();
                } else {
                    echo $this->upload->display_errors();
                    $error_image = true;
                }
            }

            $logo = isset($config['file_name']) ? $config['file_name'] : '';
            $uuid_empresa = $this->session->userdata('uuid_empresa');
            $this->cache->delete("menuListaEmpresa-". $uuid_usuario.'-'. $uuid_empresa);
            //$this->cache->delete("empresaDefault-". $uuid_usuario.'-'. $uuid_empresa);
            $this->empresa_request = new CrearEmpresaRequest();
            $datos = ['organizacion_id' => $organizacion->id, 'usuario_id' => $usuario->id, 'logo'=>$logo];
            $empresa = $this->empresa_request->datos($datos);

            if (!is_null($empresa)) {
                $mensaje = array('clase' => 'alert-success', 'contenido' => 'La Empresa se registr&oacute; correctamente');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect('usuarios/listar_empresa/' . $this->session->userdata('uuid_organizacion'));
            }else{
                if($error_image){
                    $mensaje = array('clase' => 'alert-danger', 'contenido' => $this->upload->display_errors());
                }else{
                    $mensaje = array('clase' => 'alert-danger', 'contenido' => 'hubo un error al registrar la Empresa');
                }
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect('usuarios/listar_empresa/' . $this->session->userdata('uuid_organizacion'));
            }
        }
    }

    /* EDITAR EMPRESA */

    public function editar_empresa($uuid_empresa = '') {

        if(!in_array(2,$this->session->userdata('roles')))
        {
            redirect('/');
        }

        $this->template->agregar_titulo_header('Empresa: Editar');
        $this->form_validation->set_rules('campo[nombre]', 'Nombre de Compañia', 'trim|required');
        $this->form_validation->set_rules('campo[descripcion]', 'Dirección', 'trim|required');
        $this->form_validation->set_rules('campo[telefono]', 'Teléfono', 'trim|required');
        $this->form_validation->set_rules('campo[tomo]', 'Tomo', 'required');
        $this->form_validation->set_rules('campo[asiento]', 'Asiento', 'required');
        $this->form_validation->set_rules('campo[folio]', 'Folio', 'required');
        $this->form_validation->set_rules('campo[digito_verificador]', 'Digito Verificador', 'required');
        // = $this->session->userdata('uuid_organizacion');
        //$organizacion = Organizacion_orm::findByUuid($uuid_organizacion);

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-building"></i>Empresa: Editar',
            "ruta" => array(
                0 => array(
                    "nombre" => "Empresa",
                    "activo" => false,
                    "url" => 'configuracion')
            )
        );

        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/empresa.css',
        ));

        if (empty($uuid_empresa))
            redirect('usuarios/listar_empresa/');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        if ($empresa === null) {
            redirect('usuarios/listar-empresa');
        } else {
            $uuid_usuario = $this->session->userdata('huuid_usuario');
            $usuario = Usuario_orm::findByUuid($uuid_usuario);
            $usuario_empresa = $usuario->empresas()->get();

            $empresa_padre = $usuario_empresa->where('empresa_id', 0);

            /*if ($this->input->post('campo[ruc]') != $empresa->ruc) {
                $is_unique = '|is_unique[empresas.ruc]';
            } else {
                $is_unique = '';
            }
            $this->form_validation->set_rules('campo[ruc]', 'R.U.C', 'trim|required' . $is_unique*/

            //Seleccionar empresas padre
            $empresas = $usuario->empresas()->where(array('empresas.empresa_id' => 0))->get();
            $empresasA = $empresas->map(function($emp) {
                return $emp->id;
            });

            /*$data = array(
                'lista_empresas' => Empresa_orm::where('organizacion_id', $organizacion->id)->whereIn('id', $empresasA->toArray())->get()->toArray(),
                'empresa' => $empresa->toArray()
            );*/
            $data = ['empresa' => $empresa->toArray()];
            $this->template->agregar_contenido($data);
            $this->template->agregar_breadcrumb($breadcrumb);

            if ($this->form_validation->run() == FALSE) {

                $this->template->visualizar($breadcrumb);
            } else {
                if (isset($_FILES)) {
                    $files = $_FILES;
                    $carpeta = './public/logo/';
                    $file_name = $files['logo']['name'];
                    $extension = "." . end(explode('.', $files['logo']['name']));
                    $config['upload_path'] = $carpeta;
                    $config['file_name'] = $file_name;
                    $config['allowed_types'] = 'gif|jpg|png';

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload("logo")) {
                        $fileINFO = $this->upload->data();
                    } else {
                        echo $this->upload->display_errors();
                        $error_image = true;
                    }
                }

                $logo = isset($config['file_name']) ? $config['file_name'] : '';
                /*$padre = $this->input->post('padre', true);
                $nombre_empresa = $this->input->post('nombre_empresa', true);
                $direccion = $this->input->post('direccion', true);
                $telefono = $this->input->post('telefono', true);
                $ruc = $this->input->post('ruc', true);
                $campos = array('id' => $empresa->id, 'nombre' => $nombre_empresa, 'empresa_id' => $padre, 'ruc' => $ruc, 'descripcion' => $direccion,
                    'telefono' => $telefono, 'logo' => $logo);
                if (!isset($config['file_name'])) {
                    unset($campos['logo']);
                }*/
                $delete_uuid = $this->session->userdata('uuid_empresa');
                $this->cache->delete("menuListaEmpresa-". $uuid_usuario.'-'. $delete_uuid);
                //$this->cache->delete("empresaDefault-". $uuid_usuario.'-'. $uuid_empresa);
                $this->empresa_request = new CrearEmpresaRequest();
                $datos = ['id' => $empresa->id,'logo'=>$logo];
                $empresa = $this->empresa_request->datos($datos);

                //$guardar = Empresa_orm::actualizar_empresa($campos);

                if (!is_null($empresa)){
                    $mensaje = array('clase' => 'alert-success', 'contenido' => 'La Empresa se registr&oacute; correctamente');
                    $this->session->set_flashdata('mensaje', $mensaje);
                    redirect('usuarios/listar_empresa/' . $this->session->userdata('uuid_organizacion'));
                } else {
                    if ($error_image) {
                        $mensaje = array('clase' => 'alert-danger', 'contenido' => $this->upload->display_errors());
                    } else {
                        $mensaje = array('clase' => 'alert-danger', 'contenido' => 'Hubo un error al Actualizar la Empresa');
                    }
                    $this->session->set_flashdata('mensaje', $mensaje);
                    $this->template->visualizar();
                }
            }
        }//fin else
    }

    /* AGREGAR USUARIOS */

    public function agregar_usuarios($empresa_uuid = '')
    {

        $empresa = Empresa_orm::findByUuid($empresa_uuid);

        //Verificar Empresa
        if (!count($empresa)) {
            redirect('usuarios/listar_empresa/' . $this->session->userdata('uuid_organizacion'));
        }

        $this->FlexioAssets->run();
        $this->FlexioAssets->add('js', ['public/resources/compile/modulos/usuarios/formulario.js']);

        $clause = ['empresa_id'=>$empresa->id,'transaccionales'=>true];

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            'vista' => 'agregar_usuarios',
            "empresa" => $empresa,
            "rol" => Rol_orm::find(array(2, 3)),//modelo arquitectura vieja por cuestiones de tiempo
            "roles" => Rol_orm::where('id', '>', 3)->where("empresa_id", $empresa->id)->get(),//modelo arquitectura vieja por cuestiones de tiempo
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
        ));

        //breadcrumb
        $breadcrumb = ["titulo" => '<i class="fa fa-users"></i> Usuarios - Empresa ' . $empresa->nombre];

        //visualizar
        $this->template->agregar_titulo_header('Listado de Usuarios');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();
    }

    public function ajax_guardar_usuario() {

        if (!isset($_POST)) {
            return false;
        }

        $respuesta = [];
        $datos=[];
        //si passsword existe
        if (!empty($this->input->post('password'))) {
            $datos["password"] = $this->encrypt->encode($this->input->post('password'));
        }

        try{
          $respuesta = (new Flexio\Modulo\Usuarios\FormRequest\AgregarUsuario)->addUsuario($datos);
          if (!empty($respuesta["nuevo"])) {
            $this->enviar_email_invitado($respuesta['usuario']);
          }
        }catch(\Exception $e){
            log_message('error'," __METHOD__  ->  , Linea:  __LINE__  --> " . $e->getMessage() . "\r\n");
            $respuesta=['error'=>true,'mensaje'=>'El usuario ya pertenece a esta empresa'];
        }

        echo json_encode($respuesta);
        exit;
    }

    /**
     * Enviar correo a nuevno usuario creado.
     */
    private function enviar_email_invitado($user) {
        $usuario = Usuario_orm::find($user->id);
        $password = Util::verificar_valor($this->input->post('password'));

        if (!empty($usuario)) {
            $url = base_url('login/activar_usuarios/?email=' . $usuario->email . '&token=' . $usuario->recovery_token);

            $filepath = realpath('./public/templates/email/crear_cuenta/invitacion_usuario.html');

            $usuario_empresa = $usuario->empresas()->latest()->first();
            //Verificar primero si el template de recuperar password existe
            if (!file_exists($filepath)) {
                //No se encontro el archivo ...
                log_message("error", "MODULO: Login --> No se encontro la plantilla de crear cuenta");
                return false;
            }

            //Leer archivo html que contiene el texto del correo
            $htmlmail = read_file($filepath);

            //Reemplazar valores en el archivo html
            //__SITE_URL__
            //__URL_PASSWORD_RECOVER__
            //__YEAR__
            $htmlmail = str_replace("__SITE_URL__", base_url('/'), $htmlmail);
            $htmlmail = str_replace("__EMPRESA__", $usuario_empresa->nombre, $htmlmail);
            $htmlmail = str_replace("__URL_CREAR_CUENTA__", $url, $htmlmail);
            $htmlmail = str_replace("__YEAR__", date('Y'), $htmlmail);
            $htmlmail = str_replace("__USUARIO__", $usuario->email, $htmlmail);
            $htmlmail = str_replace("__PASSWORD__", $password, $htmlmail);

            //Enviar el correo
            $this->email->from('no-reply@pensanomica.com', 'Flexio');
            $this->email->to($usuario->email);
            $this->email->subject('Nueva cuenta Flexio');
            $this->email->message($htmlmail);

            return $this->email->send();
        } else {
            return false;
        }
    }

    //esto se paso a componente de vuejs
    //se dejo este codigo en caso de que se use en algun subpanel
    //aunque no creo que aplique by Francisco Marcano
    public function ocultotabla() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/usuarios/tabla.js'
        ));

        $this->load->view('tabla');
    }

    public function ocultotablaempresa() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/usuarios/tabla_empresa.js'
        ));

        $this->load->view('tabla');
    }

    ///LISTA la organizacion
    public function organizacion() {

        if(!in_array(2,$this->session->userdata ( 'roles' )))
        {
            redirect('/');
        }

        $data = array();
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/modules/usuarios/organizacion.js',
        ));

        //Breadcrum Array
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-sitemap"></i> Organización',
            "filtro" => false,
            "menu" => array(
                "nombre" => "Crear",
                "url" => "usuarios/crear-organizacion",
                "opciones" => array()
            )
        );
        $this->template->agregar_titulo_header('Listado de Organizacion');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);


        $this->template->visualizar($breadcrumb);
    }

    function ajax_listar_organizacion() {


        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $usuario_org = $usuario->organizacion;

        $orgid = $usuario_org->map(function($org) {
            return $org->id;
        });

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = count($orgid->toArray());
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = Organizacion_orm::listar($sidx, $sord, $limit, $start, $orgid->toArray());

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url() . 'usuarios/listar_empresa/' . $row['uuid_organizacion'] . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Empresas</a>';
                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    $row['id'],
                    $row['nombre'],
                    $row['fecha_creacion'],
                    $link_option,
                    $hidden_options
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    function crear_organizacion() {

        if(!in_array(2,$this->session->userdata ( 'roles' )))
        {
            redirect('/');
        }

        $this->template->agregar_titulo_header('Crear Organización');

        $this->form_validation->set_rules('nombre', 'Nombre', 'trim|required');
        $this->form_validation->set_message('required', 'Campo es requerido.');

        $data = array();
        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-sitemap"></i> Crear Organización',
            "ruta" => array(
                0 => array(
                    "nombre" => "Organización",
                    "activo" => false,
                    "url" => '')
            )
        ));

        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/organizacion.css',
        ));

        $this->template->agregar_contenido($data);
        if ($this->form_validation->run() == FALSE) {
            $this->template->visualizar();
        } else {


            $nombre = $this->input->post('nombre', true);
            $campos = array('uuid_organizacion' => 1, 'nombre' => $nombre);
            $org = Organizacion_orm::create($campos);

            if ($org) {
                $uuid_usuario = $this->session->userdata('huuid_usuario');
                $usuario = Usuario_orm::findByUuid($uuid_usuario);
                $usuario->organizacion()->save($org);
                redirect('usuarios/organizacion');
            }
        }
    }

    public function ajax_listar_usuarios() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $uuid_empresa = $this->input->post('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = Usuario_orm::listar($uuid_empresa)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = Usuario_orm::listar($uuid_empresa, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {

                $hidden_options = "";
                $label_colors = array("activo" => "success", "pendiente" => "warning", "inactivo" => "plain");
                $label_class = !empty($row["estado"]) ? $label_colors[strtolower($row["estado"])] : "";
                $texto_estado = $row["estado"] != "Activo" ? "Activar" : "Desactivar";

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="#" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarUsuarioBtn">Editar</a>';
                $hidden_options .= '<a href="#" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success estadoUsuarioBtn">' . $texto_estado . '</a>';

                $roles = $row['roles'];

                //Selecciona Rol Acceso al Sistema
                $rol_sistema_id = (!empty($roles) ? implode("", array_map(function($roles) use ($empresa) {
                                            if (in_array($roles["id"], $this->roles_sistema)) {
                                                return $roles["id"];
                                            }
                                        }, $roles)) : "");

                //Seleccionar Rol del usuario
                $rol_id = (!empty($roles) ? implode("", array_map(function($roles) use ($empresa) {
                                            if (!in_array($roles["id"], $this->roles_sistema)) {
                                                return $roles["id"];
                                            }
                                        }, $roles)) : "");

                //Seleccionar centros contables del usuario
                $centros_contables = $row['filtro_centro_contable'] == 'todos' ? ['todos'] : array_pluck($row['centros_contables'], 'id');

                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    $row['nombre_completo'],
                    $row['email'],
                    $row['fecha_creacion'],
                    $row['filtro_centro_contable'] == 'todos' ? '&lt;&lt;Todos&gt;&gt;' : implode(', ', array_pluck($row['centros_contables'], 'nombre')),
                    '<span class="label label-' . $label_class . '">' . $row["estado"] . '</span>',
                    $link_option,
                    $hidden_options,
                    $row['nombre'],
                    $row['apellido'],
                    $rol_sistema_id,
                    $rol_id,
                    $centros_contables
                );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    function ajax_toggle_estado() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $usuario_id = $this->input->post('usuario_id', true);

        if (empty($usuario_id)) {
            return false;
        }

        $usuario = Usuario_orm::where("id", $usuario_id)->get()->toArray();


        /**
         * Inicializar Transaccion
         */
        Capsule::beginTransaction();

        try {

            if (!empty($usuario[0]["estado"]) && $usuario[0]["estado"] != "Activo") {
                Usuario_orm::where("id", $usuario_id)->update(array("estado" => "Activo"));
                $estado = "activado";
            } else {
                Usuario_orm::where("id", $usuario_id)->update(array("estado" => "Inactivo"));
                $estado = "desactivado";
            }
        } catch (ValidationException $e) {

            // Rollback
            Capsule::rollback();

            echo json_encode(array(
                "id" => false,
                "mensaje" => "Hubo un error tratando de cambiar el estado del usuario."
            ));
            exit;
        }

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();

        echo json_encode(array(
            "mensaje" => "Se ha $estado el usuario satisfactoriamente."
        ));
        exit;
    }

    public function empresas_usuario() {

        $this->template->agregar_titulo_header('Listado de Empresas');
        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-building"></i> Empresa ',
            "ruta" => array(
                0 => array(
                    "nombre" => "Empresa",
                    "activo" => false,
                    "url" => '')
            ),
            "filtro" => false,
            "menu" => array()));
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        ));

        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        ));

        $data = array();
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_empresa_usuario() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $uuid_empresa = $this->input->post('uuid_empresa');

        $count = count($usuario->empresas->count());

        //echo $count;
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        //Si existen variables de orden
        if (isset($sidx) && isset($sord))
            $usuario->empresas()->orderBy($sidx, $sord);

        //Si existen variables de limite
        if (isset($limit))
            $usuario->empresas()->skip($page)->take($limit);

        $rows = $usuario->empresas()->get();


        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['uuid_empresa'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="#" data-id="' . $row['uuid_empresa'] . '" class="btn btn-block btn-outline btn-success">Asignar Default</a>';

                $response->rows[$i]["id"] = $row['uuid_empresa'];
                $response->rows[$i]["cell"] = array(
                    $row['nombre'],
                    $row['created_at'],
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    public function politicas() {

        // Buscando informacion de las politicas
        $info = array();
        $info = $this->usuarios_model->seleccionar_politicas();



        // Verificar si existe POST
        if (isset($_POST['long_minima_usuario'])) {

            $this->form_validation->set_rules('long_minima_usuario', 'long_minima_usuario', 'required|xss_clean');
            $this->form_validation->set_rules('long_maxima_usuario', 'long_maxima_usuario', 'required|xss_clean');

            // Contenedor de error
            $this->form_validation->set_error_delimiters('<label class="required">', '</label>');

            // Mensaje personalizado
            $this->form_validation->set_message('required', 'Este campo es requerido.');

            // Verificar si los campos requeridos no estan vacios
            if ($this->form_validation->run() == true) {
                $response = $this->usuarios_model->guardar_politicas_usuario();

                if ($response == true) {
                    redirect(base_url() . 'usuarios/politicas');
                } else {
                    $data ['_error_message_'] = 'Hubo un error tratando de guardar el rol.';
                }
            }
        }

        if (isset($_POST['long_minima_contrasena'])) {

            $this->form_validation->set_rules('long_minima_contrasena', 'long_minima_contrasena', 'required|xss_clean');

            // Contenedor de error
            $this->form_validation->set_error_delimiters('<label class="required">', '</label>');

            // Mensaje personalizado
            $this->form_validation->set_message('required', 'Este campo es requerido.');

            // Verificar si los campos requeridos no estan vacios
            if ($this->form_validation->run() == true) {
                $response = $this->usuarios_model->guardar_politicas_contrasena();

                if ($response == true) {
                    redirect(base_url() . 'usuarios/politicas');
                } else {
                    $data ['_error_message_'] = 'Hubo un error tratando de guardar el rol.';
                }
            }
        }
        // Add css files
        $assets ['cssFiles'] = array(
            base_url('public/assets/css/plugins/jquery/chosen/chosen.css')
        );
        // Add js files


        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/modules/usuarios/politicas.js',
                //'public/assets/js/modules/usuarios/politicas_contrasena.js'
        ));

        // Build the data array
        // Build the data array
        $this->template->agregar_titulo_header('Ver Usuario');
        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-gear"></i> Políticas',
            /* "ruta" => array (
              0 => array (
              "nombre" => "Administraci&oacute;n",
              "activo" => false,
              "url" => 'configuracion'
              ),
              1 => array (
              "nombre" => 'Usuarios',
              "url" => 'usuarios/listar-usuarios',
              "activo" => false,
              ),
              2 => array (
              "nombre" => '<b>Ver Políticas</b>',
              "activo" => true
              )
              ) */
            "ruta" => array(
                0 => array(
                    "nombre" => "Administraci&oacute;n",
                    "activo" => false,
                    "url" => 'configuracion'
                ),
                1 => array(
                    "nombre" => 'Configuración de Sistema',
                    "activo" => true,
                    "url" => 'configuracion/grupo'
                ),
                2 => array(
                    "nombre" => '<b>Políticas</b>',
                    "activo" => true
                )
            )
        ));
        // $datos = array();
        $datos = array(
            "info" => $info
        );
        if ($this->session->userdata('usuario_actualizado')) {

            //Mostrar mensaje de que la emision fue guardada con exito.
            $datos['message'] = 'El usuario se actualiz&oacute; satisfactoriamente.';

            //Borrar la variable de session
            $this->session->unset_userdata('usuario_actualizado');
        }
        if ($this->session->userdata('politicas_usuario_actualizado')) {

            //Mostrar mensaje
            $datos['message'] = 'Las políticas de usuario se actualiz&oacute; satisfactoriamente.';

            //Borrar la variable de session
            $this->session->unset_userdata('politicas_usuario_actualizado');
        }
        if ($this->session->userdata('politicas_contrasena_actualizado')) {

            //Mostrar mensaje
            $datos['message'] = 'Las políticas de contraseña se actualiz&oacute; satisfactoriamente.';

            //Borrar la variable de session
            $this->session->unset_userdata('politicas_contrasena_actualizado');
        }

        $this->template->agregar_contenido($datos);
        $this->template->visualizar($datos);
    }

}

?>
