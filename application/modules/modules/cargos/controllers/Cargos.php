<?php

/**
 * Cargos
 *
 * Modulo para administrar cargos de contratos de alquiler
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  01/09/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\ContratosAlquiler\Repository\CargosRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerRepository;

class Cargos extends CRM_Controller {

    protected $empresa;
    protected $empresa_id;
    protected $usuario_id;
    protected $CargosRepository;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("centros/Centros_orm");
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('contabilidad/Impuestos_orm');

        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->usuario_id = $usuario->id;

         //$this->transaccionCaja    = new TransaccionCaja();
        //Obtener el empresa_id de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;

        $this->CargosRepository = new CargosRepository();
    }

    public function listar() {
        $data = array();

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css'
        ));

        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/modules/cargos/listar.js',
        ));

        //Breadcrum Array
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Cargos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Alquileres",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Cargos</b>',
                    "activo" => true
                )
            ),
            "menu" => array()
        );

        //defino mi mensaje
          if(!is_null($this->session->flashdata('mensaje'))){
          $mensaje = json_encode($this->session->flashdata('mensaje'));
          }else{
          $mensaje = '';
          }
          $this->assets->agregar_var_js(array(
          "toast_mensaje" => $mensaje
          ));

        $breadcrumb["menu"]["nombre"] = "Accion";
        $breadcrumb["menu"]["url"] = "#";

        //Verificar si tiene permiso de Exportar
        $breadcrumb["menu"]["opciones"]["#cambiarEstadoGrupal"] = "Cambiar Estado";
        $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";

        $this->template->agregar_titulo_header('Listado de Cargos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar($grid = NULL) {
        $clause = array(
            "empresa_id" => $this->empresa_id
        );
        $numero       = $this->input->post('numero', true);
        $item         = $this->input->post('item', true);
        $fecha_desde  = $this->input->post('fecha_desde', true);
        $fecha_hasta  = $this->input->post('fecha_hasta', true);
        $contrato     = $this->input->post('contrato', true);
        $periodo      = $this->input->post('periodo', true);
        $estado       = $this->input->post('estado', true);

        if (!empty($numero)) {
            $clause["numero"] = array('LIKE', "%$numero%");
        }
        if (!empty($item)) {
            $clause["item"] = $item;
        }
        if (!empty($contrato)) {
            $clause["contrato"] = $contrato;
        }
        if (!empty($periodo)) {
            $clause["ciclo"] = array('LIKE', "%$periodo%");
        }
        if (!empty($estado)) {
            $clause["estado"] = $estado;
        }
        if( !empty($fecha_desde)){
      		$fecha_desde = str_replace('/', '-', $fecha_desde);
      		$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_desde));
      		$clause["fecha_cargo"] = array('>=', $fecha_inicio);
      	}
      	if( !empty($fecha_hasta)){
      		$fecha_hasta = str_replace('/', '-', $fecha_hasta);
      		$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_hasta));
      		$clause["fecha_cargo@"] = array('<=', $fecha_fin);
      	}

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->CargosRepository->listar($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = $this->CargosRepository->listar($clause, $sidx, $sord, $limit, $start);

        //dd($rows->toArray());

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {
              $hidden_options = "";
              $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success porFacturar" data-id="'. $row["id"] .'">Por facturar</a>';
              $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success facturado" data-id="'. $row["id"] .'">Facturado</a>';
              $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success anulado" data-id="'. $row["id"] .'">Anulado</a>';

              if($row["estado"] == "por_facturar"){
                $status =  '<div class="col-lg-12" style="background-color: #E59057; color:#FFF; font-weight:bold;"><a href="#" data-id="'. $row["id"] .'" class="cambiarEstado" style="color:#FFF;">'.ucFirst(str_replace("_", " ", Util::verificar_valor($row["estado"]))).'</a></div>';
                $monto_cargo = '<div class="col-lg-12" style="border:2px solid #E59057; color:#E59057; font-weight:bold;">'.Util::verificar_valor($row["total_cargo"]).'</div>';
              }
              if($row["estado"] == "facturado"){
                $status =  '<div class="col-lg-12" style="background-color: #5CB85C; color:#FFF; font-weight:bold;"><a href="#" data-id="'. $row["id"] .'" class="cambiarEstado" style="color:#FFF;">'.ucFirst(str_replace("_", " ", Util::verificar_valor($row["estado"]))).'</a></div>';
                  $monto_cargo = '<div class="col-lg-12" style="border:2px solid #E59057; color:#E59057; font-weight:bold;">'.Util::verificar_valor($row["total_cargo"]).'</div>';
              }
              if($row["estado"] == "anulado"){
                $status =  '<div class="col-lg-12" style="background-color: rgb(0, 0, 0); color:#FFF; font-weight:bold;"><a href="#" data-id="'. $row["id"] .'" class="cambiarEstado" style="color:#FFF;">'.ucFirst(str_replace("_", " ", Util::verificar_valor($row["estado"]))).'</a></div>';
                  $monto_cargo = '<div class="col-lg-12" style="border:2px solid rgb(0, 0, 0); color:rgb(0, 0, 0); font-weight:bold;">'.Util::verificar_valor($row["total_cargo"]).'</div>';
              }

                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    Util::verificar_valor($row["numero"]),
                    '<a href="'. Util::verificar_valor($row["entregas_alquiler"]["contrato_alquiler"]["enlace"]) .'" style="color:blue;">'.Util::verificar_valor($row['item']['nombre'])."</a>",
                    $row["fecha_cargo"] !="" ? Carbon::parse(Util::verificar_valor($row["fecha_cargo"]))->format('d/m/Y') : "",
                    Util::verificar_valor($row["cantidad"]),
                    '<a href="'. Util::verificar_valor($row["entregas_alquiler"]["contrato_alquiler"]["enlace"]) .'" style="color:blue;">'.Util::verificar_valor($row["entregas_alquiler"]["contrato_alquiler"]["codigo"])."</a>",
                    '<div class="col-lg-12" style="border:2px solid #69ABD3; color:#69ABD3; font-weight:bold;">'.Util::verificar_valor($row["tarifa"]).'</div>',
                    ucFirst(str_replace("_", " ", Util::verificar_valor($row["ciclo"]))),
                    $monto_cargo,
                    $status,
                    $hidden_options
                );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/cargos/tabla.js'
        ));

        $this->load->view('tabla');
    }

    public function ajax_get_cargos() {
      if(!empty($_POST['uuid'])){
        $uuid = $this->input->post('uuid');
        $contratos = ContratosAlquilerRepository::findByUuid($uuid);
        $contrato_id = $contratos->id;
        $vista = "editar";
      }else{
        $contrato_id = $this->input->post('contrato_id');
        $vista = $this->input->post('vista');
      }


      $filtrar_todo_estado = !empty($vista) && preg_match("/editar/i", $vista) ? $vista : NULL;

      if (!$this->input->is_ajax_request() && empty($contrato_id)) {
          return false;
      }

      $clause = array(
        'contrato_id' => $contrato_id,
        'empresa_id'  => $this->empresa_id
      );
      $response = $this->CargosRepository->getCargosDeContratoPorfacturar($clause, false, $filtrar_todo_estado);


  		echo json_encode(collect($response)->toArray());
  		exit;
    }

    public function cambiar_estado_grupal()
    {
        if(empty($_POST)){
    		die();
    	}

        $ids = $this->input->post('ids', true);
		$id = explode(",", $ids);
        $estado = $this->input->post('estado', true);
        $registros = $this->CargosRepository->find($id);

        $response = array();
        $response["success"] = false;

        if(count($registros))
        {
            $response = $this->CargosRepository->cambiarEstado($registros, $estado);
        }

        echo json_encode($response);
        exit();
    }

    public function ajax_cambiar_estado() {

    	if($this->input->is_ajax_request())
        {
            $id = $this->input->post("id", true);//array or integer
            $estado = $this->input->post("estado", true);
            $registro = $this->CargosRepository->find($id);

            $response               = array();
            $response["success"]    = false;

            if(count($registro))
            {
                $aux = !is_array($id) ? [$registro] : $registro;
                $response = $this->CargosRepository->cambiarEstado($aux, $estado);
            }

            echo json_encode($response);
            exit();
        }

    }
}
