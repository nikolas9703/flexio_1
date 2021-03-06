<?php

/**
 * Cajas
 *
 * Modulo para administrar la creacion, edicion de cajas
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Repository\ConfiguracionContabilidad\CajaMenudaRepository as CajaMenuda;
use Flexio\Modulo\Cajas\Repository\CajasRepository as CajasRepository;
use Flexio\Modulo\Cajas\Repository\TransferirCajaRepository as TransferirCajaRepository;
use Flexio\Modulo\Cajas\Repository\CajasCatalogoRepository as CajasCatalogoRepository;
use Flexio\Modulo\Cajas\Events\ActualizarCajaSaldoEvent as ActualizarCajaSaldoEvent;
use Flexio\Modulo\Cajas\Listeners\ActualizarCajaListener as ActualizarCajaListener;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository as CuentasRepository;
use Flexio\Modulo\Cajas\Transacciones\CajasTransacciones as TransaccionCaja;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaBancoRepository as CuentaBancoRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Catalogos\Repository\CatalogoRepository;

//utils
use Flexio\Library\Util\FlexioSession;

class Cajas extends CRM_Controller {

    protected $empresa;
    protected $empresa_id;
    protected $usuario_id;
    protected $caja_menuda;
    protected $caja;
    protected $configuracionCajaMenuda;
    protected $CuentasRepository;
    protected $TransferirCajaRepository;
    protected $CajasCatalogoRepository;
    protected $disparador;
    protected $TransaccionCaja;
    protected $CuentaBancoRepository;
    protected $CentrosContablesRepository;
    protected $modulo_padre;
    protected $CatalogoRepository;
    //utils
    protected $FlexioSession;

     public function __construct()
    {
        parent::__construct();
        $this->load->model("centros/Centros_orm");
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('pagos/Pago_catalogos_orm');
        $this->load->module(array("documentos"));
        $this->load->model('pagos/Pagos_orm');
        $this->load->model('pagos/Pago_metodos_pago_orm');
        $this->load->library('Repository/Pagos/Guardar_pago');
        $this->pagoGuardar = new Guardar_pago;
        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->usuario_id = $usuario->id;

        $this->transaccionCaja    = new TransaccionCaja();
        //Obtener el empresa_id de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;

        $this->caja_menuda = new CajaMenuda();
        $this->out = new AsientoContable();
        $this->caja = new CajasRepository();

        $this->CuentasRepository = new CuentasRepository();

        $this->CuentaBancoRepository = new CuentaBancoRepository();

        $this->TransferirCajaRepository = new TransferirCajaRepository();

        $this->CajasCatalogoRepository = new CajasCatalogoRepository();
        $this->CatalogoRepository = new CatalogoRepository();

        $this->CentrosContablesRepository = new CentrosContablesRepository;

        //utils
        $this->FlexioSession = new FlexioSession;

        $this->disparador = new \Illuminate\Events\Dispatcher();
        $this->disparador->listen([ActualizarCajaSaldoEvent::class], ActualizarCajaListener::class);

        //----------------------------------------
        // Verificar si el plan contable para
        // Caja Menuda esta configurado
        $empresa = [
            'empresa_id' => $this->empresa_id
        ];
        $this->configuracionCajaMenuda = $this->caja_menuda->getAll($empresa)->toArray();
        $this->setPadreModulo();
    }
    function setPadreModulo(){
       $request = Illuminate\Http\Request::capture();
    //   echo '<pre>'; print_r($request); echo '</pre>';
        if($request->has('ventas')){

       return  $this->modulo_padre = 'ventas';
       }
       /*if($request->has('contabilidad')){

      return  $this->modulo_padre = 'contabilidad';
    }*/

       if($request->has('subcontrato')){
         return  $this->modulo_padre = 'compras';
       }


       return $this->modulo_padre = $this->session->userdata('modulo_padre');
    }
    public function listar() {
        $data = array();

        $usuarios = Usuario_orm::where("estado", "Activo")->get();

        $clause = ['empresa_id' => $this->empresa_id, 'transaccionales' => true];
        $data["centros"] = $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause));

        $data["usuarios"] = $usuarios;

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/toastr.min.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
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
            'public/assets/js/default/subir_documento_modulo.js',
            'public/assets/js/modules/cajas/listar.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
        ));

        //------------------------------------------
        // Para mensaje de creacion satisfactoria
        //------------------------------------------
        $mensaje = !empty($this->session->flashdata('mensaje')) ? json_encode(array('estado' => 200, 'mensaje' => $this->session->flashdata('mensaje'))) : '';
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        //Breadcrum Array
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Cajas',
            "ruta" => array(
                0 => array(
                    "nombre" => ucfirst($this->modulo_padre),
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Cajas</b>',
                    "activo" => true
                )
            ),
            "menu" => array()
        );

        $breadcrumb["menu"]["nombre"] = "Crear";
        $breadcrumb["menu"]["url"] = "cajas/crear/";

        //Verificar si tiene permiso de Exportar
        $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";

        $this->template->agregar_titulo_header('Listado de Cajas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar($grid = NULL) {
        $clause = array(
            "empresa_id" => $this->empresa_id
        );
        $nombre = $this->input->post('nombre', true);
        $centro_id = $this->input->post('centro_id', true);
        $limite = $this->input->post('limite', true);
        $responsable_id = $this->input->post('responsable_id', true);

        //filtros de centros contables del usuario
        $centros = $this->FlexioSession->usuarioCentrosContables();
        if(!in_array('todos', $centros))
        {
            $clause['centros_contables'] = $centros;
        }

        if (!empty($nombre)) {
            $clause["nombre"] = array('LIKE', "%$nombre%");
        }
        if (!empty($centro_id)) {
            $clause["centro_id"] = $centro_id;
        }
        if (!empty($limite)) {
            $clause["limite"] = array('LIKE', "%$limite%");
        }
        if (!empty($responsable_id)) {
            $clause["responsable_id"] = $responsable_id;
        }



        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->caja->listar($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = $this->caja->listar($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {

                $uuid_caja = $row['uuid_caja'];

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="' . base_url("cajas/crear/$uuid_caja") . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
                $hidden_options .= '<a href="' . base_url("cajas/transferir/$uuid_caja") . '" class="btn btn-block btn-outline btn-success m-t-xs" data-id="' . $row['id'] . '">Transferir a caja</a>';
                $hidden_options .= '<a href="' . base_url("cajas/transferir-desde-caja/$uuid_caja") . '" class="btn btn-block btn-outline btn-success m-t-xs" data-id="' . $row['id'] . '">Transferir desde caja</a>';
                $hidden_options .= '<a href="#" data-id="'.$row['id'].'" class="btn btn-block btn-outline btn-success subirDocumento">Subir documento</a>';

                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    '<a href="' . base_url("cajas/crear/$uuid_caja") . '" style="color:blue;">' . Util::verificar_valor($row['numero']) . '</a>',
                    Util::verificar_valor($row['nombre']),
                    Util::verificar_valor($row["centro"]["nombre"]),
                    Util::verificar_valor($row["responsable"]["nombre_completo"]),
                    Util::verificar_valor(number_format($row["limite"], 2)),
                    Util::verificar_valor(number_format($row["saldo"], 2)),
                    $link_option,
                    $hidden_options,
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
            'public/assets/js/modules/cajas/tabla.js'
        ));

        $this->load->view('tabla');
    }

     public function ocultoformulariodesdecaja($caja_uuid = NULL) {

     	$caja_info = $this->caja->findByUuid($caja_uuid);

        $this->assets->agregar_js(array(
            'public/assets/js/modules/cajas/components/detalle.js',
            'public/assets/js/default/vue/components/empezar_desde.js',
            'public/resources/assets/js/modulos/cajas/formulario.js',

    	));
        $cuentas = [];
      	$tipo_pagos = Pago_catalogos_orm::where('tipo','pago')->whereIn("etiqueta", array("al_contado", "cheque"))->get(array('id','etiqueta','valor'));

        $clause_caja = array(
            "empresa_id" => $this->empresa_id
        );

        $cuenta_banco = [];
        $empresa = ['empresa_id' => $this->empresa_id];
 	if($this->CuentaBancoRepository->tieneCuenta($empresa)) {
              $cuenta_banco = $this->CuentaBancoRepository->getAll($empresa);
              $cuenta_banco->load('cuenta');
 	}

        $cuentas  = $cuenta_banco->each(function ($cuentas, $key) {
                    $cuentas->id = $cuentas->cuenta->id;
                    $cuentas->nombre = $cuentas->cuenta->nombre;
         });
         $cajas = $this->caja->getCollectionCajas( $this->caja->get($clause_caja) );

         $cajas = $cajas->filter(function ($item) use ($caja_info)  {
            if ($item['id'] != $caja_info->id) {
                return $item;
            }
        });
          $this->assets->agregar_var_js(array(
                'nombre_caja_desde' => $caja_info['numero'].'-'.$caja_info['nombre'],
                //'cajas' => $this->caja->getCollectionCajas( $this->caja->get($clause_caja) ),
                'cajas' => $cajas,
                'bancos' => $cuentas,
     		"maximo_transferir" => $caja_info->saldo,
    		"metodos_pagos" => $tipo_pagos


         ));
         $this->load->view('formulario_desde_caja');
         $this->load->view('vue/components/empezar_desde');
         $this->load->view('components/detalle');
     }
    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/cajas/formulario.js'
        ));

        $this->load->view('formulario', $data);
    }

    function crear($caja_uuid = NULL) {
         if (!empty($_POST)) {
            $this->gardar_caja();
        }

        $data = array();
        $mensaje = !empty($this->session->flashdata('mensaje')) ? json_encode(array('estado' => 200, 'mensaje' => $this->session->flashdata('mensaje'))) : '';
        //----------------------------------------
        // Seleccionar Listado de Usuarios
        $clause = array(
        	"empresa_id" => $this->empresa_id,
            'transaccionales' => true,
        	"estado" => "Activo"
        );
        $usuarios = collect(Capsule::table('usuarios')->join('usuarios_has_empresas',function($join) use($clause){
        	$join->on('usuarios.id','=','usuarios_has_empresas.usuario_id')->where('usuarios_has_empresas.empresa_id','=', $clause['empresa_id']);
        })->where("usuarios.estado", $clause['estado'])->get(array('usuarios.id', Capsule::raw("CONCAT_WS(' ', IF(usuarios.nombre != '', usuarios.nombre, ''), IF(usuarios.apellido != '', usuarios.apellido, '')) AS nombre_completo"))))->toArray();

        //----------------------------------------
        // Seleccionar Listado de Centros
        $cat_centros = Capsule::select(Capsule::raw("SELECT * FROM cen_centros WHERE empresa_id = :empresa_id1 AND estado='Activo' AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2 AND estado='Activo') ORDER BY nombre ASC"), array(
			'empresa_id1' => $this->empresa_id,
			'empresa_id2' => $this->empresa_id
        ));
        $cat_centros = (!empty($cat_centros) ? array_map(function($cat_centros) {
                            return array("id" => $cat_centros->id, "nombre" => $cat_centros->nombre);
                        }, $cat_centros) : "");

        $estados = $this->CajasCatalogoRepository->getEstados()->toArray();
        $estados = (!empty($estados) ? array_map(function($estados) {
                            return array("id" => $estados["id_cat"], "nombre" => $estados["etiqueta"]);
                        }, $estados) : "");
         //----------------------------------------
        // Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "centroContableList" =>json_encode($this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause))),
            //"centroContableList" => json_encode($cat_centros),
            "usuariosList" => json_encode($usuarios),
            "configurado" => json_encode($this->configuracionCajaMenuda),
            "estadosList" => json_encode($estados),
            "toast_mensaje" => $mensaje
        ));


        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Cajas: Crear'
        );
        //------------------------------------------
        // Para mensaje de creacion satisfactoria
        //------------------------------------------


        //----------------------------------------
        // Si existe uuid de caja
        if ($caja_uuid != NULL) {
            $caja = $this->caja->findByUuid($caja_uuid);
            $caja->load('comentario_timeline', 'cajas_asignadas');
            $caja_info = $this->caja->findByUuid($caja_uuid);
            $data["caja_uuid"] = $caja_uuid;
            $data["caja_id"] = $caja_info->id;


            $this->assets->agregar_css(array(
                'public/assets/css/default/ui/base/jquery-ui.css',
                'public/assets/css/default/ui/base/jquery-ui.theme.css',
                'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
                'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
                'public/assets/css/modules/stylesheets/cobros.css',
                'public/assets/css/modules/stylesheets/animacion.css',
                'public/assets/css/plugins/jquery/toastr.min.css',
				
            ));
            $this->assets->agregar_js(array(
                'public/assets/js/default/jquery-ui.min.js',
                'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
                'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
                'public/assets/js/plugins/jquery/jquery.sticky.js',
                'public/assets/js/default/jquery.inputmask.bundle.min.js',
                'public/assets/js/default/toast.controller.js',
				
            ));

            $this->assets->agregar_var_js(array(
                "caja_uuid" => $caja_uuid,
                "nombrev" => $caja_info->nombre,
                "limitev" => $caja_info->limite,
                "centro_idv" => $caja_info->centro_id,
                "saldov" => $caja_info->saldo,
                "estado_idv" => $caja_info->estado_id,
                "maxportransferir" => number_format(($caja_info->limite - $caja_info->saldo), 2, '.', ''),
                "responsable_idv" => $caja_info->responsable_id,
                "coment" =>(isset($caja->comentario_timeline)) ? $caja->comentario_timeline : "",
                "caja_id"    => $caja->id,
                'vista' => 'ver',
                "cuenta_id" => $caja_info->cuenta_id,
                "toast_mensaje" => $mensaje
            ));

            $breadcrumb = array(
         				"titulo" => '<i class="fa fa-shopping-cart"></i> Caja: ' . $caja_info->numero . ' - ' . $caja_info->nombre,
         				"filtro" => false,
                "ruta" => array(
                  0 => array(
                      "nombre" =>  ucfirst($this->modulo_padre),
                      "activo" => false,
                  ),
                    1 => array(
                        "nombre" => "Cajas",
                        "activo" => false,
                        "url" => 'cajas/listar'
                    ),
                    2=> array(
                        "nombre" => '<b>Detalle</b>',
                        "activo" => true
                    )
                ),
         		);
        } //Termina detalle
        else{
          $breadcrumb = array(
              "titulo" => '<i class="fa fa-shopping-cart"></i> Cajas: Crear',
              "filtro" => false,
              "ruta" => array(
                0 => array(
                      "nombre" =>  ucfirst($this->modulo_padre),
                    "activo" => false,
                ),
                  1 => array(
                      "nombre" => "Cajas",
                      "activo" => false,
                      "url" => 'cajas/listar'
                  ),
                  2=> array(
                      "nombre" => '<b>Crear</b>',
                      "activo" => true
                  )
              ),
          );
        }



        $this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/modules/stylesheets/facturas_compras.css',
			
        ));

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/modules/cajas/formulario.controller.js',
            'public/assets/js/default/toast.controller.js',
			'public/assets/js/modules/cajas/plugins.js',
        ));

        $this->template->agregar_titulo_header('Cajas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_guardar_caja() {
        $id = $this->input->post('id', TRUE);
        $nombre = $this->input->post('nombre', TRUE);
        $centro_contable_id = $this->input->post('centro_contable_id', TRUE);
        $responsable_id = $this->input->post('responsable_id', TRUE);
        $limite = $this->input->post('limite', TRUE);
        $cuenta_id = !empty($this->configuracionCajaMenuda[0]["cuenta_id"]) ? $this->configuracionCajaMenuda[0]["cuenta_id"] : "";

        $fieldset = [
            'caja_id' => $id,
            'empresa_id' => $this->empresa_id,
            'centro_id' => $centro_contable_id,
            'responsable_id' => $responsable_id,
            'cuenta_id' => $cuenta_id,
            'creado_por' => $this->usuario_id,
            'nombre' => $nombre,
            'numero' => Capsule::raw("NO_CAJA('CJ', " . $this->empresa_id . ")"),
            'limite' => $limite
        ];
        //$caja = $this->caja;
        $response = Capsule::transaction(function() use($fieldset, $id) {
                    try {

                        $this->caja->create($fieldset);

                        if (!empty($id)) {
                            return $mensaje = [
                                'tipo' => 'success',
                                'mensaje' => 'la caja fue actualizada con exito'
                            ];
                        } else {
                            return $mensaje = [
                                'tipo' => 'success',
                                'mensaje' => 'Se ha guardado con exito, la informacion de la caja.'
                            ];
                        }
                    } catch (Illuminate\Database\QueryException $e) {
                        return $mensaje = [
                            'tipo' => 'error',
                            'mensaje' => $e->getMessage()
                        ];
                    }
                });

        //print_r($fieldset);
        //die();
        //mensaje success
        if ($response["tipo"] == "success") {
            $this->session->set_flashdata('mensaje', "Se ha creado la caja satisfactoriamente.");
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }

    function tablatransferencias($modulo_id = null) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/cajas/tabla.transferencias.js'
        ));

        //Verificar desde donde se esta llamando
        //la tabla de facturas
        //if(preg_match("/cajas/i", self::$ci->router->fetch_class())){
        if (!empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "caja_id" => $modulo_id
            ));
        }
        //}

        $this->load->view('tabla-transferencias');
    }

    function ajax_listar_transferencias() {

        $clause = array(
            "empresa_id" => $this->empresa_id
        );

        $caja_id = $this->input->post('caja_id', true);

        if (!empty($caja_id)) {
            $clause["caja_id"] = $caja_id;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->TransferirCajaRepository->listar($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $filas = $this->TransferirCajaRepository->listar($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;
          if (count($filas)>0) {
               foreach($filas as $i => $row){

                $pagos_cadena = '';

                $tipos_pagos = !empty($row->pagos) ? $row->pagos->first() : array(); //Ahora la relacion es de uno a uno

                $link_option = '<button class="viewOptionsss btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url("cajas/transferir_detalle/" . bin2hex($row['caja']['uuid_caja'])) . "/" . $row['id'] . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';

                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    '<a href="' . base_url("cajas/transferir_detalle/" . bin2hex($row['caja']['uuid_caja'])) . "/" . $row['id'] . '" style="color:blue;">' . Util::verificar_valor($row['numero']) . '</a>',
                    !empty($row['fecha']) ? date("d/m/Y", strtotime($row['fecha'])) : "",
                    Util::verificar_valor($row["cuenta"]["nombre"]),
                    $row->present()->monto,//Util::verificar_valor($row["monto"]),
                    $tipos_pagos->pago_info->valor,
                    $row->present()->estado_label,
                    $link_option,
                    $hidden_options,
                );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    public function transferir_detalle($tranferir_uuid = NULL, $tranferir_id = NULL) {

        $transferir = $this->TransferirCajaRepository->find($tranferir_id);
        $transferir->fecha = date("d/m/Y", strtotime($transferir->fecha));
        //$tipo_pago = Pago_catalogos_orm::where('tipo', 'pago')->whereIn("etiqueta", array("al_contado", "cheque", "ach"))->get(array('id', 'etiqueta', 'valor'));
        $tipo_pago =    $this->CatalogoRepository->get(["modulo" => 'pagos','tipo'=>'metodo_pago']);
        $data = [];
        //$transferir->load('pagos', 'caja'); //Esta x gusto

        $cuentas_bancos = $this->CuentaBancoRepository->getAll(["empresa_id" => $this->empresa_id]);
        $cuentas_bancos->load("cuenta");

        //$caja = $transferir->caja->first(); //Esta x gusto

        $data['caja_id'] = $transferir->caja->id;
        $data['caja_nombre'] = $transferir->caja->nombre;
        $data['estados'] =    $this->CatalogoRepository->get(["modulo" => 'transferencias','tipo'=>'estado','activo'=>'1']);

        $this->assets->agregar_css(array(
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/modules/cajas/transferir.controller.js',
        ));

         $breadcrumb = array(
          "titulo" => '<i class="fa fa-shopping-cart"></i> Transferir a caja: ' . $transferir->caja->numero . ':  ' . $transferir->caja->nombre,
            "filtro" => false,
            "ruta" => [
              0 => [
                    "nombre" =>  ucfirst($this->modulo_padre),
                    "activo" => false,
              ],
              1 => [
                    "nombre" => "Cajas",
                    "activo" => false,
                    "url" => 'cajas/listar'
              ],
              2=> [
                    "nombre" => '<b>Detalle Transferir</b>',
                    "activo" => true
              ]
            ],
        );
        $this->assets->agregar_var_js(array(
            "cuentas_bancosList" => json_encode($cuentas_bancos),
            "cuenta_id" => $transferir->cuenta_id,
            "estado" => $transferir->estado,
            "estados" =>   json_encode($data['estados']),
            "caja_id" => $transferir->caja->id,
        ));

        $data = ['transferir' => $transferir, 'tipo_pagos' => $tipo_pago,'caja_id'=>$transferir->caja->id];


        $this->template->agregar_titulo_header('Transferir a caja');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function transferir($caja_uuid=NULL)
    {
     	//si no existe caja retornar
    	if($caja_uuid==NULL){
    		return false;
    	}
        $clause = array();
    	$data = array();

    	//Caja Info
    	$caja_info = $this->caja->findByUuid($caja_uuid);


    	$data['caja_id'] = $caja_info->id;
    	$data['nombre'] = $caja_info->nombre;
    	$data['numero'] = $caja_info->numero;
    	$maximo_transferir = $caja_info->limite-$caja_info->saldo;


     	$this->assets->agregar_css(array(
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    		'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    		'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    		'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    		'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    		'public/assets/js/default/tabla-dinamica.jquery.js',
    		'public/assets/js/modules/cajas/transferir.controller.js',
    	));


      $breadcrumb = array(
          "titulo" => '<i class="fa fa-shopping-cart"></i> Caja: '. $caja_info->numero . ' - '. $caja_info->nombre,
          "filtro" => false,
          "ruta" => array(
            0 => array(
                "nombre" =>  ucfirst($this->modulo_padre),
                "activo" => false,
            ),
              1 => array(
                  "nombre" => "Cajas",
                  "activo" => false,
                  "url" => 'cajas/listar'
              ),
              2=> array(
                  "nombre" => '<b>Crear Transferir</b>',
                  "activo" => true
              )
          ),
      );

    	//$data['tipo_pagos'] = Pago_catalogos_orm::where('tipo','pago')->whereIn("etiqueta", array("al_contado", "aplicar_credito", "cheque", "tarjeta_de_credito", "ach", "caja_chica"))->get(array('id','etiqueta','valor'));
      $data['tipo_pagos'] =    $this->CatalogoRepository->get(["modulo" => 'pagos','tipo'=>'metodo_pago']);

      $cuentas_bancos = $this->CuentaBancoRepository->getAll(["empresa_id" => $this->empresa_id]);
      $cuentas_bancos->load("cuenta");
      $estados = $this->CatalogoRepository->get(["modulo" => 'transferencias']);

    	$this->assets->agregar_var_js(array(
    		"estados" => json_encode($estados),
    	  "cuentas_bancosList" => json_encode($cuentas_bancos),
    		"caja_id" => $caja_info->id,
    		"maximo_transferir" => $maximo_transferir,
    	));

     	$this->template->agregar_titulo_header('Transferir a caja');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    public function transferir_desde_caja($caja_uuid=NULL)
    {

    	if($caja_uuid==NULL){
    		return false;
    	}
      $clause = array();
    	$data = array();

    	$caja_info = $this->caja->findByUuid($caja_uuid);

     	$data['caja_id'] = $caja_info->id;
    	$data['nombre'] = $caja_info->nombre;
    	$data['numero'] = $caja_info->numero;
    	$data['caja_uuid'] = $caja_uuid;
    	$maximo_transferir = $caja_info->limite-$caja_info->saldo;

     	$this->assets->agregar_css(array(
             'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
    	));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/modules/cajas/transferir_desde.js',
    	));

         $empezable = collect([
            'id' =>'',
            'type' =>'',
            'bancos' => [],
            'cajas' => []
        ]);

        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => 1,
             "caja_id" => $caja_info->id,
            "empezable" => $empezable
        ));

    	$breadcrumb = array(
    		"titulo" => '<i class="fa fa-shopping-cart"></i> Transferencia desde: '. $caja_info->numero . ' - '. $caja_info->nombre
    	);

     	$this->template->agregar_titulo_header('Transferir desde caja');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
     public function guardar_transferir_desde()
    {

    	if(empty($_POST)){
    		return false;
    	}

      $request = Illuminate\Http\Request::createFromGlobals();
      $post = $this->input->post();
      $campo = $request->input('campo');
      $tipospago = $request->input('tipospago');

     	$fecha		= !empty($campo['fecha']) ? str_replace('/', '-', $campo['fecha']) : "";
     	$fecha		= !empty($campo['fecha']) ? date("Y-m-d", strtotime($fecha)) : "";

     	$fieldset = array(
	    	"empresa_id" 	=> $this->empresa_id,
    		"caja_id" 		=> $campo['desde_caja_id'],
	    	"cuenta_id" 	=> $campo['cuenta_id'],
    		"numero" 		=> Capsule::raw("NO_TRANSFERENCIA('TFR', ". $this->empresa_id .")"),
    		"monto" 		=> $campo['monto'],
	    	"fecha" 		=> $fecha,
            "estado"                => 'por_aprobar',
	    	"creado_por"            => $this->usuario_id,
            "transferencia_desde" => 1,
            "tipo_transferencia_hasta" =>  $this->input->post('empezable_type', TRUE),
    		"tipospago"		=> $tipospago
	    );

    	$desde_caja = $this->caja->find($campo['desde_caja_id']);
    	$hasta_caja = $this->caja->find($_POST['empezable_id']);
     	$transferencia = Capsule::transaction(function() use($fieldset) {

    		try {
     			return $this->TransferirCajaRepository->create($fieldset);
    		} catch(Illuminate\Database\QueryException $e) {
    			log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
    		}
    	});

    	if(!is_null($transferencia)){

    		$transferencia_creado = $this->TransferirCajaRepository->find($transferencia->id);
                $this->caja->cambiandoSaldo($desde_caja,$this->TransferirCajaRepository->rebajaCaja($transferencia_creado, $desde_caja) );
                //Cuando la operacion es hacia la caja
                 if( $_POST['empezable_type'] == 'caja' ){
                     $this->caja->cambiandoSaldo($hasta_caja ,$this->TransferirCajaRepository->subiendoCaja($transferencia_creado, $hasta_caja) );
                 }

                 $this->transaccionCaja->hacerTransaccion($transferencia, $transferencia_creado->numero);



    		$this->session->set_flashdata('mensaje', "La transferencia a caja fue guarda con exito.");

    		$response = [
	    		'tipo' => 'success',
	    		'mensaje' => 'La transferencia desde caja fue guarda con exito.'
    		];
     		//Disparar evento
    		//$this->disparador->fire(new ActualizarCajaSaldoEvent($transferencia, $caja));
    	}else{
    		$response = [
	    		'tipo' => 'error',
	    		'mensaje' => 'No se pudo guardar la transferencia.',
	    		'asas' => json_encode($transferencia)
    		];
    	}

          if (!is_null($transferencia)) {
              $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $transferencia->codigo);
          } else {
              $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
          }
           $this->session->set_flashdata('mensaje', $response['mensaje']);
          redirect(base_url('cajas/listar'));

    }

    public function ajax_guardar_transferencia()
    {

    	if(empty($_POST)){
    		return false;
    	}

    	$id 		= $this->input->post('id', TRUE);
    	$caja_id 	= $this->input->post('caja_id', TRUE); //Cuenta Banco Id
    	$cuenta_id 	= $this->input->post('cuenta_id', TRUE); //Cuenta Banco Id
    	$monto 		= $this->input->post('monto', TRUE); //Monto a Transferir
    	$fecha 		= $this->input->post('fecha', TRUE);
    	$fecha		= !empty($fecha) ? str_replace('/', '-', $fecha) : "";
    	$fecha		= !empty($fecha) ? date("Y-m-d", strtotime($fecha)) : "";
    	$tiposPago	= $this->input->post('tipospago', TRUE);
    	$estado	= $this->input->post('estado', TRUE);

    	$fieldset = array(
	    	"empresa_id" 	=> $this->empresa_id,
    		"caja_id" 		=> $caja_id,
	    	"cuenta_id" 	=> $cuenta_id,
    		"numero" 		=> Capsule::raw("NO_TRANSFERENCIA('TFR', ". $this->empresa_id .")"),
    		"monto" 		=> $monto,
	    	"fecha" 		=> $fecha,
	    	"creado_por" 	=> $this->usuario_id,
	    	"estado" 	=> $estado,
                "transferencia_desde" => 0,
	    	"tipo_transferencia_hasta" 	=> 'caja',
    		"tipospago"		=> $tiposPago
	    );
      if(isset($id) && $id>0){ //Edicion
        $fieldset = array(
          "id" 	=> $id,
          "estado" 	=> $estado,
        );
      }

    	$caja = $this->caja->find($caja_id);
     	$transferencia = Capsule::transaction(function() use($fieldset) {

    		try {
    			return $this->TransferirCajaRepository->create($fieldset);
    		} catch(Illuminate\Database\QueryException $e) {
    			log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
    		}
    	});

    	//mensaje success
    	if(!is_null($transferencia)){
             if(is_null($id)){
          		$transferencia_creado = $this->TransferirCajaRepository->find($transferencia->id);
           		$this->transaccionCaja->hacerTransaccion($transferencia, $transferencia_creado->numero);
            }else if(!is_null($id) && $estado == 'aprobado'){
              $this->_createPago($transferencia);
            }

        $this->session->set_flashdata('mensaje', "La transferencia a caja fue guarda con exito.");

    		$response = [
          'uuid_caja'=>$caja->uuid_caja,
	    		'tipo' => 'success',
	    		'mensaje' => 'La transferencia a caja fue guarda con exito.'
    		];
     		//Disparar evento
        if(is_null($id))
    		    $this->disparador->fire(new ActualizarCajaSaldoEvent($transferencia, $caja));

    	}else{
    		$response = [
	    		'tipo' => 'error',
	    		'mensaje' => 'No se pudo guardar la transferencia.',
	    		'asas' => json_encode($transferencia)
    		];
    	}

    	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
    	exit();
    }

    //actualmente se utiliza para mostrar las facturas en cajas
  //las cuales son filtradas atravez
  function ocultoSubPanel($caja_id){
    $this->assets->agregar_js(array(
      'public/assets/js/modules/cajas/subpanels/tablafacturas.js'
    ));

    if (!empty($caja_id)) {
        $this->assets->agregar_var_js(array(
            "caja_id" => $caja_id
        ));
    }
    $this->load->view('tabla_facturas');
  }

  function ajax_listar_facturas_ventas(){

      if (!$this->input->is_ajax_request()) {
          return false;
      }
      $caja_id = $this->input->post('caja_id');
      $clause = ['caja_id'=>$caja_id, 'empresa_id'=>$this->empresa_id];
      $busqueda = new Flexio\Modulo\Cajas\SubPanels\CajaFacturaVenta;

      list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

      $count = $busqueda->getFacturaVenta($clause)->count();

      list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

      $facturas = $busqueda->getFacturaVenta($clause, $sidx, $sord, $limit, $start)->get();
      //dd($rows->toArray());
      //Constructing a JSON
      $response = new stdClass();
      $response->page = $page;
      $response->total = $total_pages;
      $response->records = $count;
      $response->result = array();
      $i = 0;
     // dd($facturas);
      foreach($facturas as $row){

          $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_factura . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
          $url = base_url('facturas/editar/' . $row->uuid_factura);
          if ($row->formulario == 'refactura') {
              $url = base_url('facturas/refacturar/' . $row->uuid_factura);
          }
          $hidden_options = "";
          $hidden_options .= '<a href="' . $url . '" data-id="' . $row->uuid_factura . '" class="btn btn-block btn-outline btn-success">Ver Factura</a>';

          $response->rows[$i]["id"] = $row->uuid_factura;
          $response->rows[$i]["cell"] = array(
              $row->uuid_factura,
              '<a class="link" href="' . $url . '" >' . $row->codigo . '</a>',
              '<a class="link">' . $row->cliente_nombre . '</a>',
              Carbon::createFromFormat('m/d/Y', $row->fecha_desde, 'America/Panama')->format('d/m/Y'),
              Carbon::createFromFormat('m/d/Y', $row->fecha_hasta, 'America/Panama')->format('d/m/Y'),
              $row->estado_factura,
              '<label class="totales-success">' . Util::verificar_valor($row->total) . '</label>',
              '<label class="totales-danger">' . number_format(($row->total - $row->total_facturado()), 2, '.', ',') . '</label>',//manejar por relacion -factura-pago
              '<a class="link">' . $row->vendedor_nombre . '</a>',
              $link_option,
              $hidden_options
          );
          $i++;

      }

      $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output(json_encode($response))->_display();
      exit;
  }
    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/cajas/vue.comentario.js',
            'public/assets/js/modules/cajas/formulario_comentario.js'
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
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuario_id];
        $caja = $this->caja->agregarComentario($model_id, $comentario);
        $caja->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($caja->comentario_timeline->toArray()))->_display();
        exit;
    }

    function documentos_campos(){

    	return array(
    	array(
    		"type"		=> "hidden",
    		"name" 		=> "caja_id",
    		"id" 		=> "caja_id",
    		"class"		=> "form-control",
    		"readonly"	=> "readonly",
    	));
    }

    function ajax_guardar_documentos()
    {
    	if(empty($_POST)){
    		return false;
    	}

    	$caja_id = $this->input->post('caja_id', true);
        $modeloInstancia = $this->caja->find($caja_id);
    	$this->documentos->subir($modeloInstancia);
    }

    private function _createPago( $transferencia = []) {

         $total = Pagos_orm::deEmpresa($transferencia->empresa_id)->count();
        $year = Carbon::now()->format('y');

       $contador = 1;

       if(count($transferencia->pagos)>0){
          foreach ($transferencia->pagos as $key => $value) {

            if($value->pago_info->etiqueta == 'cheque'){
             $aux = [];
             $pago = new Pagos_orm;
             $codigo = Util::generar_codigo('PGO' . $year, $total + $contador);
             //$total_pagado_nuevo = (float)str_replace(",","",$value->monto);
             $pago->codigo = $codigo;

             $pago->empresa_id = $transferencia->empresa_id;
             $pago->fecha_pago = date("Y-m-d");
             $pago->proveedor_id = $transferencia->caja->id;
             $pago->monto_pagado = $value->monto;
             $pago->cuenta_id = $transferencia->cuenta_id;
             $pago->depositable_id = $transferencia->id;
             $pago->depositable_type = 'Flexio\\Modulo\\Cajas\\Models\\Transferencias';

             $pago->formulario = 'transferencia'; // Poner Caja
             $pago->estado = 'por_aprobar';
             $pago->save();

             $aux[$transferencia->id] = array(
                 "pagable_type" =>'Flexio\\Modulo\\Cajas\\Models\\Transferencias',
                 "monto_pagado" => $value->monto,
                 "empresa_id" => $transferencia->empresa_id
             );


             $pago->transferencias()->sync($aux);

             $item_pago = new Pago_metodos_pago_orm;

             $referencia = $this->pagoGuardar->tipo_pago('cheque', array(
               'numero_cheque'=>'',
               'nombre_banco_cheque'=>''
             ));

             $item_pago->tipo_pago = 'cheque';
             $item_pago->total_pagado = $value->monto;
             $item_pago->referencia = 'cheque transferencia';
             $pago->metodo_pago()->save($item_pago);
             ++$contador;


           }
          }
       }
    }

}
