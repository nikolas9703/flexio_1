<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;

class Honorarios_seguros extends CRM_Controller
{

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
	protected $aseguradoras;

    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        //$this->load->model('remesas/Remesas_orm');


        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_empresa = $this->empresaObj->id;


        $uuid_usuario = $this->session->userdata("huuid_usuario");
        $usuario = Usuarios::findByUuid($uuid_usuario);
        $this->usuario_id = $usuario->id;
		
		$this->aseguradoras= new Aseguradoras();
    }

    public function listar(){
		
        if (is_null($this->session->flashdata('mensaje')) ) {
           $mensaje = []; 
        } else {
            $mensaje = $this->session->flashdata('mensaje');
        }

        $this->_css();
        $this->_js();

        $data = array();
		$data['mensaje'] = $mensaje;
       /* if (!$this->auth->has_permission('acceso', 'honorarios_seguros/listar') == true) {
            $acceso = 0;
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar a honorarios', 'titulo' => 'Honorarios ');
            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url(''));
        }*/

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Honorarios',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Honorarios</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );
		$this->assets->agregar_js(array(
			'public/assets/js/modules/honorarios_seguros/listar.js',
        ));

        $breadcrumb["menu"] = array(
            "url" => 'honorarios_seguros/crear',
            "clase" => 'modalOpcionesCrear',
            "nombre" => "Crear"
        );
		
		
		$this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));
        
		$data['aseguradoras']=$this->aseguradoras->select('id','nombre')->where('empresa_id','=',$this->id_empresa)->get();
		$data['usuarios']=Usuarios::join('usuarios_has_roles', 'usuario_id', '=', 'usuarios.id')
        ->where('usuarios_has_roles.empresa_id', '=', $this->id_empresa)
        ->where('usuarios.estado', '=', 'Activo')
        ->select('usuarios.id', 'nombre','apellido')
        ->groupBy('usuarios.id')
        ->get();
        
        $menuOpciones["#exportarBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Listado de Honorarios');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

    public function ajax_listar_remesas()
    {
        //Just Allow ajax request

        if(!$this->input->is_ajax_request()){
            return false;
        }
		
		$clause = array(
    		"empresa_id" =>  $this->id_empresa
    	);
        
		$no_remesa= $this->input->post('no_remesa', true);
		$nombre_aseguradora= $this->input->post('nombre_aseguradora', true);
		$inicio_fecha= $this->input->post('inicio_fecha', true);
		$fin_fecha= $this->input->post('fin_fecha', true);
		$usuario= $this->input->post('usuario', true);
		$estado= $this->input->post('estado', true);
		
		if(!empty($no_remesa)){
    		$clause["no_remesa"] = array('LIKE', "%$no_remesa%");
    	}
		if(!empty($nombre_aseguradora)){
    		$clause["aseguradora_id"] = $nombre_aseguradora;
    	}
		if(!empty($inicio_fecha)){
			$fecha1=date('Y-m-d', strtotime($inicio_fecha));
    		$clause["fecha1"] = $fecha1;
    	}
		if(!empty($fin_fecha)){
			$fecha2=date('Y-m-d', strtotime($fin_fecha));
    		$clause["fecha2"] = $fecha2;
    	}
		if(!empty($usuario)){
    		$clause["usuario_id"] = $usuario;
    	}
		if(!empty($estado)){
    		$clause["seg_remesas_entrantes.estado"] = $estado;
    	}
		
		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		
    	$count = $this->RemesasEntrantesRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = $this->RemesasEntrantesRepository->listar($clause, $sidx, $sord, $limit, $start);
    	
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;

        if(!empty($rows)){
            foreach ($rows as  $row){
                $tituloBoton = ($row['estado']!=1)?'Habilitar':'Deshabilitar';
                $hidden_options = "";
				
				if($row->estado=='liquidada')
				{
					$clase_estado='background-color: #5cb85c';
					$estado='Liquidada';
				}
				else if($row->estado=='por_liquidar')
				{
					$clase_estado='background-color: #5bc0de';
					$estado='Por liquidar';
				}
				else if($row->estado=='en_proceso')
				{
					$clase_estado='background-color: #F8AD46';
					$estado='En proceso';
				}
				else
				{
					$clase_estado='label-danger';
					$estado='Anulada';
				}
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$hidden_options = '<a href="'. base_url('remesas_entrantes/editar/'. bin2hex($row->uuid_remesa_entrante)) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
			
				if($row['monto']>=0)
					$estilomonto='totales-success';
				else
					$estilomonto='totales-danger';
                $level = substr_count($row['nombre'],".");
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'no_remesa'=> '<a href="'.base_url('remesas_entrantes/editar/'.bin2hex($row->uuid_remesa_entrante)).'">'.$row['no_remesa']."</a>",
                    'pagos_remesados' => $row['pagos_remesados'],
                    'aseguradora_id' => $row['nom_aseguradora'],
                    'monto' => '<label class="'.$estilomonto.'">'.number_format($row['monto'], 2, '.', ',').'</label>',
					'fecha' => $row['fecha'],
                    'usuario_id' => $row->nom_usuario." ".$row->ape_usuario,
					'estado' => '<span style="color:white; '.$clase_estado.'" class="btn btn-xs btn-block estadoSolicitudes">'.$estado.'</span>',
					'link' => $link_option,
                    'options'=>$hidden_options 
                ) );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

     private function _css() {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            //'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
            //'public/assets/css/plugins/jquery/toastr.min.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css',
            'public/assets/css/modules/stylesheets/remesasentrantes.css',
			'public/assets/css/modules/stylesheets/cobros.css'
        ));
    }

    private function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/subir_documento_modulo.js',
                //'public/assets/js/default/grid.js',
        ));
    }
    
}