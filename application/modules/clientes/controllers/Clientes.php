<?php

/**
 * Clientes
 *
 * Modulo para administrar la creacion, edicion de cliente naturales
 * o juridicos.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  08/10/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\FormularioDocumentos AS FormularioDocumentos;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\Cliente\Models\Cliente as ClienteModel;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\Cliente\HttpRequest\ClienteRequest;
use Flexio\Modulo\CentroFacturable\Repository\CentroFacturableRepository;
use Flexio\Modulo\Cliente\Models\Telefonos;
use Flexio\Modulo\Cliente\Models\Correos;
use Flexio\Modulo\ConfiguracionVentas\Models\TipoClientes as TipoClientes;
use Flexio\Modulo\ConfiguracionVentas\Models\CategoriaClientes as CategoriaClientes;
use Flexio\Modulo\ConfiguracionVentas\Repository\CategoriaClienteRepository as CategoriaClienteRepository;
use Flexio\Modulo\ConfiguracionVentas\Repository\TipoClienteRepository as TipoClienteRepository;
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesRepository;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository;
use Flexio\Modulo\Modulos\Repository\ModulosRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados as AseguradosModel;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas as PersonasModel;
use Flexio\Modulo\Agentes\Models\Agentes;
use Flexio\Modulo\Agentes\Models\AgentesRamos;
use Flexio\Modulo\Ramos\Models\Ramos;
//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Toast;

class Clientes extends CRM_Controller {

    //utils
    protected $FlexioAssets;
    protected $FlexioSession;
    protected $Toast;

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
    protected $clienteRepo;
    protected $centro_facturacion_repository;
    protected $DocumentosRepository;
    protected $upload_folder = './public/uploads/';
    protected $listarCategorias;
    protected $listarTipos;
    protected $categoriaClienteRepository;
    protected  $tipoClienteRepository;
    protected $ClientesPotencialesRepository;
    protected $PreciosRepository;
    protected $ModulosRepository;
    protected $UsuariosRepository;
	protected $AseguradosModel;
	protected $PersonasModel;

    function __construct() {
        parent::__construct();
        //$this->load->model("usuarios/Empresa_orm");
        //$this->load->model("facturas/Factura_orm");
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model("cobros/Cobro_orm");
        $this->load->model('Cliente_orm');
        $this->load->model('clientes_abonos/Clientes_abonos_orm');
        $this->load->model('Catalogo_orm');
        $this->load->model('Catalogo_toma_contacto_orm');
        $this->load->model('clientes_potenciales/Clientes_potenciales_orm');
        $this->load->model('clientes_potenciales/Catalogo_toma_contacto_orm');
        $this->load->model('grupo_clientes/Grupo_cliente_orm');

        $this->load->model('grupo_clientes/Grupo_cliente_agrupador_orm');
        $this->ClientesPotencialesRepository    = new ClientesPotencialesRepository();

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        //HMVC Load Modules
        $this->load->module(array('documentos'));
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        //$this->empresaObj  = Empresa_orm::findByUuid($uuid_empresa);
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("id_usuario");
        $this->id_empresa = $this->empresaObj->id;
        $this->clienteRepo = new ClienteRepository;
        $this->centro_facturacion_repository = new CentroFacturableRepository;
        $this->listarTipos = new TipoClientes();
        $this->listarCategorias = new CategoriaClientes();
        $this->categoriaClienteRepository = new CategoriaClienteRepository();
        $this->tipoClienteRepository = new TipoClienteRepository();
        $this->PreciosRepository = new PreciosRepository;
        $this->ModulosRepository = new ModulosRepository;
        $this->UsuariosRepository = new UsuariosRepository;
		$this->AseguradosModel=new AseguradosModel();
		$this->PersonasModel=new PersonasModel();

        $this->FlexioAssets = new FlexioAssets;
        $this->FlexioSession = new FlexioSession;
        $this->Toast = new Toast;
    }

    public function listar() {

        // Verificar si tiene permiso para crear cliente Natural
        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            redirect('/');
        }

        $data = array();
        $camposGrid = array();

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/modules/stylesheets/clientes.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',

        ));
        $this->assets->agregar_js(array(
            /*'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',*/
            'public/assets/js/moment-with-locales-290.js',
            /*'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',*/
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            //'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            //'public/assets/js/default/jqgrid-toggle-resize.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            //'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            //'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        ));

        $data['info']['categoria'] = $this->categoriaClienteRepository->getCatalogoCategoria($this->id_empresa);
        $data['info']['tipo'] = $this->tipoClienteRepository->getCatalogoTipo($this->id_empresa);
        $data['info']['estado'] = Catalogo_orm::where('tipo', '=', 'estado')->get(array('valor', 'etiqueta'));
        $data['info']['identificacion'] = Catalogo_orm::where('tipo', '=', 'identificacion')->get(array('valor', 'etiqueta'));
       // dd($data);

        $nombreModuloBr = "<script>var str =localStorage.getItem('ms-selected');
                            var capitalize = str[0].toUpperCase()+str.substring(1);
                            document.write(capitalize);
                        </script>";        
        $breadcrumbUrl =base_url("/");
        $brModulo="<a href='$breadcrumbUrl'>$nombreModuloBr</a>";

        $breadcrumb = array("titulo" => '<i class="fa fa-line-chart"></i> Clientes',
            "ruta" => array(
                0 => array(
                    "nombre" => $brModulo,
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Clientes</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "clientes/crear",
                "opciones" => array()
            )
        );

        $this->FlexioAssets->add('vars', [
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata()
        ]);

        $menuOpciones["#agrupadorClientesBtn"] = "Agrupar";
        $menuOpciones["#exportarClienteBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Listado de Clientes');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_get_montos() {

        $cliente_id = $this->input->post("cliente_id");
        $cliente = $this->clienteRepo->find($cliente_id);
        $registro = array();

        if (count($cliente)) {
            $registro['cliente_id'] = $cliente->id;
            $centro_facturable = $cliente->centro_facturable;

            $centro_facturacion_id = '';
            $centro_facturacion_id = count($centro_facturable) == 1 ? $centro_facturable->first()->id : '';

            foreach ($centro_facturable as $row) {
                if($row->principal == 1){$centro_facturacion_id = $row->id;}
            }

            $registro['saldo'] = $cliente->saldo_pendiente;
            $registro['credito_favor'] = $cliente->credito_favor;
            $registro['centros_facturacion'] = $centro_facturable;
            $registro['centro_facturacion_id'] = $centro_facturacion_id;
            $registro['exonerado_impuesto'] = $cliente->exonerado_impuesto;
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($registro))->_display();

        exit;
    }

    public function ajax_listar_centros_facturacion() {

        // Just Allow ajax request
        if(!$this->input->is_ajax_request()){
  	      return false;
  	    }

        $CentroFacturableRepository = new Flexio\Modulo\CentroFacturable\Repository\CentroFacturableRepository;

        $clause = [];
        $clause["campo"] = $this->input->post("campo");
        $clause["empresa_id"] = $this->FlexioSession->empresaId();

        //hacer repositorio de centros de facturacion
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $CentroFacturableRepository->count($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
  		$centros_facturacion = $CentroFacturableRepository->get($clause ,$sidx, $sord, $limit, $start);

        // Constructing a JSON
        $response = new stdClass ();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;


        if (count($centros_facturacion)) {
            foreach ($centros_facturacion as  $i => $row) {
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="' . $row->nombre . '" data-centro="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-sm hidden-xs">Opciones</span></button>';
                $response->rows[$i]["id"] = $row->id;
                $nombre_link = "<a href='#'>" . $row->nombre . "</a> ";

                $hidden_options = '<a href="javascript:" data-id="' . $row->id . '" class="btn btn-block btn-outline btn-success verCentroFacturacion">Ver centro de facturaci&oacute;n</a>';
                if(count($centros_facturacion) > 1)
                {
                    $hidden_options .= '<a href="javascript:" data-id="' . $row->id . '" class="btn btn-block btn-outline btn-success eliminarCentroFacturacion">Eliminar</a>';
                }

                $label_principal = ($row->principal == 1) ? '<span class="label label-warning">Principal</span>':'';
                $response->rows[$i]["cell"] = array(
                    $row->principal,
                    '<a href="javascript:" class="link verCentroFacturacion" data-id="'. $row->id .'">'.$row->nombre.'</a> ' .$label_principal ,
                    count($row->provincia) ? $row->provincia->nombre : '',
                    count($row->distrito) ? $row->distrito->nombre : '',
                    count($row->corregimiento) ? $row->corregimiento->nombre : '',
                    $row->direccion,
                    $link_option,
                    $hidden_options
                );

            }
        }
        echo json_encode($response);
        exit ();
    }

    function ajax_asignar_centro_principal() {

        //Just Allow ajax request
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $clause = array();
        $centro_facturacion_id = $this->input->post('centro_facturacion_id', true);
        $centros_facturacion = \Flexio\Modulo\CentroFacturable\Models\CentroFacturable::where('empresa_id', $this->FlexioSession->empresaId())->get();
        foreach ($centros_facturacion as $row) {
            $row->principal = 0;
            $row->save();
        }
        $centro_facturacion = \Flexio\Modulo\CentroFacturable\Models\CentroFacturable::find($centro_facturacion_id);


        if(count($centro_facturacion)){
            $centro_facturacion->principal = 1;
            $json = json_encode($centro_facturacion->save());
            echo $json;
        }

        exit;
    }

    function ajax_eliminar_centro() {

        //Just Allow ajax request
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $centro_facturacion_id = $this->input->post('centro_facturacion_id', true);
        $centro_facturacion = \Flexio\Modulo\CentroFacturable\Models\CentroFacturable::find($centro_facturacion_id);

        if(count($centro_facturacion)){
            $centro_facturacion->eliminado = 1;
            $json = json_encode($centro_facturacion->save());
            echo $json;
        }

        exit;
    }

    public function ajax_listar() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        /*
          paramentos de busqueda aqui
         */
        $nombre              = $this->input->post('nombre');
        $telefono            = $this->input->post('telefono');
        $correo              = $this->input->post('correo');
        $tipo                = $this->input->post('tipo');
        $categoria           = $this->input->post('categoria');
        $estado              = $this->input->post('estado');
        $tipo_identificacion = $this->input->post('identificacion');
		$modulo= $this->input->post('modulo');

        $clause = [];
        $clause = array('empresa_id' => $this->empresaObj->id);
        $var = '';

        if (!empty($nombre))
            $clause['nombre'] = $nombre;
        if (!empty($telefono)){
            $var = Telefonos::where('telefono','=', $telefono)->get()->toArray();
            if(isset($var[0]["cliente_id"]))
                $clause['id'] = $var[0]["cliente_id"];
            else
                $clause['id'] = -1;
        }
        if (!empty($correo)){
            $var = Correos::where('correo','=', $correo)->get()->toArray();
            if(isset($var[0]["cliente_id"]))
                $clause['id'] = $var[0]["cliente_id"];
            else
                $clause['id'] = -1;
            //$clause['id'] = $var[0]["cliente_id"];
        }
        if (!empty($tipo)) $clause['tipo'] = $tipo;
        if (!empty($categoria)) $clause['categoria'] = $categoria;
        if (!empty($estado)) $clause['estado'] = $estado;
        if (!empty($tipo_identificacion)) $clause['tipo_identificacion'] = $tipo_identificacion;

         list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Cliente_orm::lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $clientes = Cliente_orm::listar($clause, $sidx, $sord, $limit, $start);


        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;


        //dd($clientes);
        if (!empty($clientes->toArray())) {
            $i = 0;
            foreach ($clientes as $row) {
                //Se agrega primer telefono y correo guardado en la base de datos de cada uno respectivamente.
                //Se agrego asi por solicitud de diseño hasta previo cambio.
                
                $telefono ="";
                if (count($row->telefonos_asignados) > 0){ $telefono =$row->telefonos_asignados[0]->telefono;}
                $correo ="";
                if(count($row->correos_asignados) > 0){ $correo = $row->correos_asignados[0]->correo;}

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_cliente . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url('clientes/ver/' . $row->uuid_cliente) . '" data-id="' . $row->uuid_cliente . '" class="btn btn-block btn-outline btn-success">Ver Cliente</a>';
                $hidden_options .= '<a href="' . base_url('clientes/ver/' . $row->uuid_cliente . "?func=agregar_contacto") . '" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Agregar Contacto</a>';
                $hidden_options .= '<a href="' . base_url('clientes/ver/' . $row->uuid_cliente . "?func=agregar_centro_facturacion") . '" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Agregar Centro de Facturaci&oacute;n</a>';
                $hidden_options .= '<a href="' . base_url('cotizaciones/crear/cliente' . $row->id) .'" data-id="' . $row->uuid_cliente . '" class="btn btn-block btn-outline btn-success">Nueva Cotizaci&oacute;n</a>';
               // $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Registrar Actividad</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documento</a>';
				
                //$hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Agregar Caso</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                if($row->estado == 'activo'){
                $hidden_options .= '<a href="' . base_url('anticipos/crear/?cliente=' . $row->uuid_cliente) .'" class="btn btn-block btn-outline btn-success">Crear anticipo</a>';
                }
				if($modulo=='seguros')
				{
					if(($row->tipo_identificacion=='cedula' || $row->tipo_identificacion=='pasaporte') && $row->estado!='bloqueado')
					{
						if ($this->auth->has_permission('listar__convertirInteres', 'clientes/listar') == true) 
						{
							$hidden_options .= '<a href="'.base_url('intereses_asegurados/crear/persona?datcli=' . $row->uuid_cliente) .'" data-id="' . $row->uuid_cliente . '" class="convertirInteres btn btn-block btn-outline btn-success">Convertir a bien asegurado</a>';
						}
					}
				}
                $saldo = empty($row->saldo) ? "0.00" : $row->saldo;
                $response->rows[$i]["id"] = $row->uuid_cliente;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_cliente,
                    $row->codigo,
                    '<a class="link" href="' . base_url('clientes/ver/' . $row->uuid_cliente) . '" class="link">' . $row->nombre . '</a>',
                    $telefono,
                    $correo,
                    '<label class="totales-success">' . number_format($row->credito_favor, 2, '.', ',') . '</label>',
                    '<label class="totales-danger">' . number_format($row->total_saldo_pendiente(), 2, '.', ',') . '</label>',
                    $row->present()->estado_label,
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }
        echo json_encode($response);
        exit;
    }


    public function ocultotabla() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/tabla.js'
        ));

        $this->load->view('tabla');
    }

    public function ocultotabla_centros_facturacion($campo_array = []) {

        if(is_array($campo_array))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($campo_array)
            ]);
        }
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/tabla_centros_facturacion.js'
        ));

        $this->load->view('tabla_centros_facturacion');
    }

    /**
     * Cargar Vista Parcial de Tabla
     * Para Filtrar Clientes desde
     * el Modulo de Contactos.
     *
     * @return void
     */
    public function filtarclientes() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/filtarclientes.js'
        ));

        $this->load->view('tabla');
    }

    public function crear($uuid_cliente_potencial = NULL)
    {
        //permisos
        $acceso = $this->auth->has_permission('acceso', 'clientes/crear/(:any)');
        $this->Toast->runVerifyPermission($acceso);

        if ($this->auth->has_permission('crear__validarAgente', 'clientes/crear/(:any)') ==  true) {
            $validaagente = 1;
        }else{
            $validaagente = 0;
        }
		
		//assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('vars', [
            "vista" => 'crear',
            "acceso" => $acceso ? 1 : 0,
            "desde_modal_cliente" => $this->input->get("func") ? $this->input->get("func") : '0',
            "desde_modal_cliente_ref" => $this->input->get("ref") ? $this->input->get("ref") : '0',
            "validaagente" => $validaagente
        ]);
		
		if(isset($_GET['datint']))
		{
			$datos=$this->AseguradosModel->where('uuid_intereses',hex2bin($_GET['datint']))->first();
			
			$datos_interes=$this->PersonasModel->find($datos->interesestable_id);
			
			 $str = $datos_interes->identificacion;
			 $provinciaVal='';
			 $letraVal='';
			 $tomo='';
			 $asiento='';
			 $pasaporte = '';
			if (substr_count($str, '-'))
			{
				$separateId = explode("-", $str);
				if (count($separateId) == 3) {
					if(!is_numeric($separateId[0])){
						$letraVal =$separateId[0];
						$provinciaVal ="";
					}else{
						$provinciaVal =$separateId[0];           
						$letraVal = "0";
					}
					$tomo = $separateId[1];
					$asiento = $separateId[2];
				} else {
					$provinciaVal = '';
					$letraVal = $separateId[0];
					$tomo = $separateId[1];
					$asiento = $separateId[2];
				}
				$tipo_identificacion = "cedula";
			}
			else
			{
				$tipo_identificacion = "pasaporte";
				$pasaporte = $str;
			}
								
			$this->FlexioAssets->add('vars', [
				"datos_interes"=>$datos_interes,
				"interes"=>'si',
				"tipo_identificacion"=>$tipo_identificacion,
				"provinciaVal"=>$provinciaVal,
				"letraVal"=>$letraVal,
				"tomo"=>$tomo,
				"asiento"=>$asiento,
				"pasaporte"=>$pasaporte
			]);
		}
		else
		{
			$this->FlexioAssets->add('vars', [
				"datos_interes"=>'',
				"interes"=>'no',
				"tipo_identificacion"=>'',
				"provinciaVal"=>'',
				"letraVal"=>'',
				"tomo"=>'',
				"asiento"=>'',
				"pasaporte"=>''
			]);
		}

        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/validarcreacion.js'
        ));

        $nombreModuloBr = "<script>var str =localStorage.getItem('ms-selected');
                            var capitalize = str[0].toUpperCase()+str.substring(1);
                            document.write(capitalize);
                        </script>";        
        $breadcrumbUrl =base_url("/");
        $brModulo="<a href='$breadcrumbUrl'>$nombreModuloBr</a>";

        //breadcrumb
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Clientes',
            "ruta" => [
                ["nombre" => $brModulo, "activo" => false],
                ["nombre" => "Clientes", "activo" => false, "url" => 'clientes/listar'],
                ["nombre" => '<b>Crear</b>',"activo" => true]
            ]
        );

        //render
        $this->template->agregar_titulo_header('Crear Cliente');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();
    }

    public function ocultoformulario($data = NULL)
    {
        $clause = ['empresa_id' => $this->FlexioSession->empresaId(), 'estado' => 1];
        $catalogo_cliente = Flexio\Modulo\Cliente\Models\ClienteCatalogo::get();
        $precios = $this->PreciosRepository->get($clause);

        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/agentes_ramos.js'
        ));

        $this->FlexioAssets->add('js', ['public/resources/compile/modulos/clientes/formulario.js']);
        $this->FlexioAssets->add('vars', [
            "tomas_contacto" => Flexio\Modulo\Cliente\Models\CatalogoTomaContacto::get(),
            "categorias_cliente" => $this->categoriaClienteRepository->getCatalogoCategoria($clause['empresa_id']),
            "tipos_cliente" => $this->tipoClienteRepository->getCatalogoTipo($clause['empresa_id']),
            "estados_cliente" => $catalogo_cliente->filter(function($row){return $row->tipo == 'estado';}),
            "lista_precios_venta" => $precios->filter(function($row){return $row->tipo_precio == 'venta';}),
            "lista_precios_alquiler" =>  $precios->filter(function($row){return $row->tipo_precio == 'alquiler';}),
            "terminos_pago" => $this->ModulosRepository->getTerminosDePago(),
            "usuarios" => $this->UsuariosRepository->getCollectionUsuarios($this->UsuariosRepository->get($clause)),
            "agentes" => Agentes::where("id_empresa", $clause['empresa_id'])->get(),
            "provincias" => Flexio\Modulo\Geo\Models\Provincia::orderBy('nombre', 'asc')->get(),
            "distritos" => Flexio\Modulo\Geo\Models\Distrito::orderBy('nombre', 'asc')->get(),
            "corregimientos" => Flexio\Modulo\Geo\Models\Corregimiento::orderBy('nombre', 'asc')->get(),
            "detalle_unico" => strtotime('now'),
            "ramos" => Ramos::where('padre_id','<>','0')->where('padre_id','<>','"id"')->where("empresa_id", $this->FlexioSession->empresaId())->select("id", "nombre")->get()
        ]);
        $this->load->view('formulario', $data);
    }



    public function ocultoformularioagrupador() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/agrupador.js'
        ));

        $clause = array('empresa_id' => $this->empresaObj->id);
        $data['agrupador'] = Grupo_cliente_orm::listaNombreAgrupadores($clause)->toArray();

        $this->load->view('agrupador', $data);
    }

    function guardar() {

        if ($_POST){

            $cliente = '';
            //creo que estas lineas revisan sin proviene desde un cliente potencial
            //de manera de inactivarlo -> pendiente de integrar en el refactory

            $campos = $_POST['campo'];
            $camposagente = $_POST['agentesCliente'];
            $camposramos = $_POST['ramos_agentes'];
            $camposporcentajes = $_POST['porcentajes_agentes'];

            $tipoidentificacion = $campos['tipo_identificacion'];
            $identificacion = $campos['identificacion'];
            $empresa = $this->id_empresa;

            if ($this->auth->has_permission('crear__validarDuplicado', 'clientes/crear/(:any)') ==  true) {
                if (!isset($campos['id'])) { 
                    $cli = ClienteModel::where("tipo_identificacion", $tipoidentificacion)->where("identificacion", $identificacion)->where("empresa_id", $empresa)->count();
                }else{
                    $cli = ClienteModel::where("tipo_identificacion", $tipoidentificacion)->where("identificacion", $identificacion)->where("empresa_id", $empresa)->where("id", $campos['id'])->count();
                }
                
                if ($cli>0) {
                    $clien = 1;
                }else{
                    $clien = 0;
                }
            }else{
                $cli=0;
            }

            if ($cli>0) {                

                if ($clien == 1) {
                    $cp_id = $this->input->post('id_cp', true);
                    if (!empty($cp_id)) {
                        Clientes_potenciales_orm::upDateClientePotencial($cp_id);
                    }

                    try {
                        $total = Cliente_orm::where('empresa_id', '=', $this->id_empresa)->count();
                        $cliente_request = new ClienteRequest;
                        $codigo = $total + 1;
                        $registro = $cliente_request->guardar($this->id_empresa, $codigo, $this->id_usuario);
                    } catch (\Exception $e) {
                        log_message('error', $e);
                        $this->Toast->setUrl('clientes/listar')->run("exception",[$e->getMessage()]);
                    }
                    $cliente == "";
                }else{
                    $registro = " ";
                    $cliente = "con_identificacion";
                }

            }else{
                $cp_id = $this->input->post('id_cp', true);
                if (!empty($cp_id)) {
                    Clientes_potenciales_orm::upDateClientePotencial($cp_id);
                }

                try {
                    $total = Cliente_orm::where('empresa_id', '=', $this->id_empresa)->count();
                    $cliente_request = new ClienteRequest;
                    $codigo = $total + 1;
                    $registro = $cliente_request->guardar($this->id_empresa, $codigo, $this->id_usuario);
                } catch (\Exception $e) {
                    log_message('error', $e);
                    $this->Toast->setUrl('clientes/listar')->run("exception",[$e->getMessage()]);
                }
            }
            

            if(!is_null($registro)){
                if ($cliente == 'con_identificacion'){
                    $this->Toast->run("error",['<strong>¡Error!</strong> Su solicitud no fue procesada. El n&uacute;mero de identificaci&oacute;n ya existe para esta empresa.']);
                }else{
                    $this->Toast->run("success",[$registro->codigo]);
                }
            }else{
                $this->Toast->run("error");
            }
			
			if($this->input->post('regreso')=='fact')
				redirect(base_url('facturas_seguros/listar'));
			else
				redirect(base_url('clientes/listar'));



        }
    }

    public function ver($uuid = NULL, $opcion = NULL)
    {
        //permisos
        $acceso = $this->auth->has_permission('acceso', 'clientes/ver/(:any)');
        $this->Toast->runVerifyPermission($acceso);

        //variables
        $cliente = $this->clienteRepo->findByUuid($uuid);

        if ($this->auth->has_permission('crear__validarAgente', 'clientes/crear/(:any)') ==  true) {
            $validaagente = 1;
        }else{
            $validaagente = 0;
        }

        $clienteInfo = $this->clienteRepo->getCollectionClienteCampo($cliente);

        //Obtener Los Agentes del Cliente
        $agtramos = AgentesRamos::join("agt_agentes","agt_agentes.id","=","agt_agentes_ramos.id_agente")->where("id_cliente",$clienteInfo['id'])->groupBy("id_agente")->orderBy("agt_agentes_ramos.id_agente", "asc")->get();
        $arrayagentes = array();
        foreach ($agtramos as $agt) {
            array_push($arrayagentes, ['agente_id'=>$agt->id_agente, 'id'=>$agt->id_agente, 'identificacion' => ucwords($agt->tipo_identificacion), 'no_identificacion'=>$agt->identificacion]);
        }

        $agtramosparti = AgentesRamos::join("agt_agentes","agt_agentes.id","=","agt_agentes_ramos.id_agente")->where("id_cliente",$clienteInfo['id'])->orderBy("agt_agentes_ramos.id_agente", "asc")->get();

        $arrayagentesp = array();
        $arrayagentesramos = array();
        $arrayagentesparti = array();
        $idant = "";
        $partant = "";
        $idram = "";
        foreach ($agtramosparti as $val) {
            if ($idant == "") {
                array_push($arrayagentesramos, $val->id_ramo);
            }else if($idant == $val->id_agente){
                if($partant == $val->participacion){
                    array_push($arrayagentesramos, $val->id_ramo);
                }else{                    
                    array_push($arrayagentesparti, ['ramos'=>$arrayagentesramos, 'porcentajes'=>$partant, 'id'=>'', 'requerido' => 'true']);
                    $arrayagentesramos = array();
                    array_push($arrayagentesramos, $val->id_ramo);
                }                
            }else{               
                array_push($arrayagentesparti, ['ramos'=>$arrayagentesramos, 'porcentajes'=>$partant, 'id'=>'', 'requerido' => 'true']);
                array_push($arrayagentesp, $arrayagentesparti);
                $arrayagentesramos = array();
                $arrayagentesparti = array();
                array_push($arrayagentesramos, $val->id_ramo);
            }
            $idant = $val->id_agente;
            $partant = $val->participacion;
            $idram = $val->id_ramo;
        }

        if (!empty($arrayagentesramos)) {
            array_push($arrayagentesparti, ['ramos'=>$arrayagentesramos, 'porcentajes'=>$partant, 'id'=>'', 'requerido' => 'true']);
            array_push($arrayagentesp, $arrayagentesparti);
        }
        /*array_push($arrayagentesparti, ['ramos'=>$arrayagentesramos, 'porcentajes'=>$partant, 'id'=>'']);
        array_push($arrayagentes, $arrayagentesparti);
        $arrayagentesramos = array();
        $arrayagentesparti = array();*/

        $clienteInfo['agentesCliente'] = $arrayagentes;
        $clienteInfo['agentesRamoCliente'] = $arrayagentesp;
        //$clienteInfo['agentesRamoCliente'] = array(array(['ramos'=>'', 'porcentajes'=>'', 'id'=>'']));

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('css',['public/assets/css/modules/stylesheets/clientes.css']);
        $this->FlexioAssets->add('vars', [
            "vista" => 'ver',
            "acceso" => $acceso ? 1 : 0,
            'cliente' => $clienteInfo,
            "desde_modal_cliente" => $this->input->get("func") ? $this->input->get("func") : '0',
            "validaagente" => $validaagente,
            "interes"=>'no'
        ]);

        $nombreModuloBr = "<script>var str =localStorage.getItem('ms-selected');
                            var capitalize = str[0].toUpperCase()+str.substring(1);
                            document.write(capitalize);
                        </script>";        
        $breadcrumbUrl =base_url("/");
        $brModulo="<a href='$breadcrumbUrl'>$nombreModuloBr</a>";

        //breadcrumb
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Clientes',
            "ruta" => [
                ["nombre" => $brModulo, "activo" => false],
                ["nombre" => "Clientes", "activo" => false, "url" => 'clientes/listar'],
                ["nombre" => '<b>Detalle</b>',"activo" => true]
            ],
            "menu" => [
                'url' => 'javascipt:',
                'nombre' => "Acción",
                "opciones" => [
                    "#agregarContactoBtn" => "Agregar Contacto",
                    "#agregarCentroFacturacionBtn" => "Agregar Centro de Facturaci&oacute;n"
                ]
            ],
        );


        //nuevo esquema de subpanels
        $data = [];
        $data['subpanels'] = [
            'oportunidades' => ['cliente' => $cliente->id],
            'contactos' => ['cliente' => $cliente->id],
            'cotizaciones' => ['cliente' => $cliente->id],
            'clientes_abonos' => ['cliente' => $cliente->id],
            'documentos' => ['cliente' => $cliente->id]
        ];

        $this->assets->agregar_js(array(
            //'public/assets/js/modules/clientes/validarcreacion.js',
            //'public/assets/js/modules/clientes/agrupador.js'
        ));

        //render
        $this->template->agregar_titulo_header('Editar Cliente');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_cliente_potencial() {
        //ID cliente potencial.

        $cliente_id = $this->input->post('id', true);
        $clientePot = null;
        $client = array('empresa_id'=>$this->id_empresa,'id_cliente_potencial' => $cliente_id);
        //$clientePot = Clientes_potenciales_orm::select_cliente_potencial($client);
        if ($client['id_cliente_potencial']<>0) {
        $clientePot = $this->ClientesPotencialesRepository->findBy($client);
        //$clientePot->load("telefonos_asignados","correos_asignados");
      //  dd($clientePot);
        }

        if ($clientePot != null) {
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($clientePot->toarray()))->_display();
        }
        exit;
    }


    public function ajax_get_agente() {
        //ID cliente potencial.

        $agente_id = $this->input->post('agente', true);
        if ($agente_id == "") {
            $agente_id = 0;
        }
        $agt = Agentes::where("id", $agente_id)->get();

        if ($agt != null) {
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($agt->toarray()))->_display();
        }
        exit;
    }

    /**
     * Funcion para exportar los clientes potenciales
     * seleccionados a formato CSV.
     *
     * return void
     */
    public function exportar() {
        if (empty($_POST)) {
            exit();
        }

        $ids = $this->input->post('ids', true);
        //dd($ids);
        $id_clientes = explode(",", $ids);
        //dd($id_clientes);
        if (empty($id_clientes)) {
            return false;
        }

        $uuid_clientes = collect($id_clientes);
        $uuid_clientes->transform(function ($item) {
             return hex2bin($item);
        });

      $uuuid = $uuid_clientes->toArray();
        $clause = array(
            "id_cliente" => $uuid_clientes->toArray()
        );
        // dd($clientesIds);
        $clientes = Cliente_orm::selectExportClient($uuuid);
        //dd($clientes);
        if (empty($clientes)) {
            return false;
        }

        $i = 0;
        foreach ($clientes as $row) {

            $telefono ="";
            if (count($row->telefonos_asignados) > 0){ $telefono =$row->telefonos_asignados[0]->telefono;}
            $correo ="";
            if(count($row->correos_asignados) > 0){ $correo = $row->correos_asignados[0]->correo;}
            $datos[$i]['codigo'] = Util::verificar_valor($row['codigo']);
            $datos[$i]['nombre'] = utf8_decode(Util::verificar_valor($row['nombre']));
            $datos[$i]['telefono'] = Util::verificar_valor($telefono);
            $datos[$i]['correo'] = Util::verificar_valor($correo);
            $datos[$i]['tipo'] = count($row->tipo_cliente)?utf8_decode(Util::verificar_valor($row->tipo_cliente[0]->nombre)):'';
            $datos[$i]['categoria'] = count($row->categoria_cliente)?utf8_decode(Util::verificar_valor($row->categoria_cliente[0]->nombre)):'';
            $datos[$i]['credito'] = Util::verificar_valor($row['credito_favor']);
            $datos[$i]['saldo'] = Util::verificar_valor($row->total_saldo_pendiente());
            $i++;
        }
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'No. Cliente',
            'Nombre',
            utf8_decode('Teléfono'),
            'E-mail',
            utf8_decode('Tipo de cliente'),
            utf8_decode('Categoría de cliente'),
            utf8_decode('Crédito a favor'),
            'Saldo acumulado'
        ]);
        $csv->insertAll($datos);
        $csv->output("Clientes-" . date('ymd') . ".csv");
        exit();
    }

    public function guardar_agrupador() {
        $id_client = $this->input->post('id_clientes', true);
        $id_grupo = $this->input->post('id_grupo', true);
        // dd($id_grupo);
        //$id_clientes = explode(",", $id_client);
        //$id_clientes = $id_client->toArray();
       // dd($id_clientes);
        if (empty($id_client)) {
            return false;
        }

        $uuid_clientes = collect($id_client[0]);
        // dd($uuid_clientes);
        $uuid_clientes->transform(function ($item) {
             return hex2bin($item);
        });
       $id_grup = (int)$id_grupo[0];
       //dd($id_grup);
      $uuuid = $uuid_clientes->toArray();
      foreach ($uuuid AS $uuid){
           $response = Grupo_cliente_agrupador_orm::guardar($uuid, $id_grup);
      }
     // dd($uuuid);

       // dd($id_grupo);
       $mensaje = array(
            "respuesta" => true,
            "mensaje" => "Se han agrupado los clientes seleccionados satisfactoriamente."
        );
     // $mensaje = array('clase' => 'alert-success', 'contenido' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ');
       echo json_encode($mensaje);
       exit;
    }

    function ajax_centro_facturable() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $id = $this->input->post('centro_facturacion_id');
        $centro_facturacion = $this->centro_facturacion_repository->find($id);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($centro_facturacion->tiene_relaciones()))->_display();
        exit;
    }

    function ajax_get_centros_facturacion() {
      $id = $this->input->post('cliente_id');
      $cliente = \Flexio\Modulo\Cliente\Models\Cliente::with(array('centro_facturable'))->where("id", $id)->get(array("id","nombre"))->toArray();
      $centros_facturable = !empty($cliente[0]) ? $cliente[0]["centro_facturable"] : "";

      $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                  ->set_output(json_encode(['centro_facturable'  => $centros_facturable], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
      exit;
    }

    function ajax_guardar_comentario() {

    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
      	$model_id   = $this->input->post('modelId');
    	$comentario = $this->input->post('comentario');
     	$comentario = ['comentario'=>$comentario,'usuario_id'=>$this->id_usuario];
     	$cliente = $this->clienteRepo->agregarComentario($model_id, $comentario);
     	$cliente->load('comentario_timeline');

     	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    	->set_output(json_encode($cliente->comentario_timeline->toArray()))->_display();
    	exit;
    }

    function ocultoformulariocomentarios() {

    	$data = array();

    	$this->assets->agregar_js(array(
    			'public/assets/js/plugins/ckeditor/ckeditor.js',
    			'public/assets/js/plugins/ckeditor/adapters/jquery.js',
    			'public/assets/js/modules/clientes/vue.comentario.js',
    			'public/assets/js/modules/clientes/formulario_comentario.js'
    	));

    	$this->load->view('formulario_comentarios');
    	$this->load->view('comentarios');
    }

    function documentos_campos() {

    	return array(
    	array(
    		"type"		=> "hidden",
    		"name" 		=> "cliente_id",
    		"id" 		=> "cliente_id",
    		"class"		=> "form-control",
    		"readonly"	=> "readonly",
    	));
    }

    function ajax_guardar_documentos() {
    	if(empty($_POST)){
    		return false;
    	}

    	$clientes_id = $this->input->post('cliente_id', true);
        $modeloInstancia = $this->clienteRepo->findByUuid($clientes_id);
    	$this->documentos->subir($modeloInstancia);
    }


    

    /*function ajax_verificar_identificacion(){

        $response = [];
        $clause = array('empresa_id' => $this->empresaObj->id);
        $cliente_request = new ClienteRequest;
        $verificar = $cliente_request->getIdentificacionClientes($clause)->count();
        if ($verificar > 0){
            $response["tipo"] == "success";
        }else{
            $response["tipo"] == "danger";
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))->_display();

              exit;
    }*/

    function ajax_catalogo_search(){
        $response =$this->clienteRepo->clienteCatalogo ($this->clienteRepo->search($_POST,null,null,10));

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))->_display();
        exit;
    }

    public function existsIdentificacion() {

        $campos = $_POST['campo'];
        $response = new stdClass();

            $tipoidentificacion = $campos['tipo_identificacion'];
            $identificacion = $campos['identificacion'];
            $empresa = $this->id_empresa;
        
        if ($this->auth->has_permission('crear__validarDuplicado', 'clientes/crear/(:any)') ==  true) {
            $cli = ClienteModel::where("tipo_identificacion", $tipoidentificacion)->where("identificacion", $identificacion)->where("empresa_id", $empresa)->count();
        }else{
            $cli=0;
        }

        if($cli > 0){
            $response->existe =  true;
        }else{
            $response->existe =  false;
        }

        echo json_encode($response);
        exit;
    }

    

}

?>
