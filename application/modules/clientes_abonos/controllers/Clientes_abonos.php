<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Abonos
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\ClientesAbonos\Repository\ClienteAbonoRepository;
use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Modulo\ClientesAbonos\Transaccion\TransaccionAbonoCliente;

class Clientes_abonos extends CRM_Controller
{
  private $empresa_id;
  private $id_usuario;
  private $empresaObj;
  protected $abonoGuardar;
  protected $listaCobro;
  protected $clienteAbono;

    function __construct(){
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('roles/Rol_orm');

        $this->load->model('clientes/Cliente_orm');

        $this->load->model('bancos/Bancos_orm');

        $this->load->model('facturas_compras/Facturas_compras_orm');

        $this->load->model('pagos/Pagos_orm');

        $this->load->model('Clientes_abonos_orm');
        $this->load->model('Clientes_abonos_catalogos_orm');
        $this->load->model('Clientes_abonos_metodos_abono_orm');

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
	      $this->id_usuario   = $this->session->userdata("huuid_usuario");
	      $this->empresa_id   = $this->empresaObj->id;

        $this->load->library('Repository/Clientes_abonos/Guardar_abono');
        $this->abonoGuardar = new Guardar_abono;
        //$this->listaAbono = new Lista_abono;
        $this->clienteAbono = new ClienteAbonoRepository;

    }


    public function crear($uuid_cliente = NULL){
        $acceso = 1;
        $mensaje = array();
        /*if(!$this->auth->has_permission('acceso') or !$uuid_cliente){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
        }*/

        $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
        'public/assets/css/modules/stylesheets/abonos.css',
        ));
        $this->assets->agregar_js(array(
        'public/assets/js/default/jquery-ui.min.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
        'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        'public/assets/js/plugins/jquery/combodate/combodate.js',
        'public/assets/js/plugins/jquery/combodate/momentjs.js',
        'public/assets/js/default/lodash.min.js',
        'public/assets/js/default/accounting.min.js',
        'public/assets/js/plugins/jquery/chosen.jquery.min.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
        'public/assets/js/modules/clientes_abonos/service.abono.js',
        'public/assets/js/modules/clientes_abonos/crearAbono.controller.js',
        ));

        $this->assets->agregar_var_js(array(
            "vista"             => 'crear',
            "acceso"            => $acceso == 0? $acceso : $acceso,
            "uuid_cliente"    => $uuid_cliente
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Abono: Crear ',
        );

        $this->template->agregar_titulo_header('Crear Abono');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    function ver($uuid_abono=NULL){
        $mensaje = array();
        $acceso = 1;

        $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
        'public/assets/css/modules/stylesheets/abonos.css',
        ));
        $this->assets->agregar_js(array(
        'public/assets/js/default/jquery-ui.min.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
        'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        'public/assets/js/plugins/jquery/combodate/combodate.js',
        'public/assets/js/plugins/jquery/combodate/momentjs.js',
        'public/assets/js/default/lodash.min.js',
        'public/assets/js/default/accounting.min.js',
        'public/assets/js/plugins/jquery/chosen.jquery.min.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
        'public/assets/js/modules/clientes_abonos/service.abono.js',
        'public/assets/js/modules/clientes_abonos/crearAbono.controller.js',
        ));

        $abonoObj    = new Buscar(new Clientes_abonos_orm,'uuid_abono');
        $abono       = $abonoObj->findByUuid($uuid_abono);
        $uuid_abono  = bin2hex($abono->uuid_abono);

        $cliente_id = $abono->cliente_id;
        $cliente_info = Cliente_orm::find($cliente_id);
        $cliente_uuid = $cliente_info->uuid_cliente;

        $data       = array();
        $clause     = array('empresa_id'=> $this->empresa_id);
//        $facturas   = Facturas_compras_orm::with('proveedor')->where(function($query) use($clause){
//            $query->where('empresa_id','=',$clause['empresa_id']);
//            $query->whereNotIn('estado',array('anulada'));
//        })->get();
        $this->assets->agregar_var_js(array(
            "vista"     => 'ver',
            //"acceso"    => $acceso == 0? $acceso : $acceso,
            "uuid_abono" => $uuid_abono,
            "uuid_cliente" => $cliente_uuid
        ));

            if(!empty($descuento_info[0]["tipo_descuento_id"]) && $descuento_info[0]["tipo_descuento_id"] != ""){
            	$this->assets->agregar_var_js(array(
            		"tipo_descuento_id" => $descuento_info[0]["tipo_descuento_id"]
            	));
            }


        //$data['facturas'] = $facturas->toArray();
        $data['uuid_abono']     = $abono->uuid_abono;
        //$data['proveedor_id']   = $abono->proveedor->uuid_proveedor;
        $data['mensaje']        = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Abono: '.$abono->codigo,
        );

        $this->template->agregar_titulo_header('Ver Abono');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

 function guardar()
    {

        if($_POST)
        {

            /*echo '<h2>Consultando Antes colaboradores:</h2><pre>';
            print_r($_POST);
            echo '</pre>';
            //die();
            */
            //campos para guardar el abono
            $success    = FALSE;
            $post       = $this->input->post();


          $abono = Capsule::transaction(function() use ($post, &$success){

                $success = TRUE;
                if(!isset($post['campo']["id"]))//identificador del abono
                {
                    $abono = new Clientes_abonos_orm;

                    $this->_createAbono($abono, $post);
                    $abono->save();

                    $this->_setMetodosAbonos($abono, $post);
                  //  $this->_actualizarCreditoProveedor($abono);
                }
                else
                {
                    $abono = Clientes_abonos_orm::find($post["campo"]["id"]);
                    $this->_setAbonoFromPost($abono, $post);//solo cambia el estado del abono
                    $abono->save();



                }
                return $abono;

            });

            if(!is_null($abono))
            {
              $transaccion = new Transaccion;
              $transaccion->hacerTransaccion($abono->fresh(), new TransaccionAbonoCliente);
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('clientes/listar'));
        }
    }

    private function _createAbono($abono, $post)
    {
        $total  = Clientes_abonos_orm::deEmpresa($this->empresa_id)->count();
        $year   = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('APY'.$year,$total + 1);

        $abono->codigo          = $codigo;
        $abono->empresa_id      = $this->empresa_id;
        $abono->fecha_abono     = date("Y-m-d", strtotime($post["campo"]["fecha_abono"]));
        $abono->cliente_id    = $post["campo"]["cliente"];
        $abono->monto_abonado   = $post['campo']['total_abonado'];
        $abono->cuenta_id       = $post["campo"]["cuenta_id"];
        $abono->formulario      = $post["campo"]["formulario"];
        $abono->estado          = 'aplicado';
    }

 //en la edicion de abonos solo se puede cambiar el estado
    private function _setAbonoFromPost($abono, $post)
    {
        $abono->estado   = isset($post["campo"]["estado"]) ? $post["campo"]["estado"] : 'por_aplicar';
    }



    private function _setMetodosAbonos($abono, $post)
    {
        foreach($post["metodo_abono"] as $metodo){
            $referencia   = $this->abonoGuardar->tipo_abono($metodo['tipo_abono'], $metodo);
            $item_abono    = new Clientes_abonos_metodos_abono_orm;

            $item_abono->tipo_abono     = $metodo['tipo_abono'];
            $item_abono->total_abonado  = $metodo['total_abonado'];
            $item_abono->referencia     = $referencia;

            $abono->metodo_abono()->save($item_abono);
        }
    }



  /*  private function _actualizarCreditoProveedor($abono)
    {
        $abono->cliente->credito += $abono->monto_abonado;
        $abono->cliente->save();
    } */

 function ajax_factura_info()
    {
        $uuid = $this->input->post('uuid');
        $facturaObj  = new Buscar(new Facturas_compras_orm,'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);
        $factura->proveedor;
        $factura->abonos;

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($factura->toArray()))->_display();
        exit;
    }

    //Obtiene el catalogo de facturas a las cuales se les puede
    //relizar abonos
    function ajax_facturas_abonos(){
        //$vista      = $this->input->post('vista');
        $facturas   = Facturas_compras_orm::deEmpresa($this->empresa_id)->paraAbonos();
        $resultados = array();

        foreach($facturas->get() as $factura){
            $total = $factura->total;
            $abonos = (count($factura->abonos)) ? $factura->abonos()->sum("pag_abonos_pagables.monto_pagado") : 0;
            $saldo = $total - $abonos;

            if($saldo > 0)
            {
                //echo $total."-".$abonos."=".$saldo."\n<br>";
                $resultados[]= array('uuid'=>$factura->uuid_factura,'nombre'=>$factura->codigo.' - '.$factura->proveedor->nombre);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($resultados))->_display();
        exit;
    }

    public function ajax_clientes(){
        $clientes    = Cliente_orm::deEmpresa($this->empresa_id)->orderBy("nombre", "asc");
        $resultados     = array();

        foreach($clientes->get() as $cliente){
            $resultados[] = array('uuid'=>$cliente->uuid_cliente,'nombre'=>$cliente->nombre);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($resultados))->_display();
        exit;
    }



    public function listar()
    {
        redirect(base_url('clientes/listar'));
        exit();

        $this->assets->agregar_css(array(
          'public/assets/css/default/ui/base/jquery-ui.css',
          'public/assets/css/default/ui/base/jquery-ui.theme.css',
          'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
          'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
          'public/assets/css/plugins/jquery/awesome-bootstrap-checkbox.css',
          'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
          'public/assets/css/plugins/jquery/fileinput/fileinput.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/modules/clientes_abonos/tabla.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput_locale_es.js',
            )
        );



        $datos = array();

        $this->template->agregar_titulo_header('Listado de Abonos de Clientes');
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Abonos',
        );

        $this->template->agregar_contenido($datos);
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar()
    {



        // Just Allow ajax request
        if(!$this->input->is_ajax_request()){
  	      return false;
  	    }

        $id_cliente = $this->input->post('id_cliente');


        $clienteObj = new Buscar(new Cliente_orm, 'uuid_cliente');
        $cliente = $clienteObj->findByUuid($id_cliente);

        $clause = array();

        $clause['empresa_id'] = $this->empresa_id;
        $clause['cliente_id'] = $cliente->id;


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->clienteAbono->listar_totales($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);


  	$clientes_abonos = $this->clienteAbono->listar($clause ,$sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL);


        // Constructing a JSON
        $response = new stdClass ();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;

                foreach ($clientes_abonos->toArray() AS  $row) {


                   $uuid_abono = bin2hex($row['uuid_abono']);

            $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-codigo="'. $row['codigo'] .'" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-sm hidden-xs">Opciones</span></button>';
                    $hidden_options .= '<a href="'. base_url('clientes_abonos/ver/'. $uuid_abono) .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
           	    $response->rows[$i]["id"] = $row['id'];
    		    $response->rows[$i]["cell"] = array(
                    '<a href="'. base_url('clientes_abonos/ver/'. $uuid_abono) .'" class="link" id="editarabono"  data-abonouuid="'. $uuid_abono .'">'.$row['codigo'].'</a> ',
                        date("d/m/Y", strtotime($row['fecha_abono'])),
                        '<label class="totales-success">'.number_format(Util::verificar_valor($row['monto_abonado']) ,2, '.', ',').'</label>',
                        $link_option,
                        $hidden_options
                    );
                    $i++;
                }

            echo json_encode($response);
            exit;

    }

   /* function crearsubpanel($uuid_abono = NULL)
    {

        $this->assets->agregar_var_js(array(
            "vista"             => 'crear'
        ));

        $this->template->vista_parcial(array(
            'clientes_abonos',
            'crear'
        ));
    }

    function editarsubpanel($uuid_abono = NULL)
    {
        $this->assets->agregar_var_js(array(
            "vista" => 'ver'
        ));


        $this->template->vista_parcial(array(
            'clientes_abonos',
            'ver'
        ));
    }*/


    public function ocultotabla()
    {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes_abonos/tabla.js',
        ));

        $this->load->view('tabla');
    }



    public function ajax_cliente_info(){

        $uuid   = $this->input->post('uuid');

        $clienteObj   = new Buscar(new Cliente_orm,'uuid_cliente');
        $cliente      = $clienteObj->findByUuid($uuid);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($cliente->toArray()))->_display();
        exit;
    }


    function ajax_info_abono(){
        $uuid       = $this->input->post('uuid_abono');
        $abonoinfo  = new Buscar(new Clientes_abonos_orm,'uuid_abono');
        $abonoObj   = $abonoinfo->findByUuid($uuid);

        $aux            = $this->_getAbono($abonoObj);
        $aux["items"]   = $this->_getAbonoItems($abonoObj);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($aux))->_display();
        exit;
    }

    private function _getAbono($abonoObj){
        return [
            "id"                => $abonoObj->id,
            "uuid_abono"        => bin2hex($abonoObj->uuid_abono),
            "codigo"       => $abonoObj->codigo,
            "fecha_abono" => $abonoObj->fecha_abono,
            "cliente_id"        => $abonoObj->cliente_id,
            "monto_abonado"     => $abonoObj->monto_abonado,
            "cuenta_id"    => $abonoObj->cuenta_id,
            "empresa_id"      => $abonoObj->empresa_id
        ];
    }

    private function _getAbonoItems($abonoObj){
        $aux = [];
        $lista = $abonoObj->metodo_abono;
        foreach($lista as $l){

            $aux[] = array(
                "id"  => $l->id,
                "abono_id"       => (string) $l->abono_id,
                "tipo_abono"   => $l->tipo_abono,
                "total_abonado"      => $l->total_abonado,
                "referencia"      => $l->referencia
            );
        }

        return $aux;
    }

    function ocultoformulario($facturas = array()){
        $data   = array();
        $clause = array('empresa_id'=> $this->empresa_id);

        $data['tipo_abonos']     = Clientes_abonos_catalogos_orm::where('tipo','pago')->get(array('id','etiqueta','valor'));
        $data['bancos']         = Bancos_orm::get(array('id','nombre'));
        $data['cuenta_bancos']  = Cuentas_orm::cuentasBanco($clause);
        $data['clientes']    = Cliente_orm::deEmpresa($this->empresa_id)->get(array('id','nombre', 'credito'));

        if(isset($facturas['info']))$data['info'] = $facturas['info'];

        $this->load->view('formulario', $data);
    }

}
