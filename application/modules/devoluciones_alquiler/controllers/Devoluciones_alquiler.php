<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Devoluciones de alquiler
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;

use Flexio\Modulo\DevolucionesAlquiler\Models\DevolucionesAlquilerCatalogos;

use Flexio\Modulo\DevolucionesAlquiler\Repository\DevolucionesAlquilerRepository;
use Flexio\Modulo\DevolucionesAlquiler\Repository\DevolucionesAlquilerCatalogosRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\CentroFacturable\Repository\CentroFacturableRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerRepository;
use Flexio\Modulo\EntregasAlquiler\Repository\EntregasAlquilerRepository;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;
use Flexio\Modulo\DevolucionesAlquiler\Models\DevolucionesAlquiler;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;

class Devoluciones_alquiler extends CRM_Controller
{
    /**
     * Atributos
     */
    private $empresa_id;
    private $empresaObj;
    private $id_usuario;
    protected $DevolucionesAlquilerCatalogos;
    protected $EntregasAlquilerRepository;
    protected $DevolucionesAlquilerRepository;
    protected $DevolucionesAlquilerCatalogosRepository;
    protected $ClienteRepository;
    protected $CentroFacturableRepository;
    protected $ContratosAlquilerRepository;
    protected $ItemsCategoriasRepository;
    protected $BodegasRepository;
    protected $UsuariosRepository;

    /**
     * Método constructor
     */
    public function __construct() {
        parent::__construct();
        //cargar los modelos
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('clientes/Cliente_orm');
        $this->load->model('roles/Rol_orm');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id = $this->empresaObj->id;
        $this->id_usuario = $this->session->userdata("id_usuario");

        $this->DevolucionesAlquilerRepository = new DevolucionesAlquilerRepository();
        $this->DevolucionesAlquilerCatalogosRepository = new DevolucionesAlquilerCatalogosRepository();
        $this->ClienteRepository = new ClienteRepository();
        $this->CentroFacturableRepository = new CentroFacturableRepository();
        $this->EntregasAlquilerRepository = new EntregasAlquilerRepository();
        $this->ContratosAlquilerRepository = new ContratosAlquilerRepository();
        $this->DevolucionesAlquilerCatalogos = new DevolucionesAlquilerCatalogos();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->BodegasRepository 				= new BodegasRepository();

        $this->UsuariosRepository = new UsuariosRepository();
    }

    public function ocultotabla($key_valor = NULL) {
        if($key_valor and count(explode("=", $key_valor)) > 1)
        {
            $aux = explode("=", $key_valor);
            $this->assets->agregar_var_js([$aux[0]=>$aux[1]]);
        }

        $this->assets->agregar_js(array(
            'public/assets/js/modules/devoluciones_alquiler/tabla.js'
        ));

        $this->load->view('tabla');
    }
    public function cargar_templates_vue() {
        $this->load->view('componente_tabla_entregas');
        $this->load->view('componente_tabla_series');
    }
    function ocultoformulario($cotizacion=array()) {
       $data=array();

        $clause = array('empresa_id'=> $this->empresa_id);
        $clause_precios = array('empresa_id'=>$this->empresa_id,'estado'=>1);
        $clause_impuesto = array('empresa_id'=>$this->empresa_id,'estado'=>'Activo');


        $this->load->view('formulario', $data);
    }
    /**
     * Método listar los registros de los subdevoluciones en ocultotabla()
     */
    public function ajax_listar() {
        if(!$this->input->is_ajax_request()){return false;}

        $clause                 = $this->input->post();
        $clause['empresa_id']   = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->DevolucionesAlquilerRepository->count($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $devoluciones_alquiler = $this->DevolucionesAlquilerRepository->get($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if($count > 0){
            foreach ($devoluciones_alquiler as $i => $devolucion_alquiler)
            {
                $response->rows[$i]["id"]   = $devolucion_alquiler->uuid_devolucion_alquiler;
                $response->rows[$i]["cell"] = $this->DevolucionesAlquilerRepository->getCollectionCell($devolucion_alquiler, $this->auth);
            }
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }

function crear() {


     $acceso = 1;
    $mensaje = array();

    if(!$this->auth->has_permission('acceso')){
      // No, tiene permiso, redireccionarlo.
      $acceso = 0;
      $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }
    if ($_POST) {
         $this->guardar();
    }


    $this->_css();
    $this->assets->agregar_css(array(
        'public/assets/css/modules/stylesheets/ordenes_trabajo.css',
    ));
    $this->_js();
    $this->assets->agregar_js(array(
       'public/assets/js/modules/devoluciones_alquiler/vue.tabla_entregas.js',
       'public/assets/js/modules/devoluciones_alquiler/vue.tabla_series.js',
       'public/assets/js/modules/devoluciones_alquiler/vue.crear.js'
    ));

       $this->_crear_variables_catalogos();

      $data=array();
      $clause = array('empresa_id'=> $this->empresa_id);
      //$cotizaciones = $this->cotizacionRepository->getCotizacionAbierta($clause);
      //$cotizaciones->load('cliente');
      $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso
      ));
   // $data['cotizaciones'] =   $cotizaciones->toArray();
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-car"></i> Retornos: Crear',
    );
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Crear Orden de Ventas');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }


 function editar($uuid = NULL) {
      $acceso = 1;
      $mensaje = $entregas = array();

      if(!$this->auth->has_permission('acceso')){
          // No, tiene permiso, redireccionarlo.
          $acceso = 0;
          $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
      }
      if ($_POST) {
          $this->guardar();
      }

      $this->_css();
      $this->assets->agregar_css(array(
          'public/assets/css/modules/stylesheets/ordenes_trabajo.css',
      ));
      $this->_js();
      $this->assets->agregar_js(array(
          'public/assets/js/modules/devoluciones_alquiler/vue.tabla_entregas.js',
          'public/assets/js/modules/devoluciones_alquiler/vue.tabla_series.js',
          'public/assets/js/modules/devoluciones_alquiler/vue.crear.js'
      ));



      $data=array();



      $devolucion_alquiler = $this->DevolucionesAlquilerRepository->findBy(['uuid_devolucion_alquiler'=>$uuid]);
      $devolucion_alquiler->load('comentario_timeline');
    //  $devolucion_alquiler->load("entregas","contratos");
     // $devolucion_alquiler->load("contratos_items_detalles_devoluciones");
       $this->_crear_variables_catalogos($devolucion_alquiler->tipo_contrato);
        //if( $devolucion_alquiler->entregas[0]->id > 0 ){
          //$devolucion_alquiler->load();
          /*$entregas_alquiler = EntregasAlquiler::where("empresa_id","=", $this->empresa_id)->where("estado_id","=", 4)->get(array('id','codigo'));
          $items =$entregas_alquiler->toArray();*/


          /*$objeto = ContratosAlquiler::where("id","=", $contrato_id)->get();
          $objeto->load('cliente', 'items', 'contratos_items','contratos_items.item','contratos_items.item.seriales')->first();
          $objeto[0]->toArray();*/

       //}
       $this->assets->agregar_var_js(array(
          "vista" => 'editar',
          "acceso" => $acceso,
          "devolucion_alquiler"=> json_encode($this->DevolucionesAlquilerRepository->getCollectionCampo($devolucion_alquiler)),
          "devolucion_items"=> ($devolucion_alquiler->tipo_contrato==1)?$devolucion_alquiler->itemscontratos:$devolucion_alquiler->items,
           "coment" =>(isset($devolucion_alquiler->comentario_timeline)) ? $devolucion_alquiler->comentario_timeline : "",
           "devoluciones_id"=> $devolucion_alquiler->id
        ));
       $breadcrumb = array(
          "titulo" => '<i class="fa fa-car"></i> Retornos: '.$devolucion_alquiler->codigo,
      );
      $data['mensaje'] = $mensaje;
      $this->template->agregar_titulo_header('Crear Orden de Ventas');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();
  }
  function guardar() {
      $request = Illuminate\Http\Request::createFromGlobals();
      $post = $this->input->post();

       if (!empty($post)) {
            Capsule::beginTransaction();
          try {
              $campo = $request->input('campo');
              if(empty($campo['id'])) //Nuevo
              {
                  $post['campo']['codigo']        = $this->_generar_codigo();
                  $post['campo']['empresa_id']    = $this->empresa_id;

                   $retorno = $this->DevolucionesAlquilerRepository->create($post);
              } else { //Edicion

                  $retorno = $this->DevolucionesAlquilerRepository->save($post);
              }

          } catch (Illuminate\Database\QueryException $e) {
              log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
              Capsule::rollback();
              $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
              $this->session->set_flashdata('mensaje', $mensaje);
              redirect(base_url('devoluciones_alquiler/listar'));
              //echo $e->getMessage();
          }
          Capsule::commit();

          if (!is_null($retorno)) {
              $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $retorno->codigo);
          } else {
              $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
          }
          $this->session->set_flashdata('mensaje', $mensaje);
          redirect(base_url('devoluciones_alquiler/listar'));
      }
   }


  /**
   * Crea catalogos en variables js
   */
  private function _crear_variables_catalogos($tipo_contrato = NULL) {

          $clause = array('empresa_id' => $this->empresa_id);
          $clause_precios = array_merge($clause, ["estado" => 1]);
          $clause_impuesto = array_merge($clause, ["estado" => "Activo"]);

          //-------------------------
          // Catalogo Clientes
          //-------------------------
          $clientes = $this->ClienteRepository->getAll(array("empresa_id" => $this->empresa_id))->toArray();
          $clientes = (!empty($clientes) ? array_map(function($clientes) {
              return array(
                  "id" => $clientes["id"],
                  "nombre" => $clientes["nombre"],
                  "saldo_pendiente" => $clientes["saldo_pendiente"],
                  "credito_favor" => $clientes["credito_favor"]
              );
          }, $clientes) : "");



          //---------------------
          // Catalogo Vendedores
          //---------------------
          $roles_users = Rol_orm::where(function ($query) use ($clause) {
                  $query->where('empresa_id', '=', $clause['empresa_id']);
                  $query->where('nombre', 'like', '%vendedor%');
          })->orWhere(function ($query) use ($clause) {
                 $query->where('empresa_id', '=', $clause['empresa_id']);
                 $query->where('nombre', 'like', '%venta%');
          })->with(array('usuarios'))->get();



              $recibidos = Usuario_orm::where("id","=",$this->id_usuario)->get(array("id","nombre","apellido"));

              $usuarios = array();
              $vendedores = array();
              foreach ($roles_users as $roles) {
                  $usuarios = $roles->usuarios;
                  foreach ($usuarios as $user) {
                      if ($user->pivot->empresa_id == $clause['empresa_id']) {
                          array_push($vendedores, array(
                              "id" => $user->id,
                              "nombre" => Util::verificar_valor($user->nombre) ." ". Util::verificar_valor($user->apellido)
                          ));
                      }
                  }
              }

              $estados = DevolucionesAlquilerCatalogos::where("tipo","=","estado")->get(array("id","nombre"));


              $categorias = $this->ItemsCategoriasRepository->get(['empresa_id'=>$this->empresa_id,'conItems'=>true]);
              $categorias->load('items_contratos_alquiler');

                //---------------------
                //Listado de Bodegas
                //---------------------
            		$bodegas = $this->BodegasRepository->getAll(array("empresa_id" => $this->empresa_id))->toArray();
            		$bodegas = (!empty($bodegas) ? array_map(function($bodegas) {
            			return array(
            				"id" => $bodegas["id"],
            				"nombre" => $bodegas["nombre"]
            			);
            		}, $bodegas) : "");

  /* $entregas_alquiler = EntregasAlquiler::where("empresa_id","=", $this->empresa_id)->where("estado_id","=", 4)->get();
       $entregas_alquiler = $entregas_alquiler->each(function ($item, $key) {
           $item->load('cliente');
           $item->cliente_nombre = $item->cliente->nombre;
           return $item;
       });*/

            		    $entregas = EntregasAlquiler::where("entregable_type","=","Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler")->get(array('id','codigo'));

            		   if($tipo_contrato == 1){
            		          $empezables = ContratosAlquiler::where("empresa_id","=", $this->empresa_id)->where("estado_id","=", 2)->get();
            		          $empezables = $empezables->each(function ($item, $key) {
            		              $item->load('cliente');
            		              $item->cliente_nombre = $item->cliente->nombre;
            		              return $item;
            		          });

            		   }else{
            		       $empezables = EntregasAlquiler::where("empresa_id","=", $this->empresa_id)->where("estado_id","=", 4)->get(array('id','codigo','cliente_id'));
            		       $empezables = $empezables->each(function ($item, $key) {
            		           $item->load('cliente');
            		           $item->cliente_nombre = $item->cliente->nombre;
            		           return $item;
            		       });
            		   }


                             $this->assets->agregar_var_js(array(
                              "clientesArray" => json_encode($clientes),
                              "usuario_id" =>  $this->id_usuario,
                              "recibidosArray" => json_encode($recibidos),
                              "vendedoresArray" => collect($vendedores),
                              //"vendedoresArray" => $this->UsuariosRepository->get(array('empresa_id' => $this->empresa_id)),
                              "estadosArray" => json_encode($estados),
                              "entregasArray" => json_encode($entregas),
                              "categoriasArray" => collect($categorias),
                              "bodegasArray" => json_encode($bodegas),
                              "empezables" => json_encode($empezables),
                              "acceso" => 1
                          ));
  }

    public function listar() {

        $data = array();
        $mensaje ='';
        if(!$this->auth->has_permission('acceso'))
        {
            redirect ( '/' );
        }
        if(!empty($this->session->flashdata('mensaje')))
        {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }

        $this->_css();$this->_js();

        $breadcrumb = array( "titulo" => '<i class="fa fa-car"></i> Alquileres: Retornos',
            "ruta" => array(
                0 => ["nombre" => "Alquileres", "activo" => false],
                1 => ["nombre" => '<b>Retornos</b>', "activo" => true]
            ),
            "menu" => ["nombre" => "Crear", "url" => "devoluciones_alquiler/crear","opciones" => array()]
        );
        $breadcrumb["menu"]["opciones"]["#exportarDevolucionesAlquiler"] = "Exportar";

        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        $clause = ['empresa_id' => $this->empresa_id];
        $data['clientes']   = $this->ClienteRepository->get($clause);
        $data['estados']    = $this->DevolucionesAlquilerCatalogosRepository->get(['tipo'=>'estado']);
        $data['centros_facturables'] = $this->CentroFacturableRepository->get(['empresa_id'=>$this->empresa_id]);
        $this->template->agregar_titulo_header('Alquileres: Retornos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

    public function ajax_exportar() {
        $post = $this->input->post();
    	if(empty($post)){exit();}

    	$devoluciones_alquiler = $this->DevolucionesAlquilerRepository->get(['ids', $post['ids']]);

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(['No. Retorno','Fecha de retorno','No. Contrato','Cliente',utf8_decode('Centro de Facturación'),'Estado']);
        $csv->insertAll($this->DevolucionesAlquilerRepository->getCollectionExportar($devoluciones_alquiler));
        $csv->output("DevolucionesAlquiler-". date('ymd') .".csv");
        exit();
    }



    private function _generar_codigo() {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->DevolucionesAlquilerRepository->count($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('RT'.$year,$total + 1);
        return $codigo;
    }

    public function ajax_seleccionar_items_entrega() {

       $entregas_alquiler = EntregasAlquiler::where("empresa_id","=", $this->empresa_id)->where("estado_id","=", 4)->get();
       $entregas_alquiler = $entregas_alquiler->each(function ($item, $key) {
           $item->load('cliente');
           $item->cliente_nombre = $item->cliente->nombre;
           return $item;
       });
        $items =$entregas_alquiler->toArray();
        $response = new stdClass();
        $response->items = $items;
        echo json_encode($response);
        exit;
    }

    public function ajax_seleccionar_items_contrato() {
       $contrato_alquiler = ContratosAlquiler::where("empresa_id","=", $this->empresa_id)->where("estado_id","=", 2)->get();
       $contrato_alquiler->load('contratos_items','entregas','cliente');

       $contrato_alquiler = $contrato_alquiler->filter(function ($validas, $key) {
            if(count($validas->entregas)>0){
                $validas->cliente_nombre = $validas->cliente->nombre;
                return $validas;
           }
       });

       $items =$contrato_alquiler->toArray();
       $response = new stdClass();
       $response->items = $items;
       echo json_encode($response);
       exit;
    }

    function ajax_seleccionar_info() {

        $id      = $this->input->post('id');
        $tipo   = $this->input->post('tipo');
        $response           = new stdClass();
        if($tipo == 'Contrato de alquiler'){

            $objeto = ContratosAlquiler::where("id","=", $id)->get();
            $objeto->load('entregas','cliente', 'items', 'contratos_items','contratos_items.item','contratos_items.item.seriales')->first();
            $response = $objeto[0]->toArray();
        }
        if($tipo == 'entrega'){

            $entrega_alquiler = $this->EntregasAlquilerRepository->find(['id'=>$id])->first();
            $objeto = $this->EntregasAlquilerRepository->findBy(['uuid_entrega_alquiler'=>$entrega_alquiler->uuid_entrega_alquiler]);
            $objeto = $objeto->items->toArray();

            $info = EntregasAlquiler::where("id","=", $id)->get(array('entregable_id'));
            $contrato_id = $info[0]->entregable_id;//NUmer del contrato

            $objeto_contrato = ContratosAlquiler::where("id","=", $contrato_id)->get();
            $objeto_contrato->load('cliente')->first();
            $objeto_contrato[0]->toArray();
            $response->items    = $objeto;
            $response->contrato     =$objeto_contrato;
        }
         $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    private function _css() {
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
            //'public/assets/css/modules/stylesheets/devoluciones_alquiler.css',
        ));
    }

    /* $this->assets->agregar_css(array(
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
            'public/assets/css/modules/stylesheets/contratos_alquiler.css',
        ));*/



    private function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/vue.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/modules/devoluciones_alquiler/plugins.js',
            'public/assets/js/default/vue-resource.min.js'
        ));
    }

    //Viene de Contratos

    private function _js3() {
         $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/vue.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue-resource.min.js',

         ));
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/devoluciones_alquiler/vue.comentario.js',
            'public/assets/js/modules/devoluciones_alquiler/formulario_comentario.js'
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');

    }

    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->id_usuario];
        $devoluciones = $this->DevolucionesAlquilerRepository->agregarComentario($model_id, $comentario);
        $devoluciones->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($devoluciones->comentario_timeline->toArray()))->_display();
        exit;
    }

}
