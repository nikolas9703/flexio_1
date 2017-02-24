<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Cotizaciones de alquiler
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\CotizacionesAlquiler\Repository\CotizacionesAlquilerRepository;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionCatalogoRepository as CotizacionesAlquilerCatalogosRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerCatalogosRepository;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Library\Util\AuthUser;
use Flexio\Modulo\Inventarios\Models\Categoria;

//otros
use Dompdf\Dompdf;

class Cotizaciones_alquiler extends CRM_Controller
{
    /**
     * Atributos
     */
    private $empresa_id;
    private $empresaObj;
    protected $CotizacionesAlquilerRepository;
    protected $CotizacionesAlquilerCatalogosRepository;
    protected $ClienteRepository;
    protected $ClientesPotencialesRepository;
    protected $CentrosContablesRepository;
//    protected $OrdenesCompraRepository;
    protected $UsuariosRepository;
    protected $ItemsCategoriasRepository;
    protected $ContratosAlquilerCatalogosRepository;
    protected $PreciosRepository;
    protected $CuentasRepository;
    protected $ImpuestosRepository;

    /**
     * Método constructor
     */
    public function __construct()
    {
        parent::__construct();
        //cargar los modelos
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Usuario_orm');

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id = $this->empresaObj->id;

        $this->CotizacionesAlquilerRepository = new CotizacionesAlquilerRepository();
        $this->CotizacionesAlquilerCatalogosRepository = new CotizacionesAlquilerCatalogosRepository();
        $this->ClienteRepository = new ClienteRepository();
        $this->ClientesPotencialesRepository = new ClientesPotencialesRepository();
        $this->CentrosContablesRepository = new CentrosContablesRepository();
//        $this->OrdenesCompraRepository              = new OrdenesCompraRepository();
        $this->UsuariosRepository = new UsuariosRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->ContratosAlquilerCatalogosRepository = new ContratosAlquilerCatalogosRepository();
        $this->PreciosRepository = new PreciosRepository;
        $this->CuentasRepository = new CuentasRepository();
        $this->ImpuestosRepository = new ImpuestosRepository();
    }

    public function listar()
    {
        $data = array();
        $mensaje ='';
        if(!$this->auth->has_permission('acceso'))
        {
            $mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
            $this->session->set_flashdata('mensaje', $mensaje);
        }
        if(!empty($this->session->flashdata('mensaje')))
        {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }

        $this->_css();$this->_js();

        $breadcrumb = array( "titulo" => '<i class="fa fa-line-chart"></i> Cotizaciones ',
            "ruta" => array(
                0 => ["nombre" => "Alquileres", "activo" => false],
                1 => ["nombre" => '<b>Cotizaciones</b>', "activo" => true]
            ),
            "menu" => [
				"nombre" => "Crear",
				"url" => "cotizaciones_alquiler/crear",
				"opciones" => array(
					"#exportarLnk" => "Exportar"
				)
			]
        );
        //$breadcrumb["menu"]["opciones"]["#exportarCotizacionesAlquiler"] = "Exportar";

        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));
        $repositoryCliente = new  Flexio\Modulo\Cliente\Repository\ClienteRepositorio;
        $clause = ['empresa_id' => $this->empresa_id,'estado'=>"Activo"];
        $data['clientes']   = $repositoryCliente->getClientes($this->empresa_id)->activos()->fetch();
        $data['estados']    = $this->CotizacionesAlquilerCatalogosRepository->getEtapas();
        $data['centros']    = $this->CentrosContablesRepository->get($clause);
        $data['usuarios']    = $this->UsuariosRepository->get($clause);
        $this->template->agregar_titulo_header('Cotizaciones ');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

	public function ajax_listar()
	{
		if(!$this->input->is_ajax_request()) return false;

		$clause                 = $this->input->post();
		$clause['campo']        = $this->input->post();
		$jqgrid = new Flexio\Modulo\CotizacionesAlquiler\Services\CotizacionAlquilerJqgrid($this->auth);
		$clause['empresa']   = $this->empresa_id;
		$clause['tipo'] = 'alquiler';
		//if(!AuthUser::is_owner())$clause['creado_por'] =  AuthUser::getId();
		
		if (!$this->auth->has_permission('ver_todos','cotizaciones_alquiler/listar')) {
			$clause["creado_por"] = $this->session->userdata("id_usuario");
		}
		
		
		
		$response = $jqgrid->listar($clause);

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($response))->_display();
		exit;
	}



    /**
     * Método para generar código del subcotizacion
     */
    private function _generar_codigo()
    {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->CotizacionesAlquilerRepository->count($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('QTA'.$year,$total + 1);
        return $codigo;
    }


    private function _css()
    {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/css/default/ladda.min.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/modules/stylesheets/cotizaciones_alquiler.css',
        ));
    }


    private function _js()
    {
        $this->assets->agregar_js(array(

            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/default/accounting.min.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/new-select2.js',
            'public/assets/js/default/vue/directives/item-comentario.js',
            'public/assets/js/default/vue/directives/porcentaje.js',
            'public/assets/js/default/vue/directives/inputmask3.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
        ));
    }

	public function exportar()
	{
		$post = $this->input->post();
		if(empty($post)){
			exit();
		}

		//$cotizaciones_alquiler = $this->CotizacionesAlquilerRepository->get(['ids', $post['ids']]);

		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([utf8_decode('No. Cotización'), 'Cliente', utf8_decode('Fecha de emisión'), utf8_decode('Válido hasta'), 'Centro contable', 'Creado por', 'Estado']);
		
		$registros = $this->CotizacionesAlquilerRepository->getCollectionExportar(['ids' => $post['ids']]);
		$i=0;
		foreach ($registros AS $row)
		{
			
			$c = CentrosContables::where("id",$row->centro_contable_id)->get()->toArray();
			$centro_contable = $c[0]["nombre"];
			
			$csvdata[$i]['numero'] = $row->codigo;
			$csvdata[$i]["cliente"] = utf8_decode(Util::verificar_valor($row->cliente->nombre));
			$csvdata[$i]["fecha_emision"] = utf8_decode($row->fecha_desde);
			$csvdata[$i]["fecha_hasta"] = utf8_decode($row->fecha_hasta);
			$csvdata[$i]["centro_contable"] = ($centro_contable);
			$csvdata[$i]["creado_por"] = utf8_decode($row->vendedor->nombre." ".$row->vendedor->apellido);
			$csvdata[$i]["estado"] = ucwords(utf8_decode($row->estado));
			
			$i++;
		}
		
		$csv->insertAll($csvdata);
		
		$csv->output("CotizacionAlquiler-". date('ymd') .".csv");
		exit();
	}

    public function crear(){

        $acceso = 1;
        $editarPrecioUnidad_adicional = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        if (!$this->auth->has_permission('editarPrecioUnidad_adicional', 'cotizaciones_alquiler/crear')){
            $editarPrecioUnidad_adicional = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');

        }

        $clause = array(
            "empresa_id" => $this->empresa_id
        );

       	$this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
          'public/assets/js/default/vue/directives/pop_over_precio.js',
          'public/assets/js/default/vue/directives/pop_over_cantidad.js',
            'public/resources/compile/modulos/cotizaciones_alquiler/crear-alquiler-cotizacion.js',
        ));

        $precios_venta_id_default = $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "venta", "principal" => 1));

        $clause2 = ['empresa_id' => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'tipo_cuenta_id' => '4', 'vendedor' => true];
        $this->assets->agregar_var_js(array(
            "vista"                 => 'crear',
            "acceso"                => $acceso == 0 ? 0 : 1,
            "usuario_id"            => $this->session->userdata("id_usuario"),
            'clientes'              => $this->ClienteRepository->getCollectionClientes($this->ClienteRepository->getClientesEstadoActivo($clause2)->get()),
            "lista_precio_alquiler" => $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "alquiler")),
            'precios_venta_id_default' => !empty(collect($precios_venta_id_default)->toArray()) ? $precios_venta_id_default[0]["id"] : "",
            'precios'               => $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "venta")),
            'categorias'            => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause2)),
            'cuentas'               => $this->CuentasRepository->get($clause2),
            'impuestos'             => $this->ImpuestosRepository->get($clause2),
            //'editarPrecioUnidad_adicional' => $editarPrecioUnidad_adicional,
            "editar_precio" => $editarPrecioUnidad_adicional//$this->auth->has_permission('crear__editarPrecioOrdenAlquiler', 'ordenes_alquiler/crear') == true ? 1 : 0
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Cotizaciones: Crear ',
            "ruta" => array(
                0 => array(
                    "nombre" => "Alquileres",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Cotizaciones',
                    "activo" => true,
                    "url" => "cotizaciones_alquiler/listar"
                ),
                2 => array(
                    "nombre" => '<b>Crear</b>',
                    "activo" => true
                )
            )
        );

        $this->template->agregar_titulo_header('Cotizaciones: Crear ');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function editar($uuid) {

        $acceso = 1;
        $mensaje = array();
        $cotizacion_alquiler = $this->CotizacionesAlquilerRepository->findBy(['empresa_id'=>$this->empresa_id,'uuid_cotizacion'=>$uuid]);
        if (!$this->auth->has_permission('acceso','cotizaciones_alquiler/editar/(:any)') || is_null($cotizacion_alquiler)) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }
        $editarPrecioUnidad_adicional = 1;
        if (!$this->auth->has_permission('editarPrecioUnidad_adicional', 'cotizaciones_alquiler/editar/(:any)')){
            $editarPrecioUnidad_adicional = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');

        }

        $this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
          'public/assets/js/default/vue/directives/pop_over_precio.js',
          'public/assets/js/default/vue/directives/pop_over_cantidad.js',
          'public/resources/compile/modulos/cotizaciones_alquiler/crear-alquiler-cotizacion.js',
        ));

        if(!is_null($cotizacion_alquiler))$cotizacion_alquiler->load('items');

        $precios_venta_id_default = $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "venta", "principal" => 1));
        $clause2 = ['empresa_id' => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'tipo_cuenta_id' => '4', 'vendedor' => true];
        $this->assets->agregar_var_js(array(
            "vista"                     => 'editar',
            "acceso"                    => $acceso == 0 ? $acceso : $acceso,
            "cotizacion_alquiler"         => $cotizacion_alquiler,
            "uuid_cotizacion"   => $uuid,
            'clientes'              => $this->ClienteRepository->getCollectionClientes($this->ClienteRepository->getClientesEstadoActivo($clause2)->get()),
            "lista_precio_alquiler" => $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "alquiler")),
            'precios_venta_id_default' => !empty(collect($precios_venta_id_default)->toArray()) ? $precios_venta_id_default[0]["id"] : "",
            'precios'    => $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "venta")),
            'categorias'            => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause2)),
            'cuentas'               => $this->CuentasRepository->get($clause2),
            'impuestos'             => $this->ImpuestosRepository->get($clause2),
            "editar_precio" => $editarPrecioUnidad_adicional
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Cotizaciones: '.$cotizacion_alquiler->codigo,
            "menu" => array(
             						"nombre" => 'Acci&oacute;n',
             						"url"	 => '#',
             						"opciones" => array()
             				),
            "ruta" => array(
                0 => array(
                    "nombre" =>  "Alquileres" ,
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Cotizaciones',
                    "activo" => false,
                    "url" => "cotizaciones_alquiler/listar"
                ),
                2 => array(
                    "nombre" =>'<b>Detalle</b>',
                    "activo" => true
                )
            )

        );
        $breadcrumb["menu"]["opciones"]["cotizaciones_alquiler/imprimir_cotizacion_de_alquiler/" . $uuid] = "Imprimir";

        $this->template->agregar_titulo_header('Cotizaciones: '.$cotizacion_alquiler->codigo);
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function ocultoformulario() {
        $this->load->view('formulario');
    }

    public function ocultotabla($subpanels = null) {


        if(is_array($subpanels) && !empty($subpanels))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($subpanels)
            ]);
        }
        $this->assets->agregar_js(array(
            'public/assets/js/modules/cotizaciones_alquiler/tabla.js'
        ));
        $this->load->view('tabla');
    }

    //agregada funcion "ocultotablaV2" por jose luis
    public function ocultotablaV2($sp_string_var = []) {

        /*$this->assets->agregar_js(array(
            'public/assets/js/modules/cotizaciones/tabla.js'
        ));*/

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));
        }

        $this->load->view('tabla');
    }

    public function ocultoformulario_items_cotizados($cotizacion_alquiler = array()) {
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);

        if (isset($cotizacion_alquiler['info'])){$data['info'] = $cotizacion_alquiler['info'];}

        $categorias = $this->ItemsCategoriasRepository->get(['empresa_id'=>$this->empresa_id,'conItems'=>true]);
        $categorias->load('items_contratos_alquiler');
        $this->assets->agregar_var_js(array(
            "categorias"        => $categorias,
            "ciclos_tarifarios" => $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'tarifa'])
        ));

        $this->load->view('formulario_items_cotizados', $data);
        $this->load->view('templates/cotizacion_items');
    }

    public function guardar()
    {

        $post = $this->input->post();
        //dd($post);

        if (!empty($post)) {
        $formGuardar = new Flexio\Modulo\CotizacionesAlquiler\FormRequest\GuardarCotizacionAlquiler;

            try {

            $cotizacion_alquiler = $formGuardar->guardar();
            }catch (\Exception $e) {
                log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('cotizaciones_alquiler/listar'));
            }


            if (!is_null($cotizacion_alquiler)) {
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $cotizacion_alquiler->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('cotizaciones_alquiler/listar'));
        }
    }
    function ajax_get_cotizacion(){
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $uuid = $this->input->post('uuid', TRUE);
        $cotizacion_alquiler = $this->CotizacionesAlquilerRepository->findBy(['empresa_id'=>$this->empresa_id,'uuid_cotizacion'=>$uuid]);
        $cotizacion_alquiler->load('items_alquiler.item.atributos','items_adicionales','items_adicionales.item.unidades','items_adicionales.item.atributos','landing_comments');

        $cotizacion_alquiler->items_alquiler->each(function($item) use($cotizacion_alquiler) {
            if ($item->comentario!=''){
                $fieldset = array(
                    'comentario'=>$item->comentario,
                    "usuario_id" => $cotizacion_alquiler->creado_por,
                    "created_at" =>$cotizacion_alquiler->created_at
                );
                $comentarios = new Comentario($fieldset);
                $cotizacion_alquiler->landing_comments->push($comentarios);
            }
            return $cotizacion_alquiler;
        });
//dd(collect($cotizacion_alquiler)->toArray());
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cotizacion_alquiler))->_display();
        exit();
    }

    function imprimir_cotizacion_de_alquiler($uuid) {
        if($uuid==null){
            return false;
        }

        $clause['uuid_cotizacion'] = $uuid;
        $cotizacion_alquiler = $this->CotizacionesAlquilerRepository->findBy(['empresa_id'=>$this->empresa_id,'uuid_cotizacion'=>$uuid]);

        //$history = $this->pagosRep->getLastEstadoHistory($cotizacion_alquiler->id);
        $dompdf = new Dompdf();
        $data   = ['cotizacion_alquiler'=>$cotizacion_alquiler];//, 'history'=>$history];
         $html = $this->load->view('pdf/cotizaciondealquiler', $data, true);
        //echo '<pre>'. $html . '</pre>'; die;
        //render
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($cotizacion_alquiler->codigo);

        exit();
    }
}
