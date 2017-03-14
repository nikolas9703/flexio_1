<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
use League\Csv\Writer as Writer;
use Flexio\Modulo\SegCatalogo\Repository\SegCatalogoRepository as SegCatalogoRepository;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\Agentes\Models\Agentes as Agente;
use Flexio\Modulo\aseguradoras\Repository\AseguradorasRepository as AseguradorasRepository;
use Flexio\Modulo\Polizas\Models\Polizas as PolizasModel;
use Flexio\Modulo\Polizas\Repository\PolizasRepository as PolizasRepository;
use Flexio\Modulo\Polizas\Models\PolizasBitacora;
use Flexio\Modulo\Documentos\Repository\DocumentosRepository as DocumentosRepository;
use Flexio\Modulo\Solicitudes\Models\Solicitudes as solicitudesModel;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable as centroModel;
use Flexio\Modulo\Planes\Models\Planes;
use Flexio\Modulo\Planes\Models\PlanesComisiones;
use Dompdf\Dompdf;
use Flexio\Modulo\Polizas\Models\PolizasPrima;
use Flexio\Modulo\Polizas\Models\PolizasVigencia;
use Flexio\Modulo\Polizas\Models\PolizasCobertura;
use Flexio\Modulo\Polizas\Models\PolizasDeduccion;
use Flexio\Modulo\Polizas\Models\PolizasParticipacion;
use Flexio\Modulo\Polizas\Models\PolizasAcreedores;
use Flexio\Modulo\Polizas\Models\PolizasCliente;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_catModel;
use Flexio\Modulo\Acreedores\Repository\AcreedoresRepository as AcreedoresRep;
use Flexio\Modulo\Polizas\Models\PolizasArticulo;
use Flexio\Modulo\Polizas\Models\PolizasCarga;
use Flexio\Modulo\Polizas\Models\PolizasAereo;
use Flexio\Modulo\Polizas\Models\PolizasMaritimo;
use Flexio\Modulo\Polizas\Models\PolizasPersonas;
use Flexio\Modulo\Polizas\Models\PolizasProyecto;
use Flexio\Modulo\Polizas\Models\PolizasUbicacion;
use Flexio\Modulo\Polizas\Models\PolizasVehiculo;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepository as FacturaVentaRepository;
use Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroRepository as FacturaSeguroRepository;
use Flexio\Modulo\SegInteresesAsegurados\Repository\SegInteresesAseguradosRepository as SegInteresesAseguradosRepository;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro as FacturaSeguro;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository as PoliticasRepository;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;
use Flexio\Modulo\Ramos\Models\RamosUsuarios;
use Flexio\Modulo\Ramos\Repository\RamoRepository as RamoRepository;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados as AseguradosModel;
use Flexio\Modulo\Usuarios\Models\CentrosUsuario;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\Cliente\Models\Cliente as clienteModel;


class Polizas extends CRM_Controller {

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
    private $AcreedoresRep;
    protected $DocumentosRepository;
    protected $polizasModel;
    protected $AseguradorasRepository;
    private $InteresesAsegurados_catModel;
    private $solicitudesModel;
    protected $facturaVentaRepository;
    protected $FacturaSeguroRepository;
    protected $politicas;
    protected $politicas_general;
    protected $PoliticasRepository;
    protected $SegInteresesAseguradosRepository;
    protected $interesesAseguradosRep;
    

    function __construct() {
        parent::__construct();
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        $this->load->helper(array('file', 'string', 'util'));
        $this->polizasModel = new PolizasModel;
        $this->SegCatalogoRepository = new SegCatalogoRepository();
        $this->SegInteresesAseguradosRepository = new SegInteresesAseguradosRepository();
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->PolizasRepository = new PolizasRepository;
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->id_empresa = $this->empresaObj->id;
        $this->DocumentosRepository = new DocumentosRepository();
        $this->load->module(array('documentos'));

        $this->solicitudesModel = new solicitudesModel();

        $this->AseguradorasRepository = new AseguradorasRepository();
        $this->InteresesAsegurados_catModel = new InteresesAsegurados_catModel();
        $this->AcreedoresRep = new AcreedoresRep();

        $uuid_empresa = $this->session->userdata('uuid_empresa');

        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
        $this->facturaVentaRepository = new FacturaVentaRepository;
        $this->FacturaSeguroRepository = new FacturaSeguroRepository;
        $this->PoliticasRepository = new PoliticasRepository();
        $this->ramoRepository = new RamoRepository();

        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuarios::findByUuid($uuid_usuario);

        $this->usuario_id = $usuario->id;

        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
        $this->interesesAseguradosRep = new AseguradosModel();
        $this->roles = $this->session->userdata("roles");

        $clause['empresa_id'] = $this->empresa_id;
        $clause['modulo'] = 'polizas';
        $clause['usuario_id'] = $this->usuario_id;
        $clause['role_id'] = $this->roles;

        $politicas_transaccion = $this->PoliticasRepository->getAllPoliticasRoles($clause);

        $politicas_transaccion_general = count($this->PoliticasRepository->getAllPoliticasRolesModulo($clause));
        $politicas_transaccion_general2 = $this->PoliticasRepository->getAllPoliticasRolesModulo($clause)->toArray();
        $this->politicas_general = $politicas_transaccion_general;

        $estados_politicas = array();
        foreach ($politicas_transaccion as $politica_estado) {
            $estados_politicas[] = $politica_estado->politica_estado;
        }

        $this->politicas = $estados_politicas;
        if (isset($politicas_transaccion_general2[0]['politica_estado'])) {
            $this->politicasgenerales = $politicas_transaccion_general2[0]['politica_estado'];
        } else {
            $this->politicasgenerales = "";
        }
    }

    public function listar() {

        //Definir mensaje
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje),
            "estado_solicitud" => 0,
            "estado_pol" => 0,
            "validavida" => 0
            ));


        $data = array();
        $clause = array('empresa_id' => $this->id_empresa);

        $this->_css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/polizas/listar_polizas.js',
            ));


        //Breadcrum Array
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Pólizas',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Polizas</b>', "activo" => true)
                ),
            "filtro" => true,
            "menu" => array(
                "nombre" => "Accion",
                "url" => "#",
                "opciones" => array()
                )
            );
        //$menuOpciones["#cambiarEstadoPolizasLnk"] = "Cambiar estado";
        $menuOpciones["#imprimirCartaPolizasLnk"] = "Imprimir carta";
        $menuOpciones["#exportarPolizasLnk"] = "Exportar";
        $menuOpciones["#agendarCobroLnk"] = "Agendar cobro";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $breadcrumb["botones"]["Polizas"] = '<i class="fa fa-tasks"></i> Pipeline';
        $breadcrumb["botones"]["Polizas"] = '<i class="fa fa-star"></i> Score';

        $data["ramos"] = Ramos::where($clause)->get();
        $data["estados"] = SegCatalogo::where(array("tipo" => "estado_p"))->get();
        $data["aseguradoras"] = Aseguradoras::where($clause)->get();
        $data["categorias"] = SegCatalogo::where(array("tipo" => "categoria_pol"))->get();


        $usuarios = $this->db->query("SELECT sol.usuario_id as id,usu.nombre,usu.apellido 
          FROM seg_solicitudes AS sol 
          JOIN usuarios AS usu ON usu.id=sol.usuario_id 
          GROUP BY sol.usuario_id ORDER BY usu.apellido ASC");
        $data["usuarios"] = $usuarios->result();
        
        $ramosRoles = RolesUsuario::with(array('ramos'))->where(['usuario_id' => $this->usuario_id, 'empresa_id' => $this->empresa_id])->get();
        $ramosUsuario = RamosUsuarios::where(['id_usuario' => $this->usuario_id])->get();

        $data['rolesArray'] = array();
        $data['usuariosArray'] = array();
        $i = 0;
        foreach ($ramosRoles AS $value) {
            foreach ($value->ramos AS $valuee) {
                $data['rolesArray'][$i] = $valuee->id_ramo;
                $i++;
            }
        }
        $i = 0;
        foreach ($ramosUsuario AS $value) {
            $data['usuariosArray'][$i] = $value['id_ramo'];
            $i++;
        }
        $data['menu_crear'] = $this->ramoRepository->listar_cuentas($clause);

        $this->template->agregar_titulo_header('Listado de Pólizas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);


        $this->template->visualizar($breadcrumb);
    }

    public function crear() {
        $data = array();
        $mensaje = array();

        if (!empty($_POST["campo"])) {
            $this->guardar_poliza();
        }

        $this->_css();
        $this->_js();


        $estado = $this->SegCatalogoRepository->listar_catalogo('estado_p', 'orden');
        $estado = $estado->where("key", "polizas_pf");

        //$aseguradoras = Aseguradoras::where(array( "empresa_id"=>$this->id_empresa, "estado"=>'Activo' ))->orderBy("nombre","asc")->get();
        $aseguradoras = '';
        $this->assets->agregar_var_js(array(
            "estado_solicitud" => $estado,
            "aseguradoras" => $aseguradoras,
            "inicio_vigencia" => "",
            "fin_vigencia" => "",
            "estado_pol" => "",
            "aseguradora_pol" => ""
            ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-book"></i> Crear Poliza',
            "ruta" => array(
                0 => array(
                    "nombre" => "Poliza",
                    "activo" => false,
                    "url" => '')
                )
            );

        $this->template->agregar_titulo_header('Polizas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ocultoformulario($data = array()) {
        $this->assets->agregar_js(array('public/assets/js/modules/polizas/crear.js'));
        $usuario = Usuario_orm::findByUuid($this->id_usuario);
        $usuario_id = $usuario->id;
        $data["campos"] = array(
            'usuario' => $usuario_id,
            'creado_por' => $usuario_id,
            'empresa_id' => $this->id_empresa,
            );
        $this->load->view('formulario', $data);
    }

    function ajax_listar() {

        $ver_renovacion = 0;
        $permiso_comision = 0;
        $permiso_agente = 0;
        $permiso_participacion = 0;

        if($this->auth->has_permission('acceso', 'polizas/crear renovación') == true){
            $ver_renovacion = 1;
        }

        if($this->auth->has_permission('acceso', 'polizas/editar comisión plan') == true){
            $permiso_comision = 1;
        }

        if($this->auth->has_permission('acceso', 'polizas/editar agentes') == true){
            $permiso_agente = 1;
        }

        if($this->auth->has_permission('acceso', 'polizas/editar participación') == true){
            $permiso_participacion = 1;
        }


        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $usuario_org = $usuario->organizacion;

        $orgid = $usuario_org->map(function($org) {
            return $org->id;
        });
        
        $clause = array(
            "usuario_id" => $this->usuario_id
            );
        
        $clause["empresa_id"]= $this->id_empresa;

        $cliente = $this->input->post('cliente', true);
        $aseguradora = $this->input->post('aseguradora', true);
        $categoria = $this->input->post('categoria', true);
        $ini_vigenci = $this->input->post('ini_vigenci', true);
        $fin_vigenci = $this->input->post('fin_vigenci', true);
        $usuario = $this->input->post('usuario', true);
        $declarativa = $this->input->post('declarativa', true);
        $estado = $this->input->post('estado', true);
        $ramo = $this->input->post('ramo', true);
        $no_poliza = $this->input->post('no_poliza', true);

        if (!empty($cliente)) {
            $clause["cliente"] = array('LIKE', "%$cliente%");
        }
        if (!empty($no_poliza)) {
            $clause["numero"] = $no_poliza;
        }
        if (!empty($aseguradora)) {
            $clause["aseguradora_id"] = $aseguradora;
        }
        if (!empty($categoria)) {
            $clause["categoria"] = $categoria;
        }
        if (!empty($ini_vigenci)) {
            $clause["inicio_vigencia"] = array('>=', $ini_vigenci);
        }
        if (!empty($fin_vigenci)) {
            $clause["fin_vigencia"] = array('<=', $fin_vigenci);
        }
        if (!empty($usuario)) {
            $clause["usuario"] = $usuario;
        }
        if (!empty($declarativa)) {
            $clause["declarativa"] = $declarativa;
        }
        if (!empty($estado)) {
            $clause["estado"] = $estado;
        }
        if (is_array($ramo) AND $ramo[0] != "") {
            $clause["ramo"] = $ramo;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = PolizasModel::listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = PolizasModel::listar($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;
        $rows = (object) $rows;
        if (!empty($rows)) {
            foreach ($rows AS $i => $row) {
               $renovationUrl=  base_url('polizas/editar/' . strtoupper(bin2hex($row->uuid_polizas)).'/renovar');

               $estado_color = $row->estado == "Por Facturar" ? 'background-color: #F0AD4E' : ($row->estado == "Facturada" ? 'background-color: #5cb85c' : ($row->estado == "Cancelada" ? 'background-color: #222222' : ($row->estado == "Expirada" ? 'background-color: #FC0D1B' : 'background-color: #00BFFF')));

               $color_factura = "";
               switch (strtoupper($row->frecuencia_facturacion)) {
                case 'MENSUAL':
                        //Calculo ultima factura no mayor a un mes
                $ultimaFactura = $row->ultima_factura;
                $fecha = date('Y-m-d', strtotime($ultimaFactura));
                $nuevafecha = strtotime('+1 month', strtotime($fecha));
                $hoy = strtotime(date("Y-m-d"));

                if ($nuevafecha < $hoy) {
                    $color_factura = 'color:red;';
                }
                break;
                case 'TRIMESTRAL':
                        //Calculo ultima factura no mayor a tres mes
                $ultimaFactura = $row->ultima_factura;
                $fecha = date('Y-m-d', strtotime($ultimaFactura));
                $nuevafecha = strtotime('+3 month', strtotime($fecha));
                $hoy = strtotime(date("Y-m-d"));

                if ($nuevafecha < $hoy) {
                    $color_factura = 'color:red;';
                }
                break;
                case 'SEMESTRAL':
                        //Calculo ultima factura no mayor a seis meses
                $ultimaFactura = $row->ultima_factura;
                $fecha = date('Y-m-d', strtotime($ultimaFactura));
                $nuevafecha = strtotime('+6 month', strtotime($fecha));
                $hoy = strtotime(date("Y-m-d"));

                if ($nuevafecha < $hoy) {
                    $color_factura = 'color:red;';
                }
                break;
                case 'ANUAL':
                $ultimaFactura = $row->ultima_factura;
                $fecha = date('Y-m-d', strtotime($ultimaFactura));
                $nuevafecha = strtotime('+1 year', strtotime($fecha));
                $hoy = strtotime(date("Y-m-d"));

                if ($nuevafecha < $hoy) {
                    $color_factura = 'color:red;';
                }
                        //Calculo ultima factura no mayor a un año
                break;
            }


            $hidden_options = "<a href=" . base_url('polizas/editar/' . strtoupper(bin2hex($row->uuid_polizas))) . " class='viewOptions btn btn-block btn-outline btn-success'>Ver Póliza</a>";
            $hidden_options .= '<a href="#" id="crearEndosoBtn" class="viewOptions btn btn-block btn-outline btn-success">Crear Endoso</a>';
            $hidden_options .= $row->poliza_declarativa == 'si' ? '<a href="'.base_url().'endosos/crear/'.$row->id.'" id="crearDeclaracionBtn"  class="viewOptions btn btn-block btn-outline btn-success">Crear Declaración</a>' : '';
            $hidden_options .= '<a href="#" id="agregarReclamoBtn" data-id="' . $row->id . '" data-ramo="'.$row->ramo_id.'" class="viewOptions btn btn-block btn-outline btn-success">Crear Reclamo</a>';
                //$hidden_options .= '<a href="#" id="renovarBtn" data-id="' . $row->id . '" class="viewOptions btn btn-block btn-outline btn-success renovationModal">Renovar</a>';
            if($row->estado == "Facturada"){
                $hidden_options .= '<a href="#" id="AgendarCobro" data-id="' . $row->id . '" class="btn btn-block btn-outline btn-success AgendarCobro">Agendar cobro</a>';
            }

            if( ($row->estado == "Facturada" || $row->estado == "Expirada") && $ver_renovacion == 1){
                $hidden_options .= '<a href="'.$renovationUrl.'" id="renovarBtn" data-id="' . $row->id . '" permiso_comision="'.$permiso_comision.'" permiso_agente="'.$permiso_agente.'" permiso_participacion="'.$permiso_participacion.'" class="viewOptions btn btn-block btn-outline btn-success">Renovar</a>';
            }

            if ( ($row->estado == "Expirada" OR $row->estado == "Facturada") && $ver_renovacion == 1 ) {
                $hidden_options .= '<a href="#" id="renovarBtn" class="viewOptions btn btn-block btn-outline btn-success cambiaEstadoBtn notRenowal" data-id="' . $row->id . '" data-estado="No Renovada">No Renovar</a>';
            }
            if ($row->estado == "Por Facturar") {
                $politicas_general = $this->politicas_general;
                $politicas = $this->politicas;
                $validar_politicas = $this->politicasgenerales;
                if ($politicas_general > 0) {

                    if (in_array(23, $politicas) && $validar_politicas == 23) {
                            //$hidden_options .= '<a href="' . base_url("polizas/facturar/" . strtoupper(bin2hex($row->uuid_polizas))) . '" id="facturarBtn" class="viewOptions btn btn-block btn-outline btn-success">Facturar</a>';
                    } else if (in_array(23, $politicas)) {
                        $hidden_options .= '<button data-id="alert" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>';
                    }
                } else {
                        //$hidden_options .= '<a href="' . base_url("polizas/facturar/" . strtoupper(bin2hex($row->uuid_polizas))) . '" id="facturarBtn" class="viewOptions btn btn-block btn-outline btn-success">Facturar</a>';
                }
            }
            $hidden_options .= '<a href="#" id="imprimirBtn" class="viewOptions btn btn-block btn-outline btn-success">Imprimir Carta</a>';
            $hidden_options .= '<a href="' . base_url('polizas/bitacora/' . strtoupper(bin2hex($row->uuid_polizas))) . '" class="viewOptions btn btn-block btn-outline btn-success">Bitacora</a>';
            $linkEstado = '';

            if ($row->estado != "Por Facturar") {
                $linkEstado .= '<a href="#" class="viewOptions btn btn-block btn-warning cambiaEstadoBtn" data-id="' . $row->id . '" data-estado="Por Facturar">Por Facturar</a>';
            }
            if ($row->estado != "Facturada") {
                $politicas_general = $this->politicas_general;
                $politicas = $this->politicas;
                $validar_politicas = $this->politicasgenerales;
                if ($politicas_general > 0) {

                    if (in_array(23, $politicas) && $validar_politicas == 23) {
                        $linkEstado .= '<a href="#" class="viewOptions btn btn-block btn-primary cambiaEstadoBtn" data-id="' . $row->id . '" data-estado="Facturada">Facturada</a>';
                    } else if ($validar_politicas == 23) {
                        $linkEstado .= '<button data-id="alert" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>';
                    }
                } else {
                    $linkEstado .= '<a href="#" class="viewOptions btn btn-block btn-primary cambiaEstadoBtn" data-id="' . $row->id . '" data-estado="Facturada">Facturada</a>';
                }
            }


            if ($row->estado == "Facturada" OR $row->estado == "Expirada") {
                $linkEstado .= '<a href="#" class="viewOptions btn btn-block btn-info cambiaEstadoBtn" data-id="' . $row->id . '" data-estado="No Renovada">No Renovar</a>';
            }


            $hidden_options .= '<a data-id="' . $row->id . '" class="viewOptions btn btn-block btn-outline btn-success subir_archivos_poliza"  data-type="' . $row->id . '" >Subir Archivos</a>';


            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $saldo = PolizasModel::saldo_pendiente($row->id);

            $response->rows[$i]["id"] = $row->id;
            $response->rows[$i]["cell"] = array(
                $row->id,
                "<a href='" . base_url('polizas/editar/' . strtoupper(bin2hex($row->uuid_polizas))) . "'>" . $row->numero . "</a>",
                strtoupper($row->clientefk->nombre),
                $row->aseguradorafk->nombre,
                $row->ramo,
                $row->inicio_vigencia,
                $row->fin_vigencia,
                $row->frecuencia_facturacion,
                '<span class="btn btn-block btn-xs" style="' . $color_factura . '">' . $row->ultima_factura . '</span>',
                //$row->present()->saldo_pendiente,
                $saldo,
                //$row->usuariofk->nombre . " " . $row->usuariofk->apellido,
                $row->categoriafk->valor,
                '<span class="btn btn-block btn-xs estadoPoliza" style="color:white;' . $estado_color . '" data-id="' . $row->id . '" data-estado="'.$row->estado.'" >' . $row->estado . '</span>',
                $link_option,
                $hidden_options,
                $linkEstado
                );
        }
    }

    echo json_encode($response);
    exit;
}

function guardar() {
    unset($_POST["campo"]["guardarFormBtn"]);

        /**
         * Inicializar Transaccion
         */
        Capsule::beginTransaction();

        try {
            $usuario = Usuario_orm::findByUuid($this->id_usuario);
            $usuario_id = $usuario->id;
            $fieldset = Util::set_fieldset("campo");
            $fieldset['empresa_id'] = $this->id_empresa;
            $fieldset["uuid_polizas"] = Capsule::raw("ORDER_UUID(uuid())");
            $fieldset["created_at"] = date('Y-m-d H:i:s');
            $fieldset["usuario"] = $usuario_id;
            $fieldset["creado_por"] = $usuario_id;
            /**
             * Guardar Aseguradora
             */
            $colaborador = PolizasModel::create($fieldset);
        } catch (ValidationException $e) {

            // Rollback
            Capsule::rollback();
        }

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();

        //Redireccionar
        redirect(base_url('polizas/listar'));
    }

    //Abrir form de edicion
    function editoformulario($data) {
        $uuid = $data["campos"]["uuid_polizas"];
        $poliza = new PolizasModel();
        $poliza = $poliza->where("uuid_polizas", "=", hex2bin(strtolower($uuid)))->with(array('clientefk'))->first();
        $data["campos"] = array(
            "id" => $poliza->id,
            "created_at" => $poliza->created_at,
            "uuid_polizas" => strtoupper(bin2hex($poliza->uuid_polizas)),
            "numero" => $poliza->numero,
            "empresa_id" => $poliza->empresa_id,
            "cliente" => $poliza->cliente,
            "clientetxt" => $poliza->clientefk->nombre,
            "ramo" => $poliza->ramo,
            "estado" => $poliza->estado,
            "inicio_vigencia" => $poliza->inicio_vigencia,
            "fin_vigencia" => $poliza->fin_vigencia,
            "usuario" => $poliza->usuario,
            "creado_por" => $poliza->creado_por,
            "id_tipo_interes" => $poliza->id_tipo_int_asegurado,
            "politicas" => $this->politicas,
            "politicas_general" => $this->politicas_general,
            "validar_politicas" => $this->politicasgenerales,
            "poliza_declarativa" => $poliza->poliza_declarativa,
            );


        $this->load->view('formulario', $data);
    }

    public function renovationView($data = NULL) {



        $this->load->view('renovaciones', $data);
    }

    function comentaformulario($data) {
        $uuid = $data["campos"]["uuid_polizas"];
        $poliza = new PolizasModel();
        $poliza = $poliza->where("uuid_polizas", "=", hex2bin(strtolower($uuid)))->first();

        $data = array();
        $Bitacora = new Flexio\Modulo\Polizas\Models\PolizasBitacora;
        $data["Bitacora"] = $Bitacora;
        $data["nid_poliza"] = $poliza->id;
        $data["historial"] = $Bitacora->where(array("comentable_id" => $poliza->id, "comentable_type" => "Comentario"))->with(array('usuario'))->orderBy("created_at", "desc")->get(array("comentario", "created_at", "usuario_id"))->toArray();

        $this->load->view('comentarios', $data);
    }

    function cargabitacora($data) {
        $this->load->view('comentarios', $data);
    }

    function editar($uuid = NULL, $renovar = NULL) {
        if (!$this->auth->has_permission('acceso', 'polizas/editar') && !$this->auth->has_permission('acceso', 'polizas/ver')) {
            // No, tiene permiso, redireccionarlo.
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ver el registro', 'titulo' => 'Polizas ');

            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url('polizas/listar'));
        }

        if (!$uuid) {
            echo "Error.";
        }

        $data = array();
        $mensaje = array();
        $poliza = new PolizasModel();
        $poliza = $poliza->where("uuid_polizas", "=", hex2bin(strtolower($uuid)))->first();

        $renovacion = $poliza->categoria;
        if($renovacion != 45){ $validarenovacion = 0; }else{ $validarenovacion = 1; }

        if (!empty($_POST)) {
            $response = false;
            $response = Capsule::transaction(
                function() {
                    $poliza = new PolizasModel;
                    $poliza = $poliza->where("uuid_polizas", "=", hex2bin(strtolower($this->uri->segment(3, 0))))->first();

                    $campo = $this->input->post("campo");
                    $prima = $this->input->post("campoprima");     
                        //DATOS GENERALES DE LA ASEGURADORA
                    $poliza->estado = $campo["estado"];
                    $poliza->updated_at = date("Y-m-d H:i:s");
                    if(isset($prima)){
                     $polizaPagos = new PolizasPrima ();
                     $polizaPagos = $polizaPagos->where("id_poliza",$poliza->id)->first(); 
                     $poliza->frecuencia_facturacion=$prima['frecuencia_pago'];
                     $polizaPagos->frecuencia_pago = $prima['frecuencia_pago'];
                     $polizaPagos->metodo_pago = $prima['metodo_pago'];
                     $primerPago = new Carbon($prima['fecha_primer_pago']);    
                     $polizaPagos->fecha_primer_pago =  $primerPago->format('Y/m/d/');
                     $polizaPagos->cantidad_pagos = $prima['cantidad_pagos'];
                     $polizaPagos->sitio_pago = $prima['sitio_pago'];
                     $polizaPagos->centro_facturacion = $prima['centro_facturacion'];
                     $polizaPagos->direccion_pago = $prima['direccion_pago']; 
                     $polizaPagos->prima_anual =$prima["poliza_prima_anual"];
                     $polizaPagos->descuentos = $prima["poliza_descuentos"]; 
                     $polizaPagos->otros = $prima["poliza_otros"];
                     $polizaPagos->impuesto =$prima["poliza_impuesto"];
                     $polizaPagos->total =$prima["poliza_total"]; 
                     $polizaPagos->save();
                 }

                 $poliza->save();

                //Crear Acreedores
                 $fieldsetacre = array();
                 $campoacreedores = $this->input->post('campoacreedores');
                 $ids = array();
                //PolizasAcreedores::where("id_poliza", $poliza->id)->delete();
                 if($campoacreedores!=NULL){                        
                    $porcentaje_cesion = $this->input->post('campoacreedores_por');
                    $monto_cesion = $this->input->post('campoacreedores_mon');
                    $id_acreedores = $this->input->post('campoacreedores_id');                    
                    foreach ($campoacreedores as $key => $value) {
                        $fieldsetacre['acreedor'] = $value;
                        $fieldsetacre["id_poliza"] = $poliza->id;
                        $fieldsetacre["porcentaje_cesion"] = $porcentaje_cesion[$key];
                        $fieldsetacre["monto_cesion"] = $monto_cesion[$key];
                        if ($id_acreedores[$key] != "0") {
                            PolizasAcreedores::where("id", $id_acreedores[$key])->update($fieldsetacre); 
                            array_push($ids, $id_acreedores[$key]);
                        }else{
                            if ($value != "") {
                                $acre = PolizasAcreedores::create($fieldsetacre); 
                                array_push($ids, $acre->id );   
                            }
                        }                                                                               
                    }
                    PolizasAcreedores::whereNotIn("id", $ids)->delete();
                }

                $factura = FacturaSeguro::where(['id_poliza' => $poliza->id])->count();
                $fecha_creado = date('Y-m-d H:i:s');

                if ( ($poliza->estado == 'Facturada' && $poliza->poliza_declarativa == 'no' && $factura == 0) || ($poliza->estado == 'Facturada' && $poliza->poliza_declarativa == 'si') ) {


                    $array_factura['uuid_factura'] = Capsule::raw("ORDER_UUID(uuid())");
                    $array_factura['id_poliza'] = $poliza->id;
                    $array_factura['numero_poliza'] = $poliza->numero;
                    $array_factura['centro_contable_id'] = $poliza->centro_contable == 0 ? 19 : $poliza->centro_contable;
                    $array_factura['cliente_id'] = $poliza->cliente;
                    $usuario = Usuarios::findByUuid($this->id_usuario);
                    $array_factura['created_by'] = $usuario->id;
                    $array_factura['empresa_id'] = $this->empresa_id;
                    $array_factura['estado'] = "por_cobrar";
                    $array_factura['termino_pago'] = $poliza->primafk->frecuencia_pago;
                    $array_factura['formulario'] = "facturas_seguro";
                    $array_factura['centro_facturacion_id'] = $poliza->primafk->centro_facturacion;
                    $array_factura['bodega_id'] = 0;
                    $array_factura['formulario'] = 'facturas_seguro';

                    $prima_anual = $poliza->primafk->prima_anual;
                    $otros = $poliza->primafk->otros;
                    $valor_descuento = $poliza->primafk->descuentos;
                    $cantidad_pagos = $poliza->primafk->cantidad_pagos;
                    $impuesto_plan = Planes::where(['id' => $poliza->plan_id])->first();
                    $impuesto_plan = $impuesto_plan->impuestofk->impuesto/100;
                    $array_factura['porcentaje_impuesto'] = $impuesto_plan*100;

                    $total_prima = $poliza->primafk->total;
                    $total_impuesto = $poliza->primafk->impuesto;
                    $total_otros = $poliza->primafk->otros;

                    $prima_neta = $prima_anual ;
                    $prima_bruta = $prima_neta / $cantidad_pagos;

                    $array_factura['descuento'] = round(($valor_descuento / $cantidad_pagos), 2, PHP_ROUND_HALF_DOWN);
                    if($otros != 0){
                        $array_factura['otros'] = round( ($otros / $cantidad_pagos), 2, PHP_ROUND_HALF_DOWN);
                    }else{
                        $array_factura['otros'] = 0;
                    }

                    $fecha_inicio = new carbon($poliza->primafk->fecha_primer_pago);
                    $frecuencia_pagos = $poliza->primafk->frecuencia_pago;

                    if($poliza->poliza_declarativa == 'si'){
                        $cantidad_pagos = 1;
                    }

                    for ($i = 1; $i <= $cantidad_pagos; $i++) {

                        $codigo = $this->FacturaSeguroRepository->getLastCodigo(array('empresa_id' => $this->empresa_id));
                        $array_factura['codigo'] = $codigo;

                        $array_factura['subtotal'] = round($prima_bruta, 2,PHP_ROUND_HALF_DOWN);
                        $array_factura['impuestos'] = round( ($prima_bruta + $array_factura['otros'] - $array_factura['descuento']) * $impuesto_plan, 2 ,PHP_ROUND_HALF_DOWN);
                        $array_factura['total'] = round( $prima_bruta + $array_factura['otros'] + $array_factura['impuestos'] - $array_factura['descuento'] , 2 ,PHP_ROUND_HALF_DOWN);

                        if($poliza->poliza_declarativa == 'no'){

                            if ($i == $cantidad_pagos) {

                                $valor = $array_factura['subtotal'] * $cantidad_pagos;
                                if ($valor < $prima_neta) {
                                    $array_factura['subtotal'] = $array_factura['subtotal'] + ($prima_neta - $valor);
                                } elseif ($valor > $prima_neta) {
                                    $array_factura['subtotal'] = $array_factura['subtotal'] - ($valor - $prima_neta);
                                } else {
                                    $array_factura['subtotal'] = round($prima_bruta, 2,PHP_ROUND_HALF_DOWN);
                                }

                                $valor2 = $array_factura['total'] * $cantidad_pagos;
                                if($valor2 < $total_prima){
                                    $array_factura['total'] = $array_factura['total'] + ($total_prima - $valor2);
                                }else{
                                    $array_factura['total'] = $array_factura['total'] - ($valor2 - $total_prima);
                                }

                                $valor3 = $array_factura['impuestos'] * $cantidad_pagos;
                                if($valor3 < $total_impuesto){
                                    $array_factura['impuestos'] = $array_factura['impuestos'] + ($total_impuesto - $valor3);
                                }else{
                                    $array_factura['impuestos'] = $array_factura['impuestos'] - ($valor3 - $total_impuesto);
                                }

                                $valor4 = $array_factura['otros'] * $cantidad_pagos;
                                if($valor4 < $total_otros){
                                    $array_factura['otros'] = $array_factura['otros'] + ($total_otros - $valor4);
                                }else{
                                    $array_factura['otros'] = $array_factura['otros'] - ($valor4 - $total_otros);
                                }

                                $valor5 = $array_factura['descuento'] * $cantidad_pagos;
                                if($valor5 < $valor_descuento){
                                    $array_factura['descuento'] = $array_factura['descuento'] + ($valor_descuento - $valor5);
                                }else{
                                    $array_factura['descuento'] = $array_factura['descuento'] - ($valor5 - $valor_descuento);
                                }

                            }
                        }
                        $array_factura['saldo'] =  $array_factura['total'];

                        if($i > 1){
                            if($array_factura['total'] == 0){
                                $array_factura['estado'] = "cobrado_completo";
                            }else{
                                $array_factura['estado'] = "por_aprobar";
                            }
                        }elseif($array_factura['total'] == 0) {
                            $array_factura['estado'] = "cobrado_completo";
                        }

                        if ($frecuencia_pagos == "anual") {

                            $array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                            $fecha_final = $fecha_inicio->addYear(1)->subDay(1);
                            $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                            $fecha_inicio = $fecha_inicio->addDay(1);
                        } elseif ($frecuencia_pagos == "semestral") {

                            $array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                            $fecha_final = $fecha_inicio->addMonth(6)->subDay(1);
                            $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                            $fecha_inicio = $fecha_inicio->addDay(1);
                        } elseif ($frecuencia_pagos == "trimestral") {

                                /*$array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                                $fecha_final = $fecha_inicio->addDay(1);
                                $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                                $fecha_inicio = $fecha_inicio; *///->addDay(1);
                                
                                $array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                                $fecha_final = $fecha_inicio->addMonth(3)->subDay(1);
                                $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                                $fecha_inicio = $fecha_inicio->addDay(1);
                            } elseif ($frecuencia_pagos == "mensual") {

                                $array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                                $fecha_final = $fecha_inicio->addMonth(1)->subDay(1);
                                $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                                $fecha_inicio = $fecha_inicio->addDay(1);
                            }
                            if($i == 1){
                                if($array_factura['fecha_desde'] > date('d/m/Y')){
                                    $array_factura['estado'] = "por_aprobar"; 
                                }
                            }
                            
                            $factura = $this->FacturaSeguroRepository->crear($array_factura);
                            $factura_uuid = $this->FacturaSeguroRepository->find($factura->id);

                            if($factura->estado == "por_cobrar" ){

                                $Bitacora = new Flexio\Modulo\Polizas\Models\PolizasBitacora;
                                $comentario = "N° factura: " .$factura->codigo. "<br>Total: " . $factura->total . "<br>Fecha emisión: " . $factura->fecha_desde . "<br>Cuota ".$i." de ".$cantidad_pagos."<br><br><input type='hidden' id='uuid_factura' value='".$factura_uuid->uuid_factura."'><button class='btn btn-success btn-sm' type='button' id='descargar_factura' ><span class='hidden-xs hidden-sm hidden-m'>Descargar</span></button>";
                                $comment = ['comentario' => $comentario, 'usuario_id' => $array_factura['created_by'], 'comentable_id' => $array_factura['id_poliza'], 'comentable_type' => 'facturas_seguro', 'created_at' => $fecha_creado, 'empresa_id' => $this->id_empresa];
                                $Bitacora->create($comment);
                            }
                        }
                        return true;
                        exit();
                    } else {

                        return true;
                    }
                        //return true;
                }
                );


if ($response) {
   $campo = $this->input->post("campo");
   $this->session->set_userdata('updatedPoliza', $poliza->id);
   if ($campo["regreso"] == 'ase')
    redirect(base_url('aseguradoras/editar/' . $campo["regreso_valor"]));
else if ($campo["regreso"] == 'age')
    redirect(base_url('agentes/ver/' . $campo["regreso_valor"]));
else
    redirect(base_url('polizas/listar'));
}else {
                        //Establecer el mensaje a mostrar
   $data["mensaje"]["clase"] = "alert-danger";
   $data["mensaje"]["contenido"] = "La poliza ya tiene facturas generadas";
                        //$data["mensaje"]["contenido"] = "Hubo un error al tratar de editar la aseguradora.";
}
}

                //Introducir mensaje de error al arreglo
                //para mostrarlo en caso de haber error
$data["message"] = $mensaje;

$this->_css();
$this->_js();

$this->assets->agregar_js(array(
   'public/assets/js/modules/polizas/crear.vue.js',
   'public/assets/js/modules/intereses_asegurados/formulario.js'
   ));

$estado = $this->SegCatalogoRepository->listar_catalogo('estado_p', 'orden');
$estado = $estado->whereIn('key', array('polizas_pf', 'polizas_f'));

$estado_pol = $poliza->estado;

$cliente = PolizasCliente::where(['id_poliza' => $poliza->id])->first();
if(count($cliente) == 0){
   $cliente = '';
}
$aseguradora = Aseguradoras::where(['id' => $poliza->aseguradora_id])->get(array('id', 'nombre'));
if (count($aseguradora) == 0) {
   $aseguradora = '';
}
$plan = Planes::where(['id' => $poliza->plan_id])->get(array('nombre'));
$coberturas = PolizasCobertura::where(['id_poliza' => $poliza->id])->get();
$deducciones = PolizasDeduccion::where(['id_poliza' => $poliza->id])->get();

$comision = $poliza->comision;
$vigencia = PolizasVigencia::where(['id_poliza' => $poliza->id])->first();
if(count($vigencia) == 0){
   $vigencia = '';
}
$prima = PolizasPrima::where(['id_poliza' => $poliza->id])->first();
$centroFacturacion = centroModel::where(['id' => $prima->centro_facturacion])->first();
if ($centroFacturacion == '') {
   $centroFacturacion = '';
}
$participacion = PolizasParticipacion::where(['id_poliza' => $poliza->id])->get();
$totalParticipacion = PolizasParticipacion::where(['id_poliza' => $poliza->id])->sum('porcentaje_participacion');
if ($totalParticipacion == '') {
   $totalParticipacion = '';
}

$acreedores = PolizasAcreedores::where("id_poliza", $poliza->id)->get();
if (count($acreedores) == 0) {
    $acreedores = 'undefined';
}

if($poliza->centros != null){
   $id_centroContable = $poliza->centros->id;
   $nombre_centroContable = $poliza->centros->nombre;
}else{
   $id_centroContable = 0;
   $nombre_centroContable = '';
}
$ver_renovacion = 0;
$permiso_comision = 0;
$permiso_agente = 0;
$permiso_participacion = 0;

if($this->auth->has_permission('acceso', 'polizas/crear renovación') == true){
   $ver_renovacion = 1;
}

if($this->auth->has_permission('acceso', 'polizas/editar comisión plan') == true){
   $permiso_comision = 1;
}

if($this->auth->has_permission('acceso', 'polizas/editar agentes') == true){
   $permiso_agente = 1;
}

if($this->auth->has_permission('acceso', 'polizas/editar participación') == true){
   $permiso_participacion = 1;
}
$cantidad_pagos =    $this->SegInteresesAseguradosRepository->listar_catalogo('cantidad_pagos', 'orden');
$frecuencia_pagos = $this->SegInteresesAseguradosRepository->listar_catalogo('frecuencia_pagos', 'orden');
$metodo_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('metodo_pago', 'orden');
$sitio_pago =$this->SegInteresesAseguradosRepository->listar_catalogo('sitio_pago', 'orden');
$pagador = $this->SegInteresesAseguradosRepository->listar_catalogo('pagador_seguros', 'orden');
if($poliza->id_tipo_int_asegurado !=5){
 unset($pagador[1]);
}
$centrosFacturacion = centroModel:: where("cliente_id",$poliza->cliente)
->where("empresa_id",$this->empresa_id)->get();
$datosUsuarios = Usuarios::where(['id' => $this->usuario_id])->first();
if($datosUsuarios->filtro_centro_contable == "todos"){
    $centroContable = CentrosContables::where(['empresa_id' => $this->empresa_id])->get();
}else{
    $centrosContables = CentrosUsuario::where(['usuarios_has_centros.usuario_id' => $this->usuario_id, 'usuarios_has_centros.empresa_id' => $this->empresa_id])->join('cen_centros','cen_centros.id','=','usuarios_has_centros.centro_id')->get(array('cen_centros.id','cen_centros.nombre'));
    if(count($centrosContables) > 0){
        $centroContable = $centrosContables;
    }else{
        $centroContable = ''; 
    }
}
$clause['cli_clientes.empresa_id'] = $this->empresa_id;
$clause['cli_clientes.id'] = $poliza->cliente;
$group = clienteModel::join('grp_grupo_clientes', 'grp_grupo_clientes.uuid_cliente', '=', 'cli_clientes.uuid_cliente')
->join('grp_grupo', 'grp_grupo.id', '=', 'grp_grupo_clientes.grupo_id')
->where($clause)
->where('grp_grupo_clientes.deleted_at', '=', NULL)
->select('grp_grupo.nombre')
->get();

$agenteprincipaltotal=Agente::where('principal',1)->
where('id_empresa',$this->empresa_id)->count();

if($agenteprincipaltotal>0)
{
    $agenteprincipal=Agente::where('id_empresa',$this->empresa_id)->where('principal',1)->first();
    $agenteprincipalnombre=$agenteprincipal->nombre;
    $totalparticipacion=PolizasParticipacion::where('id_poliza',$poliza->id)->sum('porcentaje_participacion');
    $agtPrincipalporcentaje=number_format((100-$totalparticipacion),2);
}
else
{
    $agenteprincipalnombre="";
    $agtPrincipalporcentaje=0;
}

//---------------------------------------------------------------

$solicitudes_titulo = Ramos::find($poliza->ramo_id);
$ramo = $solicitudes_titulo->nombre;
$id_ramo = $solicitudes_titulo->id;
$idpadre = $solicitudes_titulo->padre_id;
$tipo_solicitud=Ramos::find($id_ramo)->first();
$indcolec = $tipo_solicitud->id_tipo_poliza;
$ramocadena=$ramo;

while ($idpadre != 0) {
    $ram = Ramos::where('id', $idpadre)->first();
    $id_ramo = $ram->id;
    $idpadre = $ram->padre_id;
    $ramocadena = $ram->nombre . "/" . $ramocadena;
}

$ram1 = Ramos::where('id', $id_ramo)->first();
$nombrepadre = $ram1->nombre;

if (strpos($ramocadena, "Vida")>-1) {
    $validavida = 1;
}else{
    $validavida = 0;
}

//---------------------------------------------------------------

//if ($poliza->renovacion_id != 0 && $poliza->categoria == 45 ) {
if($renovar == "renovar"){
    $catego = "renovada";
}else{
    $catego = "nueva";
}

//---------------------------------------------------------------

$this->assets->agregar_var_js(array(
   "vista" => isset($renovar)? $renovar:"editar",
   "agtPrincipal"=>$agenteprincipalnombre,
   "agtPrincipalporcentaje"=>$agtPrincipalporcentaje,
   "estado_solicitud" => $estado,
   "estado_pol" => $estado_pol,
   "poliza_id" => $poliza->id,
   "cliente" => $cliente,
   "aseguradora" => $aseguradora,
   "plan" => $plan,
   "coberturas" => $coberturas,
   "deducciones" => $deducciones,
   "comision" => $comision,
   "vigencia" => $vigencia,
   "prima" => $prima,
   "centroFacturacion" => $centroFacturacion,
   "participacion" => $participacion,
   "totalParticipacion" => $totalParticipacion,
   "id_tipo_int_asegurado" => $poliza->id_tipo_int_asegurado,
   "nombre_ramo" => $poliza->ramo, 
   "tipo_ramo" => $poliza->tipo_ramo,
   "ramo" => $poliza->ramo,
   "id_tipo_poliza" => $poliza->tipo_ramo == "colectivo" ? 2 : 1,
   "desde" => "poliza",
   "tablaTipo" => "vida",
   "permiso_editar" => "undefined",
   "validarenovacion" => $validarenovacion,
   "id_centroContable" => $id_centroContable,
   "nombre_centroContable" => $nombre_centroContable,
   "permiso_comision" => $permiso_comision,
   "permiso_agente"=>    $permiso_agente,
   "permiso_participacion" => $permiso_participacion,
   "cantidadPagos" => $cantidad_pagos,
   "frecuenciaPagos" => $frecuencia_pagos,
   "sitioPago" => $sitio_pago,
   "metodoPago" => $metodo_pago,
   "centrosFacturacion" =>$centrosFacturacion,
   "poliza_declarativa" => $poliza->poliza_declarativa,
   "grupo" => $group,
   "pagador" => $pagador,
   "centrosContables" => $centroContable,
   "validavida" => $validavida,
   "acreedores" => $acreedores,
   "contacre" => count($acreedores),
   "categoria_poliza" => $catego,
   "indcolec" => $indcolec
   ));

$isRenewal  = array('Expirada','Facturada');
$opciones   =array(
   "polizas/bitacora/" . strtoupper(bin2hex($poliza->uuid_polizas)) => "Bitacora",
   "#subir_documento" => "Subir Documento",
   "#imprimir_poliza" => "Imprimir",
   "#exportarPBtn" => "Exportar"
   );

foreach ($isRenewal as $key => $value) {
    if($estado_pol ==$value){
        $renewalUrl='polizas/editar/'.bin2hex($poliza->uuid_polizas).'/renovar';
        $opciones[$renewalUrl] = "Renovar póliza";
    }
}
$breadcrumb = array(
   "titulo" => '<i class="fa fa-book"></i> P&oacute;liza N° ' . $poliza->numero,
   "ruta" => array(
    0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
    1 => array("nombre" => '<a href="' . base_url() . 'polizas/listar">Pólizas</a>', "activo" => false),
    2 => array("nombre" => '<b>P&oacute;liza N° ' . $poliza->numero . '</b>', "activo" => true),
    ),
            "filtro" => false, //sin vista grid
            "menu" => array(
                'url' => 'javascipt:',
                'nombre' => "Acción",
                "opciones" => $opciones,
                ),
            "historial" => true,
            );


$data['subpanels'] = [];

$data["data"] = array(
   "campos" => array(
    "created_at" => $poliza->created_at,
    "uuid_polizas" => strtoupper(bin2hex($poliza->uuid_polizas)),
    "numero" => $poliza->numero,
    "empresa_id" => $poliza->empresa_id,
    "cliente" => $poliza->cliente,
    "ramo" => $poliza->ramo,
    "estado" => $poliza->estado,
    "inicio_vigencia" => $poliza->inicio_vigencia,
    "fin_vigencia" => $poliza->fin_vigencia,
    "politicas" => $this->politicas,
    "politicas_general" => $this->politicas_general,
    "validar_politicas" => $this->politicasgenerales
    )
   );

$this->template->agregar_titulo_header('Polizas');
$this->template->agregar_breadcrumb($breadcrumb);
$this->template->agregar_contenido($data);
$this->template->visualizar();
}

public function ajax_clientes_ac() {

        //$clientes = $clientes->like('nombre', "%".$_GET["term"]."%")->get();
    $clientes = $this->db->query("select id,nombre from cli_clientes where nombre like '%" . $_GET["term"] . "%' ");

    $data = array();
    $res = $clientes->result();
        //print($res);
    $id = 0;
    foreach ($res as $row_cliente) {
        $row = get_object_vars($row_cliente);
        $id++;
        $data[] = array(
            'id' => $id,
            'value' => $row["id"],
            'label' => strtoupper($row["nombre"])
            );
    }
    echo json_encode($data);
}

public function ajaxramos() {

    $ramos = Ramos::where(array("empresa_id" => $this->id_empresa))->get();

    $html = '';
    $res = $ramos->result();
        //print($res);
    $id = 0;
    foreach ($res as $row) {
        $html .= '<option value="' . $row["id"] . '">' . $row["nombre"] . '</option>';
    }
    echo ($html);
}

public function ajax_ramos_ac() {

    $clientes = $this->db->query("select id,nombre from seg_ramos where nombre like '%" . $_GET["term"] . "%' ");

    $data = array();
    $res = $clientes->result();
        //print($res);
    $id = 0;
    foreach ($res as $row_cliente) {
        $row = get_object_vars($row_cliente);
        $id++;
        $data[] = array(
            'id' => $id,
            'value' => $row["id"],
            'label' => strtoupper($row["nombre"])
            );
    }
    echo json_encode($data);
}

public function ajax_cambiar_estado_polizas() {
    $FormRequest = new Flexio\Modulo\Polizas\Models\GuardarPolizasEstado;

    try {

        $PolRepository = new PolizasRepository;
        $usuario = Usuario_orm::findByUuid($this->id_usuario);

        $campos = $_POST['campo'];
        $tipo = $campos['tipo'];
        $estado = $campos['estado'];
        $id_poliza = $campos['id_poliza'];
        $poliza = $campos['poliza'];
        $usuario = $usuario->id;
        $fecha_creado = date('Y-m-d H:i:s');

        $PolizaObj = new PolizasModel;
        $PolizaRes = $PolizaObj->find($id_poliza)->toArray();
        $estado_anterior = $PolizaRes["estado"];

        $msg = $FormRequest->guardar();
        $Bitacora = new Flexio\Modulo\Polizas\Models\PolizasBitacora;

        $comentario = "No. de Poliza: " . $poliza . "<br>Estado Actual: " . $estado . "<br>Estado Anterior: " . $estado_anterior . "<br>";

        $comment = ['comentario' => $comentario, 'usuario_id' => $usuario, 'comentable_id' => $id_poliza, 'comentable_type' => $tipo, 'created_at' => $fecha_creado, 'empresa_id' => $this->id_empresa];

        $bus = PolizasModel::find($id_poliza);

        $factura = FacturaSeguro::where(['id_poliza' => $bus->id])->count();

        if ( ($bus->estado == 'Facturada' && $bus->poliza_declarativa == 'no' && $factura == 0) || ($bus->estado == 'Facturada' && $bus->poliza_declarativa == 'si')  ) {


            $array_factura['uuid_factura'] = Capsule::raw("ORDER_UUID(uuid())");
            $array_factura['id_poliza'] = $bus->id;
            $array_factura['numero_poliza'] = $bus->numero;
            $array_factura['centro_contable_id'] = $bus->centro_contable == 0 ? 19 : $bus->centro_contable;
            $array_factura['cliente_id'] = $bus->cliente;
            $usuario = Usuarios::findByUuid($this->id_usuario);
            $array_factura['created_by'] = $usuario->id;
            $array_factura['empresa_id'] = $this->empresa_id;
            $array_factura['estado'] = "por_cobrar";
            $array_factura['termino_pago'] = $bus->primafk->frecuencia_pago;
            $array_factura['formulario'] = "facturas_seguro";
            $array_factura['centro_facturacion_id'] = $bus->primafk->centro_facturacion;
            $array_factura['bodega_id'] = 0;
            $array_factura['formulario'] = 'facturas_seguro';

            $prima_anual = $bus->primafk->prima_anual;
            $otros = $bus->primafk->otros;
            $valor_descuento = $bus->primafk->descuentos;
            $cantidad_pagos = $bus->primafk->cantidad_pagos;
            $impuesto_plan = Planes::where(['id' => $bus->plan_id])->first();
            $impuesto_plan = $impuesto_plan->impuestofk->impuesto/100;
            $array_factura['porcentaje_impuesto'] = $impuesto_plan*100;

            $total_prima = $bus->primafk->total;
            $total_impuesto = $bus->primafk->impuesto;
            $total_otros = $bus->primafk->otros;

            $prima_neta = $prima_anual ;
            $prima_bruta = $prima_neta / $cantidad_pagos;

            $array_factura['descuento'] = round(($valor_descuento / $cantidad_pagos), 2, PHP_ROUND_HALF_DOWN);
            if($otros != 0){
                $array_factura['otros'] = round($otros / $cantidad_pagos, 2, PHP_ROUND_HALF_DOWN);
            }else{
                $array_factura['otros'] = 0;//$otros;
            }

            $fecha_inicio = new carbon($bus->primafk->fecha_primer_pago);
            $frecuencia_pagos = $bus->primafk->frecuencia_pago;

            if($bus->poliza_declarativa == 'si'){
                $cantidad_pagos = 1;
            }

            for ($i = 1; $i <= $cantidad_pagos; $i++) {

                $codigo = $this->FacturaSeguroRepository->getLastCodigo(array('empresa_id' => $this->empresa_id));
                $array_factura['codigo'] = $codigo;

                $array_factura['subtotal'] = round($prima_bruta, 2, PHP_ROUND_HALF_DOWN);
                $array_factura['impuestos'] = round( ($prima_bruta + $array_factura['otros'] - $array_factura['descuento']) * $impuesto_plan, 2 ,PHP_ROUND_HALF_DOWN);
                $array_factura['total'] = round( $prima_bruta + $array_factura['otros'] + $array_factura['impuestos'] - $array_factura['descuento'] , 2 ,PHP_ROUND_HALF_DOWN);

                if($bus->poliza_declarativa == 'no'){
                    if ($i == $cantidad_pagos) {

                        $valor = $array_factura['subtotal'] * $cantidad_pagos;
                        if ($valor < $prima_neta) {
                            $array_factura['subtotal'] = $array_factura['subtotal'] + ($prima_neta - $valor);
                        } elseif ($valor > $prima_neta) {
                            $array_factura['subtotal'] = $array_factura['subtotal'] - ($valor - $prima_neta);
                        } else {
                            $array_factura['subtotal'] = round($prima_bruta, 2 ,PHP_ROUND_HALF_DOWN);
                        }

                        $valor2 = $array_factura['total'] * $cantidad_pagos;
                        if($valor2 < $total_prima){
                            $array_factura['total'] = $array_factura['total'] + ($total_prima - $valor2);
                        }else{
                            $array_factura['total'] = $array_factura['total'] - ($valor2 - $total_prima);
                        }

                        $valor3 = $array_factura['impuestos'] * $cantidad_pagos;
                        if($valor3 < $total_impuesto){
                            $array_factura['impuestos'] = $array_factura['impuestos'] + ($total_impuesto - $valor3);
                        }else{
                            $array_factura['impuestos'] = $array_factura['impuestos'] - ($valor3 - $total_impuesto);
                        }

                        $valor4 = $array_factura['otros'] * $cantidad_pagos;
                        if($valor4 < $total_otros){
                            $array_factura['otros'] = $array_factura['otros'] + ($total_otros - $valor4);
                        }else{
                            $array_factura['otros'] = $array_factura['otros'] - ($valor4 - $total_otros);
                        }

                        $valor5 = $array_factura['descuento'] * $cantidad_pagos;
                        if($valor5 < $valor_descuento){
                            $array_factura['descuento'] = $array_factura['descuento'] + ($valor_descuento - $valor5);
                        }else{
                            $array_factura['descuento'] = $array_factura['descuento'] - ($valor5 - $valor_descuento);
                        }
                    }
                }

                $array_factura['saldo'] =  $array_factura['total'];

                if($i > 1){
                    if($array_factura['total'] == 0){
                        $array_factura['estado'] = "cobrado_completo";
                    }else{
                        $array_factura['estado'] = "por_aprobar";
                    }
                }elseif($array_factura['total'] == 0) {
                    $array_factura['estado'] = "cobrado_completo";
                }

                if ($frecuencia_pagos == "anual") {

                    $array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                    $fecha_final = $fecha_inicio->addYear(1)->subDay(1);
                    $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                    $fecha_inicio = $fecha_inicio->addDay(1);

                } elseif ($frecuencia_pagos == "semestral") {

                    $array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                    $fecha_final = $fecha_inicio->addMonth(6)->subDay(1);
                    $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                    $fecha_inicio = $fecha_inicio->addDay(1);
                } elseif ($frecuencia_pagos == "trimestral") {

                    /*$array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                    $fecha_final = $fecha_inicio->addDay(1);
                    $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                    $fecha_inicio = $fecha_inicio;*///->addDay(1);
                    
                    $array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                    $fecha_final = $fecha_inicio->addMonth(3)->subDay(1);
                    $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                    $fecha_inicio = $fecha_inicio->addDay(1);
                } elseif ($frecuencia_pagos == "mensual") {

                    $array_factura['fecha_desde'] = $fecha_inicio->format('d/m/Y');
                    $fecha_final = $fecha_inicio->addMonth(1)->subDay(1);
                    $array_factura['fecha_hasta'] = $fecha_final->format('d/m/Y');
                    $fecha_inicio = $fecha_inicio->addDay(1);
                }
                if($i == 1){
                    if($array_factura['fecha_desde'] > date('d/m/Y')){
                        $array_factura['estado'] = "por_aprobar"; 
                    }
                }

                $factura = $this->FacturaSeguroRepository->crear($array_factura);
                $factura_uuid = $this->FacturaSeguroRepository->find($factura->id);

                if($factura->estado == "por_cobrar" ){

                    $Bitacora = new Flexio\Modulo\Polizas\Models\PolizasBitacora;
                    $comentario = "N° factura: " .$factura->codigo. "<br>Total: " . $factura->total . "<br>Fecha emisión: " . $factura->fecha_desde . "<br>Cuota ".$i." de ".$cantidad_pagos."<br><br><input type='hidden' id='uuid_factura' value='".$factura_uuid->uuid_factura."'><button class='btn btn-success btn-sm' type='button' id='descargar_factura' ><span class='hidden-xs hidden-sm hidden-m'>Descargar</span></button>";
                    $comment2 = ['comentario' => $comentario, 'usuario_id' => $array_factura['created_by'], 'comentable_id' => $id_poliza, 'comentable_type' => 'facturas_seguro', 'created_at' => $fecha_creado, 'empresa_id' => $this->id_empresa];
                    $Bitacora->create($comment2);
                }
            }
        }

        if ($bus->count() != 0) {
            $msg = $Bitacora->create($comment);
        }

            //$msg = $PolRepository->agregarComentario($id_poliza, $comment);
    } catch (\Exception $e) {
        $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
    }
    print json_encode($msg);
    exit;
}

function ajax_carga_comentarios_poliza() {
    $html = '';
    try {
        $id_poliza = $_POST["id_poliza"];

        $Bitacora = new PolizasBitacora;
        $historial = $Bitacora->where(array("comentable_id" => $id_poliza, "comentable_type" => "Comentario"))->with(array('usuario'))->orderBy("created_at", "desc")->get(array("comentario", "created_at", "usuario_id"))->toArray();
        foreach ($historial as $item) {

            $html .= '<div class="vertical-timeline-block">
            <div class="vertical-timeline-icon blue-bg">
                <i class="fa fa-comments-o"></i>
            </div>
            <div class="vertical-timeline-content" >
                <h2>Coment&oacute;</h2>
                <div>
                  ' . $item["comentario"] . '
              </div>
              <span class="vertical-date">
                  ' . $Bitacora->getCuantoTiempo($item["created_at"]) . '
                  <br>
                  <small>' . $item["created_at"] . '</small>
                  <div><small>' . $item["usuario"]["nombre"] . " " . $item["usuario"]["apellido"] . " " . $Bitacora->getHora($item["created_at"]) . '</small></div>
              </span>
          </div>
      </div>';
  }
  $data = array();

  $data["nid_poliza"] = $id_poliza;
} catch (\Exception $e) {
    $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
    
}
return $msg;
}

function ajax_guardar_comentario() {

    if (!$this->input->is_ajax_request()) {
        return false;
    }


    try {

        $Bitacora = new Flexio\Modulo\Polizas\Models\PolizasBitacora;
        $usuario = Usuario_orm::findByUuid($this->id_usuario);

        $tipo = "Comentario";
        $id_poliza = $this->input->post('nid_poliza');
        $comentario = $this->input->post('comentario');
        $usuario = $usuario->id;
        $fecha_creado = date('Y-m-d H:i:s');


        $comment = ['comentario' => $comentario, 'usuario_id' => $usuario, 'comentable_id' => $id_poliza, 'comentable_type' => $tipo, 'created_at' => $fecha_creado, 'empresa_id' => $this->id_empresa];

        $bus = PolizasModel::find($id_poliza);
        if ($bus->count() != 0) {
            $msg = $Bitacora->create($comment);
            exit;
        }
    } catch (\Exception $e) {
        $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
    }

    
}

function bitacora($uuid) {
    $poliza = new PolizasModel();
    $poliza = $poliza->where("uuid_polizas", "=", hex2bin(strtolower($uuid)))->first();

    $data = array();
    $Bitacora = new Flexio\Modulo\Polizas\Models\PolizasBitacora;
    $data["Bitacora"] = $Bitacora;
    $data["nid_poliza"] = $poliza->id;
    $data["historial"] = $Bitacora->where(array("comentable_id" => $poliza->id))->with(array('usuario'))->orderBy("created_at", "desc")->get(array("comentario", "created_at", "usuario_id", "comentable_type"))->toArray();


    $this->_css();
    $this->_js();


    $breadcrumb = array(
        "titulo" => '<i class="fa fa-line-chart"></i> Historial de Poliza',
            "filtro" => false, //sin vista grid
            "ruta" => array(
                0 => array(
                    "nombre" => "Seguros",
                    "activo" => false
                    ),
                1 => array(
                    "nombre" => 'Polizas',
                    "activo" => false,
                    "url" => "polizas/listar"
                    ),
                2 => array(
                    "nombre" => 'Poliza N°. ' . $poliza->numero,
                    "activo" => false,
                    "url" => "polizas/editar/" . $uuid
                    ),
                3 => array(
                    "nombre" => '<b>Bitácora</b>',
                    "activo" => true
                    )
                ),
            "menu" => array(
                "nombre" => "Volver",
                "url" => "polizas/listar",
                "opciones" => array(
                    "ImprimirBitacoraBtn" => "Imprimir"
                    )
                )
            );


    $data["data"] = array(
        "campos" => array(
            "created_at" => $poliza->created_at,
            "uuid_polizas" => strtoupper(bin2hex($poliza->uuid_polizas)),
            "numero" => $poliza->numero,
            "empresa_id" => $poliza->empresa_id,
            "cliente" => $poliza->cliente,
            "ramo" => $poliza->ramo,
            "estado" => $poliza->estado,
            "inicio_vigencia" => $poliza->inicio_vigencia,
            "fin_vigencia" => $poliza->fin_vigencia
            )
        );

    $this->template->agregar_titulo_header('Polizas');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
}

public function exportar() {
    if (empty($_POST)) {
        exit();
    }

    $ids = $this->input->post('ids', true);
    $id = explode(",", $ids);

    if (empty($id)) {
        return false;
    }
    $csv = array();
    $clause = array(
        "empresa_id" => $this->id_empresa
        );
    $clause['id'] = $id;

    $polizas = $this->PolizasRepository->exportar($clause, NULL, NULL, NULL, NULL);
    if (empty($polizas)) {
        return false;
    }
    $i = 0;
    foreach ($polizas AS $row) {

        $x = $row->facturasegurofk;
        $ids = array();
        foreach ($x as $value) {
            array_push($ids, $value['id']);
        }

        $cobros = $this->PolizasRepository->total_facturado_polizas($ids);
        $total = floatval($row->primafk->total);
        $cobros = floatval($cobros);
        $saldo = floatval($total - $cobros);  
        $saldo = number_format($saldo, 2);      

        $csvdata[$i]['numero'] = utf8_decode($row->numero)." ";
        $csvdata[$i]["cliente"] = utf8_decode(Util::verificar_valor($row->clientefk->nombre));
        $csvdata[$i]["aseguradora"] = utf8_decode(Util::verificar_valor($row->aseguradorafk->nombre));
        $csvdata[$i]["ramo"] = utf8_decode(Util::verificar_valor($row->ramo));
        $csvdata[$i]["inicio_vigencia"] = (Util::verificar_valor($row->inicio_vigencia));
        $csvdata[$i]["fin_vigencia"] = utf8_decode(Util::verificar_valor($row->fin_vigencia));
        $csvdata[$i]["frecuenciafk"] = ucwords(utf8_decode(Util::verificar_valor($row->frecuencia_facturacion)));
        $csvdata[$i]["ultima_factura"] = utf8_decode(Util::verificar_valor($row->ultima_factura));
        $csvdata[$i]["usuario"] = utf8_decode($row->usuariofk->nombre) . " " . utf8_decode($row->usuariofk->apellido);
        $csvdata[$i]["categoria"] = utf8_decode(Util::verificar_valor($row->categoriafk->valor));
        $csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->estado));
        $csvdata[$i]["total"] = utf8_decode(Util::verificar_valor(number_format($row->primafk->total, 2)));
        $csvdata[$i]["saldo"] = $saldo;
        $csvdata[$i]["fecha_renovacion"] = utf8_decode(Util::verificar_valor($row->fecha_renovacion));

        $i++;
    }

        //we create the CSV into memory
    $csv = Writer::createFromFileObject(new SplTempFileObject());
    $csv->insertOne([
        '# Poliza',
        'Cliente',
        'Aseguradora',
        'Ramo',
        'Fecha Ini. Vigencia',
        'Fecha Fin. Vigencia',
        'Frecuencia Factura',
        'Fecha Ult. Factura',
        'Usuario',
        'Categoria',
        'Estado',
        'Total a Pagar',
        'Saldo',
        'Fecha Renovacion',
        ]);
    $csv->insertAll($csvdata);
    $csv->output("polizas-" . date('ymd') . ".csv");
    exit();
}

public function formularioModal($data = NULL) {

    $this->assets->agregar_js(array(
                //'public/assets/js/modules/documentos/formulario.controller.js'
        ));

    $this->load->view('formularioModalDocumento', $data);
}

    /* public function exportarDocumentos() {
      if (empty($_POST)) {
      exit();
      }
      $ids = $this->input->post('ids', true);
      $id = explode(",", $ids);

      if (empty($id)) {
      return false;
      }
      $csv = array();

      $clause['id'] = $id;

      $documentos = $this->DocumentosRepository->exportar($clause, NULL, NULL, NULL, NULL);
      if (empty($documentos)) {
      return false;
      }
      $i = 0;
      foreach ($documentos AS $row) {

      $usuario = Usuario_orm::find($row->subido_por);

      $csvdata[$i]['nombre'] = $row->archivo_nombre;

      if (!empty($row->archivo_nombre)) {
      $info1 = new SplFileInfo($row->archivo_nombre);
      $info = $info1->getExtension();

      if ($info == "png" || $info == "jpg" || $info == "gif" || $info == "jpeg" || $info == "bmp" || $info == "ai" || $info == "crd" || $info == "dwg" || $info == "svg") {
      $tipo = "Imagen";
      } else if ($info == "doc" || $info == "docx" || $info == "dot" || $info == "rtf") {
      $tipo = "Documento";
      } else if ($info == "xls" || $info == "xlsx") {
      $tipo = "Datos";
      } else if ($info == "ppt" || $info == "pps" || $info == "pptx" || $info == "ppsx") {
      $tipo = "Presentación";
      } else if ($info == "pdf") {
      $tipo = "PDF";
      } else
      $tipo = "";
      }
      else {
      $tipo = "";
      }
      $csvdata[$i]["tipo"] = $tipo;
      $csvdata[$i]["fecha_creacion"] = $row->created_at;
      $csvdata[$i]["subido_por"] = $usuario->nombre . " " . $usuario->apellido;
      $i++;
      }
      //we create the CSV into memory
      $csv = Writer::createFromFileObject(new SplTempFileObject());
      $csv->insertOne([
      'Nombre',
      'Tipo',
      utf8_decode('Fecha Creación'),
      'Usuario'
      ]);
      $csv->insertAll($csvdata);
      $csv->output("documentos-" . date('ymd') . ".csv");
      exit();
  } */

  function ajax_guardar_documentos() {
    if (empty($_POST)) {
        return false;
    }

    $id_poliza = $this->input->post('id_poliza', true);
    $modeloInstancia = $this->polizasModel->find($id_poliza);
    $this->documentos->subir($modeloInstancia);

    $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado los documentos satisfactoriamente');
    $this->session->set_flashdata('mensaje', $mensaje);

        //echo $modeloInstancia->uuid_polizas;
    redirect(base_url('polizas/editar/' . bin2hex($modeloInstancia->uuid_polizas)));
}

function tabladetalles($data = array()) {
        /* $this->assets->agregar_var_js(array(
          "modulo_id" => 57,
          )); */
          $this->load->view('tabladetalles', $data);
      }

      public function formularioModalEditar($data = NULL) {

        $this->assets->agregar_var_js(array(
            "numero" => "",
            'data' => "",
            ));

        $this->load->view('formularioModalDocumentoEditar');
    }

    public function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/modules/polizas/crear.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/polizas/formulario_comentario.js',
            'public/assets/js/modules/polizas/vue.comentario.js',
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
            'public/assets/js/default/grid.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/modules/polizas/routes.js',
            'public/assets/js/modules/polizas/tabla.js',
            'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            ));
    }

    public function _css() {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/modules/stylesheets/aseguradoras.css',
            'public/assets/css/modules/stylesheets/polizas.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            ));
    }

    public function tablapolizas_agt($data = array()) {
        //If ajax request

        $this->assets->agregar_js(array(
            'public/assets/js/modules/polizas/tablatab.js'
        )); //'public/assets/js/modules/aseguradoras/tabla_ramos.js'
        //$this->aseguradora_id = $data['uuid_aseguradora'];

        $this->load->view('tablatab_agt', $data);
    }

    public function ajax_listar_polizas_agt($grid = NULL) {

      $clause = array(
        "usuario_id" => $this->usuario_id
        );
      
      if ($this->input->post('uuid_asegura') != "") {
        $aseguradora = $this->AseguradorasRepository->findByUuid($this->input->post('uuid_asegura'));
        $clause['aseguradora_id'] = $aseguradora->id;
        $clause['agente_id'] = "";
        $atras = "ase";
        $val = $this->input->post('uuid_asegura');
    } else if ($this->input->post('uuid_agente') != "") {
        $agt = Agente::findByUuid($this->input->post('uuid_agente'));
        $clause['agente_id'] = $agt->id;
        $atras = "age";
        $val = $this->input->post('uuid_agente');
        $clause['aseguradora_id'] = "";
    }

        //**************************************************
        // clause modulo clientes detalle tab solicitudes
        //**************************************************
    $numero = $this->input->post('numero', true);
    $cliente = $this->input->post('cliente', true);
    $aseguradora = $this->input->post('aseguradora', true);
    $ramo = $this->input->post('ramo', true);
    $inicio_vigencia = $this->input->post('inicio_vigencia', true);
    $fin_vigencia = $this->input->post('fin_vigencia', true);
    $fecha_creacion = $this->input->post('fecha_creacion', true);
    $usuario = $this->input->post('usuario', true);
    $estado = $this->input->post('estado', true);

    if (!empty($numero)) {
        $clause["num_poliza"] = $numero;
    }
    if (!empty($cliente)) {
        $clause["cliente"] = $cliente;
    }
    if (!empty($aseguradora)) {
        $clause["aseguradora"] = $aseguradora;
    }
    if (!empty($ramo)) {
        $clause["ramo"] = $ramo;
    }
    if (!empty($inicio_vigencia)) {
        $clause["inicio_vigencia"] = $inicio_vigencia;
    }
    if (!empty($fin_vigencia)) {
        $clause["fin_vigencia"] = $fin_vigencia;
    }
    if (!empty($fecha_creacion)) {
        $fecha_inicio = date("Y-m-d", strtotime($fecha_creacion));
        $clause["fecha_creacion"] = array('=', $fecha_inicio);
    }
    if (!empty($estado)) {
        $clause["estado"] = $estado;
    }
    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    $count = PolizasModel::listar_polizas_agt($clause, NULL, NULL, NULL, NULL)->count();

    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    $rows = PolizasModel::listar_polizas_agt($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $response->result = array();
    $i = 0;

    if (!empty($rows)) {
        foreach ($rows AS $i => $row) {

            $uuid_polizas = bin2hex($row->uuid_polizas);
            $url = base_url("polizas/editar/$uuid_polizas?reg=" . $atras . '&val=' . $val);
                //$hidden_options = ""; 
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                //$hidden_options .= '<a href="'. base_url('colaboradores/ver/'. $uuid_colaborador) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';

            $hidden_options = '<a href="' . $url . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarSolicitud" >Ver Poliza</a>';
                /* $hidden_options .= '<button data-id="' . $row['id'] . '" id="cambio_estado_solicitud" class="btn btn-block btn-outline btn-success " data-type="" data-estado="' . $row->estado . '" >Cambio de Estado</button>';
                  $hidden_options .= $row->estado == "Anulada" ? '' : ($row->estado == "Aprobada" ? '' : ($row->estado == "Rechazada" ? '' : '<a href="javascript:" data-id="' . $row['id'] . '" data-solicitud="' . $row->numero . '" data-cliente="' . $row->cliente->nombre . '" class="btn btn-block btn-outline btn-success anular_solicitud" data-type="' . $row['id'] . '" >Anular</a>' ));
                  $hidden_options .= '<a href="' . $urlbitacora . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success bitacora_solicitud" data-type="' . $row['id'] . '" >Bitácora</a>';
                  $hidden_options = '<a href="" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success subir_archivos_solicitudes" data-type="' . $row['id'] . '" >Subir Archivos</a>'; */

                  $estado_color = $row->estado == "En Trámite" ? 'background-color: #F8AD46' : ($row->estado == "Aprobada" ? 'background-color: #5cb85c' : ($row->estado == "Rechazada" ? 'background-color: #fc0d1b' : ($row->estado == "Anulada" ? 'background-color: #000000' : 'background-color: #5bc0de')));
                  $modalstate = "";

                  $response->rows[$i]["id"] = $row->id;
                  $response->rows[$i]["cell"] = array(
                    $row->id,
                    "<a href='".$url."' >".$row->numero."</a>",
                    $row->cliente,
                    $row->aseguradora,
                    $row->ramo,
                    $row->inicio_vigencia,
                    $row->fin_vigencia,
                    $row->fecha_creacion,
                    !empty($row->estado) ? '<span style="color:white; ' . $estado_color . '" class="btn btn-xs btn-block estadoSolicitudes" data-id="' . $row['id'] . '" data-solicitudEstado="' . $row->estado . '">' . $row->estado . '</span>' : "",
                    $link_option,
                    $hidden_options,
                    $modalstate
                    );
                  $i++;
              }
          }
          echo json_encode($response);
          exit;
      }

      function ocultotablaintereses($uuid = 0) {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/polizas/tablaintereses.js'
            ));
        /* $uuid = hex2bin($uuid);
          $pol = $this->polizasModel->where("uuid_polizas",$uuid)->first()->toArray();
          $this->assets->agregar_var_js(array(
          'solicitud' => $pol["solicitud"],
          )); */

          $this->load->view('tablaintereses');
      }

      function ocultotablarenovaciones($uuid = 0) {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/polizas/tablarenovaciones.js'
            ));
        /* $uuid = hex2bin($uuid);
          $pol = $this->polizasModel->where("uuid_polizas",$uuid)->first()->toArray();
          $this->assets->agregar_var_js(array(
          'solicitud' => $pol["solicitud"],
          )); */

          $this->load->view('tablarenovaciones');
      }

      public function ocultoTabEndosos(){

        $this->assets->agregar_js(array(
            'public/assets/js/modules/polizas/tablaTabEndosos.js',
            ));

        $this->load->view('tablaEndosos');

    }

    public function ajax_listar_intereses($grid = NULL) {

        $clause = array(
            "empresa_id" => $this->id_empresa
            );

        if (isset($_POST["filters"])) {
            $filt = (array) json_decode($_POST["filters"]);
            if (isset($filt["rules"]) AND count($filt["rules"]) > 0) {
                for ($i = 0; $i < count($filt["rules"]); $i++) {
                    $busq = (array) $filt["rules"][$i];
                    if (isset($busq["field"]) AND $busq["data"] != "") {
                        $clause[$busq["field"]] = array("like", "%" . $busq["data"] . "%");
                    }
                }
            }
        }

        $uuid_poliza = $this->input->post('uuid_poliza', true);

        $pol = $this->polizasModel->where("uuid_polizas", hex2bin($uuid_poliza))->first()->toArray();
        $solicitud = $pol["solicitud"];

        $sol = $this->solicitudesModel->where('numero', $solicitud)->first();
        $id_sol = $sol["id"];

        if (!empty($id_sol)) {
            $clause["int_intereses_asegurados_detalles.id_solicitudes"] = array('=', $id_sol);
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->PolizasRepository->listar_intereses_asegurados($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = $this->PolizasRepository->listar_intereses_asegurados($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        $rutaAlmacenamiento = array();
        $rutaAlmacenamiento = explode("/", $_SERVER['HTTP_REFERER']);
        $totalRuta = count($rutaAlmacenamiento)-1;
        $rutallamado = $rutaAlmacenamiento[$totalRuta-2]."/".$rutaAlmacenamiento[$totalRuta-1];

        if (!empty($rows)) {
            foreach ($rows AS $i => $row) {
                $uuid_intereses = bin2hex($row->uuid_intereses);
                $now = Carbon::now();
                $btnClass = $row->estado !== "Activo" ? "successful" : "danger";
                $negativeState = $row->estado != "Activo" ? "Activar" : "Desactivar";

                $modalstate = '';

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $estado = $row->estado === "Activo" ? "Activo" : "Inactivo";
                $labelClass = $row->estado === "Activo" ? "successful" : "danger";

                $url = base_url("intereses_asegurados/editar/$uuid_intereses");

                if ($rutallamado == "polizas/editar") {
                    $url .= "?reg=poli&val=".$uuid_poliza;
                }

                $hidden_options = '<a href="' . $url . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarInteres"  target="_blank">Ver interés asegurado</a>';

                $redirect = "<a style='text-decoration: underline' href=" . $url . " target='_blank'>$row->numero</a>";
                $id = $row->id;
                $response->rows[$i]["cell"] = array(
                    "id" => $id,
                    "numero" => $redirect,
                    "seg_ramos_tipo_interes.nombre" => $row->etiqueta,
                    "int_intereses_asegurados_detalles.fecha_inclusion" => $row->fecha_inclusion,
                    "int_intereses_asegurados_detalles.fecha_exclusion" => $row->fecha_exclusion,
                    "usuarios.nombre" => $row->nombre . " " . $row->apellido,
                    "estado" => "<label class='label label-$labelClass estadoInteres' data-id='$id' >$estado</label>",
                    "options" => $link_option,
                    "link" => $hidden_options,
                    "modalstate" => $modalstate,
                    "massState" => $estado
                    );
                $i++;
            }
        }
        echo json_encode($response);
        exit;
    }

    public function exportarInteresesPolizas() {
        if (empty($_POST)) {
            exit();
        }

        $ids = $this->input->post('ids', true);
        $uuid = $this->input->post('solicitud', true);

        $uuid = hex2bin($uuid);
        $pol = $this->polizasModel->where("uuid_polizas", $uuid)->first()->toArray();
        $solicitud = $pol["solicitud"];


        $csv = array();
        $clause = array(
            "empresa_id" => $this->id_empresa
            );

        $sol = $this->solicitudesModel->where('numero', $solicitud)->first();
        $id_sol = $sol["id"];
        if (empty($id_sol)) {
            exit();
        }
        $clause["id_solicitudes"] = $id_sol;
        $id = explode(",", $ids);
        if (empty($id)) {
            return false;

        }

        $clause['id'] = $id;

        $intereses = $this->PolizasRepository->exportarInteresesPolizas($clause, NULL, NULL, NULL, NULL);
        if (empty($intereses)) {
            return false;
        }
        $i = 0;
        foreach ($intereses AS $row) {
            //$csvdata[$i]['id'] = $row->id;
            $csvdata[$i]["numero"] = $row->numero;
            $csvdata[$i]["interesestable_type"] = $row->tipo->etiqueta;
            $csvdata[$i]["identificacion"] = $row->identificacion;
            $csvdata[$i]["estado"] = $row->estado;
            $csvdata[$i]["fecha_inclusion"] = $row->fecha_inclusion;
            $csvdata[$i]["fecha_exclusion"] = $row->fecha_exclusion;

            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'Numero',
            'Tipo',
            'Identificacion',
            'Estado',
            'Fecha inclusion',
            'Fecha exclusion',
            ]);
        $csv->insertAll($csvdata);
        $csv->output("interesesPolizas-" . date('ymd') . ".csv");
        exit();
    }

    public function ajax_listar_renovaciones($grid = NULL) {

        $clause = array(
            "empresa_id" => $this->id_empresa
            );

        if (isset($_POST["filters"])) {
            $filt = (array) json_decode($_POST["filters"]);
            if (isset($filt["rules"]) AND count($filt["rules"]) > 0) {
                for ($i = 0; $i < count($filt["rules"]); $i++) {
                    $busq = (array) $filt["rules"][$i];
                    if (isset($busq["field"]) AND $busq["data"] != "") {
                        $clause[$busq["field"]] = array("like", "%" . $busq["data"] . "%");
                    }
                }
            }
        }



        $uuid_poliza = $this->input->post('uuid_poliza', true);
        $pol = $this->polizasModel->where("uuid_polizas", hex2bin($uuid_poliza))->first()->toArray();
        $clause['pol_polizas.id'] = $pol['id'];

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->PolizasRepository->listar_renovaciones_asegurados($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = $this->PolizasRepository->listar_renovaciones_asegurados($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows)) {
            foreach ($rows AS $i => $row) {
                $uuid_polizas = bin2hex($row->uuid_polizas);
                //$now = Carbon::now();

                $modalstate = '';

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $url = base_url("polizas/editar/$uuid_polizas");
                $hidden_options = '<a href="' . $url . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarPoliza"  target="_blank">Ver póliza</a>';

                $redirect = "<a style='text-decoration: underline' href=" . $url . " >$row->numero</a>";
                $fecha_renovacion = explode(" ", $row->updated_at);
                $id = $row->id;
                $response->rows[$i]["cell"] = array(
                    "id" => $id,
                    "pol_polizas.numero" => $redirect,
                    "pol_polizas.inicio_vigencia" => $row->inicio_vigencia,
                    "pol_polizas.fin_vigencia" => $row->fin_vigencia,
                    "pol_polizas.updated_at" => $fecha_renovacion[0],
                    "usuarios.nombre" => $row->nombre . " " . $row->apellido,
                    "options" => $link_option,
                    "link" => $hidden_options,
                    "modalstate" => $modalstate,
                    "massState" => ''
                    );
                $i++;
            }
        }
        echo json_encode($response);
        exit;
    }

    public function exportarRenovacionesPolizas() {
        if (empty($_POST)) {
            exit();
        }

        $ids = $this->input->post('ids', true);
        $uuid = $this->input->post('solicitud', true);

        $csv = array();
        
        $id = explode(",", $ids);        

        $clause['pol_polizas.id'] = $id;

        $polizas = $this->PolizasRepository->exportarRenovacionesPolizas($clause, NULL, NULL, NULL, NULL);
        if (empty($polizas)) {
            return false;
        }
        $i = 0;
        foreach ($polizas AS $row) {
            //$csvdata[$i]['id'] = $row->id;
            $fecha_renovacion = explode(" ", $row->updated_at);

            $csvdata[$i]["numero"] = $row->numero;
            $csvdata[$i]["inicio_vigencia"] = $row->inicio_vigencia;
            $csvdata[$i]["fin_vigencia"] = $row->fin_vigencia;
            $csvdata[$i]["updated_at"] = $fecha_renovacion[0];
            $csvdata[$i]["nombre"] = $row->nombre." ".$row->apellido;

            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'Numero',
            'Inicio de Vigencia',
            'Fin de Vigencia',
            'Fecha de Renovacion',
            'Usuario',
            ]);
        $csv->insertAll($csvdata);
        $csv->output("renovacionesPolizas-" . date('ymd') . ".csv");
        exit();
    }

    public function imprimirPoliza($id_poliza = null) {

        if ($id_poliza == null) {
            return false;
        }

        $poliza = PolizasModel::where(['id' => $id_poliza])->first();
        $comision = $poliza->comision;
        $vigencia = PolizasVigencia::where(['id_poliza' => $poliza->id])->first();
        $prima = PolizasPrima::where(['id_poliza' => $poliza->id])->first();
        $centroFacturacion = centroModel::where(['id' => $prima->centro_facturacion])->first();
        $totalParticipacion = PolizasParticipacion::where(['id_poliza' => $poliza->id])->sum('porcentaje_participacion');

        if ($poliza->tipo_ramo == "colectivo") {
            if ($poliza->id_tipo_int_asegurado == 1) {
                $interes_asegurado = PolizasArticulo::listar_articulo_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            } elseif ($poliza->id_tipo_int_asegurado == 2) {
                $interes_asegurado = PolizasCarga::listar_carga_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            } elseif ($poliza->id_tipo_int_asegurado == 3) {
                $interes_asegurado = PolizasAereo::listar_aereo_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            } elseif ($poliza->id_tipo_int_asegurado == 4) {
                $interes_asegurado = PolizasMaritimo::listar_maritimo_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            } elseif ($poliza->id_tipo_int_asegurado == 5) {
                $interes_asegurado = PolizasPersonas::listar_personas_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            } elseif ($poliza->id_tipo_int_asegurado == 6) {
                $interes_asegurado = PolizasProyecto::listar_proyecto_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            } elseif ($poliza->id_tipo_int_asegurado == 7) {
                $interes_asegurado = PolizasUbicacion::listar_ubicacion_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            } elseif ($poliza->id_tipo_int_asegurado == 8) {
                $interes_asegurado = PolizasVehiculo::listar_vehiculo_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            }
        } else {
            if ($poliza->id_tipo_int_asegurado == 5) {
                $interes_asegurado = PolizasPersonas::listar_personas_provicional(NULL, NULL, NULL, NULL, NULL, $poliza->id);
            } else {
                $interes_asegurado = '';
            }
        }


        $nombre = $poliza->numero;
        $formulario = "formularioPoliza";

        $data = ['datos' => $poliza, 'centro_facturacion' => $centroFacturacion, 'total_participacion' => $totalParticipacion, 'interes_asegurado' => $interes_asegurado];
        $dompdf = new Dompdf();
        $html = $this->load->view('pdf/' . $formulario, $data, true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($nombre, array("Attachment" => false));
        exit(0);
    }

    function formulariointereses($data = array()) {
        /* $clause = array('empresa_id' => $this->empresa_id);        
          $this->assets->agregar_var_js(array(
          ));
         */

          if ($this->auth->has_permission('editar__cambiarEstado', 'intereses_asegurados/editar/(:any)') == true) {
            $cestado = 1;
        } else {
            $cestado = 0;
        }
        /*
          if ($this->auth->has_permission('acceso', 'solicitudes/editar participación') == true) {
          $editarParticipacion = 1;
          } else {
          $editarParticipacion = 0;
      } */

      $this->assets->agregar_js(array(
                //'public/assets/js/modules/intereses_asegurados/formulario.js',
                //'public/assets/js/modules/intereses_asegurados/crear.js',
                //'public/assets/js/default/vue-validator.min.js',   
        ));

      $formulario = null;

      if ($formulario != NULL) {
        $this->assets->agregar_var_js(array(
            "formulario_seleccionado" => $formulario,
            "permiso_cambio_estado" => $cestado,
            ));
    } else {
        $this->assets->agregar_var_js(array(
            "permiso_cambio_estado" => $cestado,
            "formulario_seleccionado" => 'formcasco_aereo',
            ));
    }

    if ($data['id_tipo_interes'] == 1) {
        $tabla = "articulo";
    } elseif ($data['id_tipo_interes'] == 2) {
        $tabla = "carga";
    } elseif ($data['id_tipo_interes'] == 3) {
        $tabla = "casco_aereo";
    } elseif ($data['id_tipo_interes'] == 4) {
        $tabla = "casco_maritimo";
    } elseif ($data['id_tipo_interes'] == 5) {
        $tabla = "persona";
    } elseif ($data['id_tipo_interes'] == 6) {
        $tabla = "proyecto_actividad";
    } elseif ($data['id_tipo_interes'] == 7) {
        $tabla = "ubicacion";
    } elseif ($data['id_tipo_interes'] == 8) {
        $tabla = "vehiculo";
    }

    $data["campos"] = array(
        "campos" => array(
            "tipos_intereses_asegurados" => $this->InteresesAsegurados_catModel->get(),
            "tipo_interes" => $tabla
            ),
        );
    $this->load->view('formularioIntereses', $data);
}

function ajax_get_tipointereses() {

    $interes = $_POST['interes'];
    $id_poliza = $_POST['id_poliza'];

    $interes = str_replace("Tab", "", $interes);
    if ($interes == "articulo") {
        $tbl = new PolizasArticulo();
    } else if ($interes == "carga") {
        $tbl = new PolizasCarga();
    } else if ($interes == "casco_aereo") {
        $tbl = new PolizasAereo();
    } else if ($interes == "casco_maritimo") {
        $tbl = new PolizasMaritimo();
    } else if ($interes == "persona") {
        $tbl = new PolizasPersonas();
    } else if ($interes == "proyecto_actividad") {
        $tbl = new PolizasProyecto();
    } else if ($interes == "ubicacion") {
        $tbl = new PolizasUbicacion();
    } else if ($interes == "vehiculo") {
        $tbl = new PolizasVehiculo();
    }


    $inter = $tbl->where(['id_poliza' => $id_poliza])->get();
    $response = new stdClass();
    $response->inter = array();
    foreach ($inter as $key => $value) {
        if ($interes == "articulo") {
            $v = $value->nombre . " - " . $value->numero_serie . " (" . $value->numero . ")";
        } else if ($interes == "carga") {
            $v = $value->no_liquidacion . " (" . $value->numero . ")";
        } else if ($interes == "casco_aereo") {
            $v = $value->serie . " (" . $value->numero . ")";
        } else if ($interes == "casco_maritimo") {
            $v = $value->serie . " (" . $value->numero . ")";
        } else if ($interes == "persona") {
            $v = $value->nombrePersona . " - " . $value->identificacion . " (" . $value->numero . ")";
        } else if ($interes == "proyecto_actividad") {
            $v = $value->nombre_proyecto . " - " . $value->no_orden . " (" . $value->numero . ")";
        } else if ($interes == "ubicacion") {
            $v = $value->nombre . " - " . $value->direccion . " (" . $value->numero . ")";
        } else if ($interes == "vehiculo") {
            $v = $value->chasis . " (" . $value->numero . ")";
        }
        array_push($response->inter, array("id" => $value->id, "numero" => $v));
    }
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($response))->_display();

    exit;
}

function ajax_get_intereses() {

    $interes = $_POST['interes'];
    if ($interes == "") {
        $interes = "0";
    }
    $tipointeres = $_POST['tipointeres'];
    $tipointeres = str_replace("Tab", "", $tipointeres);
    if ($tipointeres == "articulo") {
        $tipo = 1;
        $tbl = new PolizasArticulo();
    } else if ($tipointeres == "carga") {
        $tipo = 2;
        $tbl = new PolizasCarga();
    } else if ($tipointeres == "casco_aereo") {
        $tipo = 3;
        $tbl = new PolizasAereo();
    } else if ($tipointeres == "casco_maritimo") {
        $tipo = 4;
        $tbl = new PolizasMaritimo();
    } else if ($tipointeres == "persona") {
        $tipo = 5;
        $tbl = new PolizasPersonas();
        $tipointeres = "personas";
    } else if ($tipointeres == "proyecto_actividad") {
        $tipo = 6;
        $tbl = new PolizasProyecto();
    } else if ($tipointeres == "ubicacion") {
        $tipo = 7;
        $tbl = new PolizasUbicacion();
    } else if ($tipointeres == "vehiculo") {
        $tipo = 8;
        $tbl = new PolizasVehiculo();
    }

    $inter = $tbl->where(['id' => $interes])->first();

    $response = new stdClass();
    $response->inter = array();
    $response->inter = $inter->toArray();
    if ($tipo != 2 && $tipo != 3 && $tipo != 5) {
            //$response->inter ['uuid_'.$tipointeres] = bin2hex($response->inter ['uuid_'.$tipointeres]);
        $tipointeres = str_replace("_actividad", "", $tipointeres);
        $response->inter ['uuid_' . $tipointeres] = "";
    }
    $response->inter ['tipointeres'] = $tipo;
        //$response->inter ['uuid_intereses'] = bin2hex($response->inter ['uuid_intereses']);

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($response))->_display();

    exit;
}

public function ajax_listar_articulo($grid = NULL) {

    $estado = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);
    $clause = array(
        "numero" => $this->input->post('numero', true),
        "nombre" => $this->input->post('nombre', true),
        "clase_equipo" => $this->input->post('clase_equipo', true),
        "marca" => $this->input->post('marca', true),
        "modelo" => $this->input->post('modelo', true),
        "anio" => $this->input->post('anio', true),
        "numero_serie" => $this->input->post('numero_serie', true),
        "id_condicion" => $this->input->post('id_condicion', true),
        "valor" => $this->input->post('valor', true),
        "fecha" => $this->input->post('fecha', true),
        );


    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = PolizasArticulo::listar_articulo_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $rows = PolizasArticulo::listar_articulo_provicional($clause, $sidx, $sord, $limit, $start, $id_poliza);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i = 0;
    if (!empty($rows->toArray())) {
        foreach ($rows->toArray() AS $i => $row) {

            if ($row['estado'] == 'Inactivo')
                $spanStyle = 'label label-danger';
            else if ($row['estado'] == 'Activo')
                $spanStyle = 'label label-successful';
            else
                $spanStyle = 'label label-warning';

            $hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoArticulo' data-int-gr='" . $row["id"] . "' data-int-id='" . $row["id"] . "'>Ver Inter&eacute;s</a>";
                //$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses' data-int-id='" . $row["id"] . "' ' >Subir Documento</a>";
                //$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $response->rows[$i]["id"] = $row['id'];
            $response->rows[$i]["cell"] = array(
                $row["numero"],
                $row['nombre'],
                $row['clase_equipo'],
                $row['marca'],
                $row['modelo'],
                $row['anio'],
                $row['numero_serie'],
                $row['id_condicion'],
                $row['valor'],
                $row['fecha_inclusion'],
                    '', //$row['fecha_exclusion'],
                    "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                    $link_option,
                    $hidden_options
                    );
            $i++;
        }
    }
    print(json_encode($response));
    exit;
}

public function ajax_listar_carga($grid = NULL) {

    $estado = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);

    $clause = array(
        "numero" => $this->input->post('numero', true),
        "no_liquidacion" => $this->input->post('no_liquidacion', true),
        "fecha_despacho" => $this->input->post('fecha_despacho', true),
        "fecha_arribo" => $this->input->post('fecha_arribo', true),
        "medio_transporte" => $this->input->post('medio_transporte', true),
        "valor" => $this->input->post('valor', true),
        "origen" => $this->input->post('origen', true),
        "destino" => $this->input->post('destino', true),
        "fecha_inclusion" => $this->input->post('fecha_inclusion', true),
        );


    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = PolizasCarga::listar_carga_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $rows = PolizasCarga::listar_carga_provicional($clause, $sidx, $sord, $limit, $start, $id_poliza);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i = 0;

    if (!empty($rows->toArray())) {
        foreach ($rows->toArray() AS $i => $row) {

            if ($row['estado'] == 'Inactivo')
                $spanStyle = 'label label-danger';
            else if ($row['estado'] == 'Activo')
                $spanStyle = 'label label-successful';
            else
                $spanStyle = 'label label-warning';

            $hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoCarga' data-int-gr='" . $row["id"] . "' data-int-id='" . $row["id"] . "'>Ver Inter&eacute;s</a>";

                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $response->rows[$i]["id"] = $row['id'];
            $response->rows[$i]["cell"] = array(
                $row["numero"],
                $row['no_liquidacion'],
                $row['fecha_despacho'],
                $row['fecha_arribo'],
                $row['medio_transporte'],
                $row['valor'],
                $row['origen'],
                $row['destino'],
                $row['fecha_inclusion'],
                    '', //$row['fecha_exclusion'],
                    "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                    $link_option,
                    $hidden_options
                    );
            $i++;
        }
    }
    print(json_encode($response));
    exit;
}

public function ajax_listar_aereo($grid = NULL) {

    $estado = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);

    $clause = array(
        "numero" => $this->input->post('numero', true),
        "serie" => $this->input->post('serie', true),
        "marca" => $this->input->post('marca', true),
        "modelo" => $this->input->post('modelo', true),
        "matricula" => $this->input->post('matricula', true),
        "valor" => $this->input->post('valor', true),
        "pasajeros" => $this->input->post('pasajeros', true),
        "tripulacion" => $this->input->post('tripulacion', true),
        "fecha_inclusion" => $this->input->post('fecha_inclusion', true),
        "fecha_exclusion" => $this->input->post('fecha_exclusion', true),
        );


    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = PolizasAereo::listar_aereo_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $rows = PolizasAereo::listar_aereo_provicional($clause, $sidx, $sord, $limit, $start, $id_poliza);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i = 0;

    if (!empty($rows->toArray())) {
        foreach ($rows->toArray() AS $i => $row) {

            if ($row['estado'] == 'Inactivo')
                $spanStyle = 'label label-danger';
            else if ($row['estado'] == 'Activo')
                $spanStyle = 'label label-successful';
            else
                $spanStyle = 'label label-warning';

            $hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoAereo' data-int-gr='" . $row["id"] . "' data-int-id='" . $row["id"] . "'>Ver Inter&eacute;s</a>";

                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $response->rows[$i]["id"] = $row['id'];
            $response->rows[$i]["cell"] = array(
                $row["numero"],
                $row['serie'],
                $row['marca'],
                $row['modelo'],
                $row['matricula'],
                $row['valor'],
                $row['pasajeros'],
                $row['tripulacion'],
                $row['fecha_inclusion'],
                    '', //$row['fecha_exclusion'],
                    "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                    $link_option,
                    $hidden_options
                    );
            $i++;
        }
    }
    print(json_encode($response));
    exit;
}

public function ajax_listar_maritimo($grid = NULL) {

    $estado = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);

    $clause = array(
        "numero" => $this->input->post('numero', true),
        "serie" => $this->input->post('serie', true),
        "nombre_embarcacion" => $this->input->post('nombre_embarcacion', true),
        "tipo" => $this->input->post('tipo', true),
        "marca" => $this->input->post('marca', true),
        "valor" => $this->input->post('valor', true),
        "acreedor" => $this->input->post('acreedor', true),
        "fecha_inclusion" => $this->input->post('fecha_inclusion', true),
        );

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = PolizasMaritimo::listar_maritimo_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $rows = PolizasMaritimo::listar_maritimo_provicional($clause, $sidx, $sord, $limit, $start, $id_poliza);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i = 0;

    if (!empty($rows->toArray())) {
        foreach ($rows->toArray() AS $i => $row) {

            if ($row['estado'] == 'Inactivo')
                $spanStyle = 'label label-danger';
            else if ($row['estado'] == 'Activo')
                $spanStyle = 'label label-successful';
            else
                $spanStyle = 'label label-warning';

            $hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoMaritimo' data-int-gr='" . $row["id"] . "' data-int-id='" . $row["id"] . "'>Ver Inter&eacute;s</a>";

            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $response->rows[$i]["id"] = $row['id'];
            $response->rows[$i]["cell"] = array(
                $row["numero"],
                $row['serie'],
                $row['nombre_embarcacion'],
                $row['tipo'],
                $row['marca'],
                $row['valor'],
                $row['nombre'],
                $row['fecha_inclusion'],
                    '', //$row['fecha_exclusion'],
                    "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                    $link_option,
                    $hidden_options
                    );
            $i++;
        }
    }
    print(json_encode($response));
    exit;
}

public function ajax_listar_proyecto($grid = NULL) {

    $estado = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);

    $clause = array(
        "numero" => $this->input->post('numero', true),
        "no_orden" => $this->input->post('no_orden', true),
        "nombre_proyecto" => $this->input->post('nombre_proyecto', true),
        "ubicacion" => $this->input->post('ubicacion', true),
        "fecha_inclusion" => $this->input->post('fecha_inclusion', true),
        );

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = PolizasProyecto::listar_proyecto_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $rows = PolizasProyecto::listar_proyecto_provicional($clause, $sidx, $sord, $limit, $start, $id_poliza);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i = 0;

    if (!empty($rows->toArray())) {
        foreach ($rows->toArray() AS $i => $row) {

            if ($row['estado'] == 'Inactivo')
                $spanStyle = 'label label-danger';
            else if ($row['estado'] == 'Activo')
                $spanStyle = 'label label-successful';
            else
                $spanStyle = 'label label-warning';

            $hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoProyecto' data-int-gr='" . $row["id"] . "' data-int-id='" . $row["id"] . "'>Ver Inter&eacute;s</a>";

                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $response->rows[$i]["id"] = $row['id'];
            $response->rows[$i]["cell"] = array(
                $row["numero"],
                $row['nombre_proyecto'],
                $row['no_orden'],
                $row['ubicacion'],
                $row['fecha_inclusion'],
                    '', //$row['fecha_exclusion'],
                    "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                    $link_option,
                    $hidden_options
                    );
            $i++;
        }
    }
    print(json_encode($response));
    exit;
}

public function ajax_listar_ubicacion($grid = NULL) {

    $estado = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);

    $clause = array(
        "numero" => $this->input->post('numero', true),
        "nombre" => $this->input->post('nombre', true),
        "direccion" => $this->input->post('direccion', true),
        "edif_mejoras" => $this->input->post('edif_mejoras', true),
        "contenido" => $this->input->post('contenido', true),
        "maquinaria" => $this->input->post('maquinaria', true),
        "inventario" => $this->input->post('inventario', true),
        "acreedor" => $this->input->post('acreedor', true),
        );

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = PolizasUbicacion::listar_ubicacion_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $rows = PolizasUbicacion::listar_ubicacion_provicional($clause, $sidx, $sord, $limit, $start, $id_poliza);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i = 0;

    if (!empty($rows->toArray())) {
        foreach ($rows->toArray() AS $i => $row) {

            if ($row['estado'] == 'Inactivo')
                $spanStyle = 'label label-danger';
            else if ($row['estado'] == 'Activo')
                $spanStyle = 'label label-successful';
            else
                $spanStyle = 'label label-warning';
            if ($row['acreedor'] != "0") {
                $acreedor = ucwords($row['acreedor']);
            } else {
                $acreedor = "";
            }

            $hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoUbicacion' data-int-gr='" . $row["id"] . "' data-int-id='" . $row["id"] . "'>Ver Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $clause['tipo'] = 1;
            $clause['empresa_id'] = $this->empresa_id;
            $row['nombre_acreedor'] = $this->AcreedoresRep->get($clause);

            $response->rows[$i]["id"] = $row['id'];
            $response->rows[$i]["cell"] = array(
                $row["numero"],
                $row['nombre'],
                $row['direccion'],
                $row['edif_mejoras'],
                $row['contenido'],
                $row['maquinaria'],
                $row['inventario'],
                $acreedor == 1 ? $row['nombre_acreedor'][0]["nombre"] : $acreedor,
                "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                $link_option,
                $hidden_options
                );
            $i++;
        }
    }
    print(json_encode($response));
    exit;
}

public function ajax_listar_vehiculo($grid = NULL) {

    $estado = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);

    $clause = array(
        "numero" => $this->input->post('numero', true),
        "detalle_certificado" => $this->input->post('detalle_certificado', true),
        "chasis" => $this->input->post('chasis', true),
        "unidad" => $this->input->post('unidad', true),
        "marca" => $this->input->post('marca', true),
        "modelo" => $this->input->post('modelo', true),
        "placa" => $this->input->post('placa', true),
        "color" => $this->input->post('color', true),
        "operador" => $this->input->post('operador', true),
        "fecha_inclusion" => $this->input->post('fecha_inclusion', true),
        "prima" => $this->input->post('prima', true),
        );

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = PolizasVehiculo::listar_vehiculo_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $rows = PolizasVehiculo::listar_vehiculo_provicional($clause, $sidx, $sord, $limit, $start, $id_poliza);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i = 0;

    if (!empty($rows->toArray())) {
        foreach ($rows->toArray() AS $i => $row) {

            if ($row['estado'] == 'Inactivo')
                $spanStyle = 'label label-danger';
            else if ($row['estado'] == 'Activo')
                $spanStyle = 'label label-successful';
            else
                $spanStyle = 'label label-warning';

            $hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoVehiculo' data-int-gr='" . $row["id"] . "' data-int-id='" . $row["id"] . "'>Ver Inter&eacute;s</a>";

                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $response->rows[$i]["id"] = $row['id'];
            $response->rows[$i]["cell"] = array(
                $row["numero"],
                $row['detalle_certificado'],
                $row['chasis'],
                $row['unidad'],
                $row['marca'],
                $row['modelo'],
                $row['placa'],
                $row['color'],
                $row['operador'],
                $row['fecha_inclusion'],
                    '', //$row['fecha_exclusion'],
                    $row['detalle_prima'],
                    "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                    $link_option,
                    $hidden_options
                    );
            $i++;
        }
    }
    print(json_encode($response));
    exit;
}

public function ajax_listar_personas($grid = NULL) {


    $estado    = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);
    $vista     = $this->input->post("desde");
    $detalle_unico = $this->input->post("detalle_unico");
    $relacion = empty($this->input->post('relacion')) ? '' : 'Principal'; 

    $clause = array(
        "numero" => $this->input->post('numero', true),
        "nombrePersona" => $this->input->post('nombrePersona', true),
        "identificacion" => $this->input->post('identificacion', true),
        "edad" => $this->input->post('edad', true),
        "sexo" => $this->input->post('sexo', true),
        "estatura" => $this->input->post('estatura', true),
        "telefono_residencial" => $this->input->post('telefono', true),
        "created_at" => $this->input->post('fecha_inclusion', true),
        "telefono_residencial" => $this->input->post('telefono', true),
        "estado" => $this->input->post('estado', true),
        "prima" => $this->input->post('prima', true),
        "detalle_relacion" => $relacion,
        "detalleUnico" => $detalle_unico,
        "desde" => $vista,
        );

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = PolizasPersonas::listar_personas_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $rows = PolizasPersonas::listar_personas_provicional($clause, $sidx, $sord, $limit, $start, $id_poliza);
    $parents = array();
        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i = 0;

    if (!empty($rows->toArray())) {
        foreach ($rows->toArray() AS $i => $row) {
            array_push($parents, $row);
            $clause = array('id_interes' => $row['id_interes']);

            $child = PolizasPersonas::listar_personas_provicional($clause, NULL, NULL, NULL, NULL, $id_poliza);

            if (count($child)) {
                foreach ($child->toArray() as $key => $value) {
                        # code...
                    array_push($parents, $value);
                }
            }
        }



        foreach ($parents as $key => $row) {
                # code...   
            if ($row['estado'] == 'Inactivo')
                $spanStyle = 'label label-danger';
            else if ($row['estado'] == 'Activo')
                $spanStyle = 'label label-successful';
            else
                $spanStyle = 'label label-warning';

            $hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoPersona' data-int-gr='" . $row["id"] . "' data-int-id='" . $row["id"] . "' data-idint-det='" . $row["id"] . "'>Ver Inter&eacute;s</a>";

            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id_interes'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
            $age = $row['fecha_nacimiento'];
            $year = "";
            $telefono = $row['telefono_principal'] != 'Laboral' ? $row['telefono_residencial'] : $row['telefono_oficina'];

            if (strpos($age, '-') !== false) {
                $age = explode("-", $row['fecha_nacimiento']);
                $year = Carbon::createFromDate($age[0], $age[1], $age[2])->age;
            }
            $response->rows[$key]["id"] = $row['id_interes'];
            $response->rows[$key]["cell"] = array(
                'numero' => $row["numero"],
                'certificado' => $row["detalle_certificado"],
                'nombrePersona' => $row['nombrePersona'],
                'identificacion' => $row['identificacion'],
                'fecha_nacimiento' => $row['fecha_nacimiento'],
                'nacionalidad' => $row['nacionalidad'],
                'edad' => $year,
                'sexo' => $row['sexo'] != 1 ? "M" : "F",
                'estatura' => $row['estatura'],
                'peso' => $row['peso'],
                'telefono' => $telefono,
                'relacion' => $row['detalle_relacion'],
                'tipo_relacion' => $row['tipo_relacion'],
                'participacion' => $row['detalle_participacion'],
                'fecha_inclusion' => "",
                'fecha_exclusion' => "",
                'prima' => $row['detalle_prima'],
                'estado' => "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                'options' => $link_option,
                'link' => $hidden_options,
                    "level" => $row["detalle_int_asociado"] != 0 ? "1" : "0", //level
                    'parent' => $row["detalle_int_asociado"] == 0 ? "NULL" : (string) $row["detalle_int_asociado"], //parent
                    'isLeaf' => $row['detalle_int_asociado'] != 0 ? true : false, //isLeaf
                    'expanded' => false, //expended
                    'loaded' => true, //loaded
                    );
        }
    }
    print(json_encode($response));
    exit;
}

public function getRenovationData() {
    $idPoliza = $this->input->post("idPoliza");
    $detalleUnico = $this->input->post("detalleUnico");
    $rows = $this->polizasModel->where('uuid_polizas', hex2bin($idPoliza))
    ->get();
    $response = new stdClass();

    foreach ($rows as $key => $data) {
        $response->agentes = $data->participacionfk;
        $response->numero = $data->numero;
        $plusOneToBeginigDay = new Carbon($data->inicio_vigencia);
        $expirationDay = new Carbon($data->fin_vigencia);
        $difference = ($plusOneToBeginigDay->diff($expirationDay)->days);
        $response->fechaInicio = $expirationDay->addDays(1)->format('m/d/Y');
        $response->fechaExpiracion = $expirationDay->addDays($difference)->format('m/d/Y');
        $clause["id_poliza"] = $data->id;
        $genericObject = $this->factoryHelper($data->id_tipo_int_asegurado);
        $duplicateData = $genericObject->where($clause)->get()->toArray();
        foreach ($duplicateData as $key => $value) {
            $value["detalle_unico"] = $detalleUnico;
            unset($value['id']);
            unset($value['id_poliza']);
            $genericObject->create($value);
        }
        $createdAt = new carbon ($data->created_at);
        $comisionPerYear = $createdAt->diffInYears($expirationDay);
        $response->diferenciaEnAnios= $comisionPerYear;
        $response->interesesAnteriores=$this->session->userdata("interest");
        $response->permiso = $this->auth->has_permission('acceso', 'polizas/crear renovación');
        $comisionesMatriz=PlanesComisiones::where("id_planes",$data->plan_id)
        ->where("inicio",$comisionPerYear+1)
        ->select('inicio','fin','comision')
        ->first();
        if(count($comisionesMatriz)){
         $response->comision = $comisionesMatriz->comision; 
     }else {
        $response->comision = $data->comision;


    }

}
print json_encode($response);
}
public function factoryHelper($objecType){
    $policyInterestModels =[
    1=>"\PolizasArticulo",
    2=>"\PolizasCarga",
    3=>"\PolizasAereo",
    4=>"\PolizasMaritimo",
    5=>"\PolizasPersonas",
    6=>"\PolizasProyecto",
    7=>"\PolizasUbicacion",
    8=>"\PolizasVehiculo"
    ];
    $objectRoutes = "Flexio\Modulo\Polizas\Models";
    $factoryResult;
    foreach ($policyInterestModels as $key => $table) {
           # code...
        if($key ==$objecType){
            $genericObject = $objectRoutes .$table;
            $factoryResult =  new $genericObject(); 

        }
    }
    return $factoryResult;
}

public function policyRenewal() {

    $inf = array();
    $inf["msg"] ='error';
    try {
        $campos = $_POST;
        $camposInteres=json_decode($_POST['camposInteres'],TRUE);
        $error = false;
        $permisoRenovar = $this->auth->has_permission('acceso', 'polizas/crear renovación');

        $required = array("Número de Póliza"=>'numeroPoliza',"Id póliza"=>'idPolicy',"Inicio de vigencia"=>'fechaInicio',"Fin de vigencia"=>'fechaExpiracion',"Guardar"=>'renovarPoliza',"Comision"=>'comision',"Centro contable"=>"centroContable");

        foreach ($required as $key => $field) {
            # code...
           if (empty($_POST[$field])) {
            $error = true;
            $inf['field'] = $key ." es requerido";
            break;
        }
    }
    if(!$error and $permisoRenovar){
     $motivo = $campos['numeroPoliza'];
     $solicitud =$campos['idPolicy'];
     $fechaInicio = new Carbon($this->input->post('fechaInicio'));
     $fechaExpiracion = new Carbon($this->input->post('fechaExpiracion'));
     $comision = $campos['comision'];


     $usuario = $this->usuario_id;
     $exist = false;
     $datosSolicitud = $this->polizasModel->where(['numero'=>$motivo])
     ->groupBy('aseguradora_id')
     ->select('aseguradora_id','solicitud')
     ->get();

     if(count($datosSolicitud)){
         $aseguradora = $this->polizasModel->where('id',$solicitud)
         ->select('aseguradora_id','solicitud')
         ->first();
         foreach ($datosSolicitud as $key => $value) {

          if($value->aseguradora_id ==$aseguradora->aseguradora_id ){
            if($value->solicitud !=$aseguradora->solicitud)
                $exist =true;
            $inf['field']= $motivo." ya se encuentra registrada con aseguradora ".$aseguradora->aseguradorafk->nombre;

        }
    }  
}

if (!$exist) {

 $solicitudes = $this->polizasModel->where('id', $solicitud)->first()->toArray();

 $sol = [
 'numero' => $motivo,
 'creado_por' => $usuario,
 'empresa_id' => $this->empresa_id,
 'cliente' => $solicitudes['cliente'],
 'ramo_id' => $solicitudes['ramo_id'], 
 'ramo' => $solicitudes['ramo'],
 'tipo_ramo' => $solicitudes['tipo_ramo'],
 'id_tipo_int_asegurado' => $solicitudes['id_tipo_int_asegurado'],
 'usuario' => $solicitudes['usuario'],
 'estado' => 'Por Facturar',
 'inicio_vigencia' => $fechaInicio->format('Y/m/d/'),
 'fin_vigencia' =>$fechaExpiracion->format('Y/m/d'),
 'frecuencia_facturacion' => $campos["pagosFrecuencia"],
 'ultima_factura' => $solicitudes['ultima_factura'],
 'categoria' => '45',
 'solicitud' => $solicitudes['solicitud'],
 'aseguradora_id' => $solicitudes['aseguradora_id'],
 'plan_id' => $solicitudes['plan_id'],
 'comision' => $comision,
 'porcentaje_sobre_comision' => $solicitudes['porcentaje_sobre_comision'],
 'impuesto' => $solicitudes['impuesto'],
 'poliza_declarativa'=>isset($campos["vigenciaPolizaDeclarativa"])  ? "si" : "no",
 'created_at' => $solicitudes['created_at'],
 'renovacion_id' => $solicitud ,
 'centro_contable' => $campos["centroContable"]

 ];

         /* $datos['dias_transcurridos'] = ($datosSolicitud->created_at->diff($now)->days < 1) ? '1' : $datosSolicitud->created_at->diff($now)->days;
         $this->polizasModel->where(['numero'=> $solicitud])->update($datos);*/

         $poliza1 = new Flexio\Modulo\Polizas\Models\Polizas;
         $p = $poliza1->create($sol);
         $poliza2 = new Flexio\Modulo\Polizas\Models\PolizasPrima;

         $solprima =[
         'id_poliza' => $p->id,
         'prima_anual' => $campos["primaAnual"],
         'impuesto' => $campos["primaImpuesto"],
         'otros' => $campos["primaOtros"],
         'descuentos' => $campos["primaDescuentos"],
         'total' => $campos["primaTotal"],
         'frecuencia_pago' => $campos["pagosFrecuencia"],
         'metodo_pago' => $campos["pagosMetodo"],
         'fecha_primer_pago' => $campos["pagosPrimerPago"],
         'cantidad_pagos' => $campos["pagosCantidad"],
         'sitio_pago' =>  $campos["pagosSitio"],
         'centro_facturacion' => $campos["pagosCentroFac"],
         'direccion_pago' => $campos["pagosDireccion"]
         ];
         $p2 = $poliza2->create($solprima);

         $poliza3 = new Flexio\Modulo\Polizas\Models\PolizasVigencia;

         $solvigencia = [
         'id_poliza' => $p->id,
         'vigencia_desde' =>  $fechaInicio->format('Y/m/d/'),
         'vigencia_hasta' => $fechaExpiracion->format('Y/m/d/'),
         'suma_asegurada' => $campos["sumaAsegurada"],
         'tipo_pagador' => $campos["vigenciapagador"],
         'pagador' => $campos["vigenciaNombrePagador"],
         
         ];
         $p3 = $poliza3->create($solvigencia);

         $poliza4 = new Flexio\Modulo\Polizas\Models\PolizasCobertura;
         $coberturas = json_decode($campos["planesCoberturas"],TRUE);
         for ($i =0;$i<count($coberturas['coberturas']['nombre']); $i++) {
            $solCoberturas = [
            'cobertura' => $coberturas['coberturas']["nombre"][$i],
            'valor_cobertura' => $coberturas['coberturas']["valor"][$i],
            'id_poliza' => $p->id
            ];
            $p4 = $poliza4->create($solCoberturas);
        }

        $poliza5 = new Flexio\Modulo\Polizas\Models\PolizasDeduccion;

        for ($i=0;$i<count($coberturas['deducibles']['nombre']);$i++) {
            $solDeducion = [
            'deduccion' => $coberturas['deducibles']['nombre'][$i],
            'valor_deduccion' => $coberturas['deducibles']['valor'][$i],
            'id_poliza' => $p->id
            ];
            $p5 = $poliza5->create($solDeducion);
        }

        $poliza6 = new Flexio\Modulo\Polizas\Models\PolizasParticipacion;
        //$participacion = PolizasParticipacion::where('id_poliza',$solicitudes['id']);

        $porcentage = $this->input->post('participacion');
        if(!empty($porcentage)){
            for ($i=0; $i <count($porcentage['nombre']) ; $i++) { 
               $solParticipacion = [
               'id_poliza' => $p->id,
               'agente' => $porcentage['nombre'][$i], 
               'porcentaje_participacion' => $porcentage['valor'][$i]
               ];
               $p6 = $poliza6->create($solParticipacion);       
           } 
       }

        //Crear Acreedores
       $fieldsetacre = array();
       $campoacreedores = $this->input->post('campoacreedores');
       $ids = array();
        //PolizasAcreedores::where("id_poliza", $poliza->id)->delete();
       if($campoacreedores!=NULL){                        
        $porcentaje_cesion = $this->input->post('campoacreedores_por');
        $monto_cesion = $this->input->post('campoacreedores_mon');
        $id_acreedores = $this->input->post('campoacreedores_id');                    
        foreach ($campoacreedores as $key => $value) {
            $fieldsetacre['acreedor'] = $value;
            $fieldsetacre["id_poliza"] = $p->id;
            $fieldsetacre["porcentaje_cesion"] = $porcentaje_cesion[$key];
            $fieldsetacre["monto_cesion"] = $monto_cesion[$key];
            if ($value != "") {
                $acre = PolizasAcreedores::create($fieldsetacre); 
            }                                         
        }
    }
}

$poliza7 = new Flexio\Modulo\Polizas\Models\PolizasCliente;
$cliente = PolizasCliente::where('id_poliza',$solicitudes['id'])->first();

$solCliente = [
'id_poliza' => $p->id,
'nombre_cliente' => $cliente->nombre_cliente,
'identificacion' => $cliente->identificacion,
'n_identificacion' => $cliente->n_identificacion,
'grupo' =>isset( $campos["clienteGrupo"]) ? $campos["clienteGrupo"] :"",
'telefono' => $campos["clienteTelefono"],
'correo_electronico' => $campos["clienteCorreo"],
'direccion' =>isset( $campos["clienteDireccion"]) ?  $campos["clienteDireccion"] :"",
'exonerado_impuesto' => $campos["clienteExoneradoImp"]
];

$p7 = $poliza7->create($solCliente);


$policyType=$solicitudes['id_tipo_int_asegurado'];
$adittionalParam['id_poliza'] = $p->id;
$adittionalParam['id_interes'] = $campos["interesId"];
$genericObject->where(['id_poliza'=>$aditionalParam['id_poliza']])->update(['detalle_unico'=>$aditionalParam['detalleUnico']]);
if($solicitudes['tipo_ramo']=="colectivo"){
 $interesTypo  = $solicitudes['id_tipo_int_asegurado'];
 $detalleUnico = $campos["detalle"];
 $this->restoreInformation($interesTypo,$detalleUnico,$p->id);

}else{
    $adittionalParam['detalleUnico']=0;
    $inf['msg'] = $this->saveIndividualInterestByType($policyType,$camposInteres,$adittionalParam);
}
$this->polizasModel->where(['id'=> $solicitud])
->update(['estado'=>'Renovada','fecha_renovacion' => date("Y/m/d")]);
$mensaje = array('estado' => 200, 'titulo' => '<b>¡&Eacute;xito!</b>', 'mensaje' => 'Se ha Realizado la renovación <b>'.$motivo.'</b>');
$this->session->set_flashdata('mensaje', $mensaje);
}


} catch (\Exception $e) {
    $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
    print  $inf["msg"] = 'error'. __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n";
}
print json_encode($inf);
exit;
}
function restoreInformation($interestType,$detalleUnico,$idPoliza=NULL){
    if ($this->input->is_ajax_request()) {
     $detalleUnico = $this->input->post('detalleUnico');
     $interestType =$this->input->post('interestType');
 }
 $genericObject=$this->factoryHelper($interestType);

 $genericObject->where(['detalle_unico'=> $detalleUnico])->update(['id_poliza'=>$idPoliza]);
 $previousInterest = $this->session->userdata("interest");
 foreach ($previousInterest as $key => $value) {
    $genericObject->create($value);
} 

}
function ajax_save_individual_interest(){
    $policyType = $this->input->post("interestType");
    $camposInteres =json_decode($this->input->post("camposInteres"),TRUE);
    $aditionalParam['detalleUnico'] = $this->input->post("detalleUnico");
    //$aditionalParam['id_poliza'] = $this->input->post("polizaId");
    $aditionalParam['id_interes']=$this->input->post("interesId");
    $msg= $this->saveIndividualInterestByType($policyType,$camposInteres,$aditionalParam);

    print($msg);
}
function saveIndividualInterestByType($policyType,$camposInteres, $aditionalParam){
    $msg = "";
    try {
     Capsule::beginTransaction();


     $clause["interesestable_type"]=$policyType;
     $clause["empresa_id"] = $this->empresa_id;

     $genericObject =$this->factoryHelper($policyType);

     if($policyType == 1){
        $result =$this->interesesAseguradosRep->where("interesestable_type",$policyType)
        ->where("interesestable_id",$aditionalParam['id_interes'])
        ->select("numero")
        ->first();

        if(!count($result)){
            $total=$this->interesesAseguradosRep->where($clause)->count();
            $numero= Util::generar_codigo('ART', count($total) + 1);
        }else{
            $numero = $result->numero;
        }
        $clause=[
        "uuid_articulo"=>hex2bin($camposInteres["campo[uuid]"]),
        "id_poliza"=>$aditionalParam['id_poliza'],
        "numero"=>$numero,
        "empresa_id"=>$this->empresa_id,
        "nombre"=>$camposInteres["campo[nombre]"],
        "clase_equipo"=>$camposInteres["campo[clase_equipo]"],
        "marca"=>$camposInteres["campo[marca]"],
        "modelo"=>$camposInteres["campo[modelo]"],
        "anio"=>$camposInteres["campo[anio]"],
        "numero_serie"=>$camposInteres["campo[numero_serie]"],
        "id_condicion"=>$camposInteres["campo[id_condicion]"],
        "valor"=>$camposInteres["campo[valor]"],
        "observaciones"=>$camposInteres["campo[observaciones]"],
        "estado"=>$camposInteres["campo2[estado]"],
        "detalle_certificado"=>$camposInteres["campodetalle[certificado]"],
        "detalle_suma_asegurada"=>$camposInteres["campodetalle[suma_asegurada]"],
        "detalle_prima"=>$camposInteres["campodetalle[prima_anual]"],
        "detalle_deducible"=>$camposInteres["campodetalle[deducible]"],
        ];
        PolizasArticulo::create($clause);
        $msg="print";

    }elseif($policyType == 2){

        $result =$this->interesesAseguradosRep->where("interesestable_type",$policyType)
        ->where("interesestable_id",$aditionalParam['id_interes'])
        ->select("numero")
        ->first();

        if(!count($result)){
            $total=$this->interesesAseguradosRep->where($clause)->count();
            $numero= Util::generar_codigo('CGA', count($total) + 1);
        }else{
            $numero = $result->numero;
        }
        $clause =[
        "empresa_id"=>$this->empresa_id,
        "id_poliza"=>$aditionalParam['id_poliza'],
        "numero"=>$numero,
        "detalle"=>$camposInteres["campo[detalle]"],
        "no_liquidacion"=>$camposInteres["campo[no_liquidacion]"],
        "fecha_despacho"=>$camposInteres["campo[fecha_despacho]"],
        "fecha_arribo"=>$camposInteres["campo[fecha_arribo]"],
        "condicion_envio"=>$camposInteres["campo[condicion_envio]"],
        "medio_transporte"=>$camposInteres["campo[medio_transporte]"],
        "origen"=>$camposInteres["campo[origen]"],
        "destino"=>$camposInteres["campo[destino]"],
        "observaciones"=>$camposInteres["campo[observaciones]"],
        "tipo_empaque"=>$camposInteres["campo[tipo_empaque]"],
        "tipo_obligacion"=>$camposInteres["campo[tipo_obligacion]"],
        "acreedor"=>$camposInteres["campo[acreedor]"],
        "estado"=>$camposInteres["campo[estado]"],
        "acreedor_opcional"=>$camposInteres["campo[acreedor_opcional]"],
        "tipo_obligacion_opcional"=>$camposInteres["campo[tipo_obligacion_opcional]"],
        "detalle_certificado"=>$camposInteres["campodetalle[certificado]"],
        "detalle_suma_asegurada"=>$camposInteres["campodetalle[suma_asegurada]"],
        "detalle_prima"=>$camposInteres["campodetalle[prima_anual]"],
        "detalle_deducible"=>$camposInteres["campodetalle[deducible]"],
        ];


        $msg =PolizasCarga::create($clause);



    }elseif($policyType == 3){

        $result =$this->interesesAseguradosRep->where("interesestable_type",$policyType)
        ->where("interesestable_id",$aditionalParam['id_interes'])
        ->select("numero")
        ->first();


        if(!count($result)){
            $total=$this->interesesAseguradosRep->where($clause)->count();
            $numero= Util::generar_codigo('CAE', count($total) + 1);
        }else{
            $numero = $result->numero;
        }
        $clause =[

        "empresa_id"=>$this->empresa_id,
        "id_poliza"=>$p->id,
        "serie"=>$camposInteres["campo[serie]"],
        "marca"=>$camposInteres["campo[marca_aereo]"],
        "modelo"=>$camposInteres["campo[modelo_aereo]"],
        "matricula"=>$camposInteres["campo[matricula_aereo]"],
        "valor"=>$camposInteres["campo[valor_aereo]"],
        "pasajeros"=>$camposInteres["campo[pasajeros_aereo]"],
        "tripulacion"=>$camposInteres["campo[tripulacion_aereo]"],
        "observaciones"=>$camposInteres["campo[observaciones]"],
        "numero"=>$numero,
        "tipo_id"=>$camposInteres["campo[tipo_id]"],
        "detalle_certificado"=>$camposInteres["campodetalle[certificado]"],
        "detalle_suma_asegurada"=>$camposInteres["campodetalle[suma_asegurada]"],
        "detalle_prima"=>$camposInteres["campodetalle[prima_anual]"],
        "detalle_deducible"=>$camposInteres["campodetalle[deducible]"],
        "estado"=>$camposInteres["campo[estado]"],
        ]; 

        PolizasAereo::create($clause);
    }elseif($policyType == 4){
        $result =$this->interesesAseguradosRep->where("interesestable_type",$policyType)
        ->where("interesestable_id",$campos["interesId"])
        ->select("numero")
        ->first();

        if(!count($result)){
            $total=$this->interesesAseguradosRep->where($clause)->count();
            $numero= Util::generar_codigo('CM', count($total) + 1);
        }else{
            $numero = $result->numero;
        }
        $clause= [
        "uuid_casco_maritimo"=>hex2bin($camposInteres["campo[uuid]"]),
        "empresa_id"=>$this->empresa_id,
        "id_poliza"=>$p->id,
        "numero"=>$numero,
        "serie"=>$camposInteres["campo[serie]"],
        "nombre_embarcacion"=>$camposInteres["campo[nombre_embarcacion]"],
        "tipo"=>$camposInteres["campo[tipo]"],
        "marca"=>$camposInteres["campo[marca]"],
        "valor"=>$camposInteres["campo[valor]"],
        "pasajeros"=>$camposInteres["campo[pasajeros]"],
        "acreedor"=>$camposInteres["campo[acreedor]"],
        "porcentaje_acreedor"=>$camposInteres["campo[porcentaje_acreedor]"],
        "observaciones"=>$camposInteres["campo[observaciones]"],
        "tipo_id"=>$camposInteres["campo[tipo_id]"],
        "detalle_certificado"=>$camposInteres["campodetalle[certificado]"],
        "detalle_suma_asegurada"=>$camposInteres["campodetalle[suma_asegurada]"],
        "detalle_prima"=>$camposInteres["campodetalle[prima_anual]"],
        "detalle_deducible"=>$camposInteres["campodetalle[deducible]"],
        "estado"=>$camposInteres["campo2[estado]"],
        ];    

        PolizasMaritimo::create($clause);



    }elseif($policyType == 5){

        $result =$this->interesesAseguradosRep->where("interesestable_type",$policyType)
        ->where("interesestable_id",$aditionalParam['id_interes'])
        ->select("numero")
        ->first();

        if(!count($result)){
            $total=$this->interesesAseguradosRep->where($clause)->count();
            $numero= Util::generar_codigo('PER', count($total) + 1);
        }else{
            $numero = $result->numero;
        }
    //$datosPersonas = PolizasPersonas::where(['id_poliza' => $solicitudes['id']])->get();

        if (!empty($camposInteres['campo[pasaporte]']) || $camposInteres['campo[letra]'] == 'PAS') {
            $cedula = $camposInteres['campo[pasaporte]'];
            $camposInteres['ruc'] = $cedula;
        }if ($camposInteres['campo[identificacion]'] == 'cedula') {
            $provincia = strlen($camposInteres['campo[provincia]']) ==0  ? "" : $camposInteres['campo[provincia]'] . '-';
            $letra = !isset($camposInteres['campo[letra]']) ? "" : $camposInteres['campo[letra]']."-";
            $cedula = $provincia . $letra . $camposInteres['campo[tomo]'] . "-" . $camposInteres['campo[asiento]'];
            $camposInteres['ruc'] = $cedula;
        }
        $clause =[
        "id_interes"=>$aditionalParam["id_interes"],
        "id_poliza"=>isset($aditionalParam["id_poliza"]) ? $aditionalParam["id_poliza"] : 0,
        "detalle_unico"=>$aditionalParam["detalleUnico"],
        "numero"=>$numero,
        "nombrePersona"=>$camposInteres["campo[nombrePersona]"],
        "identificacion" =>$camposInteres["ruc"],
        "fecha_nacimiento"=>$camposInteres["campo[fecha_nacimiento]"],
        "estado_civil"=>$camposInteres["campo[estado_civil]"],
        "nacionalidad"=>$camposInteres["campo[nacionalidad]"],
        "sexo"=>$camposInteres["campo[sexo]"],
        "estatura"=>$camposInteres["campo[estatura]"],
        "peso"=>$camposInteres["campo[peso]"],
        "telefono_residencial"=>$camposInteres["campo[telefono_residencial]"],
        "telefono_oficina"=>$camposInteres["campo[telefono_oficina]"],
        "direccion_residencial"=>$camposInteres["campo[direccion_residencial]"],
        "direccion_laboral"=>$camposInteres["campo[direccion_laboral]"],
        "observaciones"=>$camposInteres["campo[observaciones]"],
        "empresa_id"=>$this->empresa_id,
        "telefono_principal"=>$camposInteres["campo[telefono]"],
        "direccion_principal"=>$camposInteres["campo[direccion]"],
        "detalle_relacion"=>strlen($camposInteres["campodetalle[relacion]"])?$camposInteres["campodetalle[relacion]"] :$camposInteres["campodetalle[relacion_benficario]"],
        "detalle_int_asociado"=>$camposInteres["campodetalle[interes_asociado]"],
        "detalle_certificado"=>$camposInteres["campodetalle[certificado]"],
        "detalle_monto"=>$camposInteres["campodetalle[monto]"],
        "detalle_prima"=>$camposInteres["campodetalle[prima_anual]"],
        "estado"=>$camposInteres["campo[estado]"],
        "fecha_inclusion"=>"",
        "detalle_participacion"=>$camposInteres["campodetalle[participacion]"], 
        "detalle_suma_asegurada"=>$camposInteres["campodetalle[suma_asegurada]"],
        "correo" =>$camposInteres["campo[correo]"],
        "tipo_relacion"=>$camposInteres["campodetalle[tipo_relacion]"]
        ];

        $msg="success";
        print_r($this->session->userdata("interest"));
        $result=$genericObject->where(['numero'=>$numero,'detalle_unico'=>$aditionalParam['detalleUnico']])->count();
        if($result){
           $genericObject->where(['numero'=>$numero,'detalle_unico'=>$aditionalParam['detalleUnico']])->update($clause);
       }else{

        $genericObject->create($clause);
    }
}elseif($policyType == 6){
    $result =$this->interesesAseguradosRep->where("interesestable_type",$policyType)
    ->where("interesestable_id",$campos["interesId"])
    ->select("numero")
    ->first();

    if(!count($result)){
        $total=$this->interesesAseguradosRep->where($clause)->count();
        $numero= Util::generar_codigo('PRO', count($total) + 1);
    }else{
        $numero = $result->numero;
    }
    $clause =[
    "uuid_proyecto"=>hex2bin($camposInteres["campo[uuid]"]),
    "id_poliza"=>$p->id,
    "empresa_id"=>$this->empresa_id,
    "numero"=>$numero,
    "nombre_proyecto"=>$camposInteres["campo[nombre_proyecto]"],
    "no_orden"=>$camposInteres["campo[no_orden]"],
    "contratista"=>$camposInteres["campo[contratista]"],
    "representante_legal"=>$camposInteres["campo[representante_legal]"],
    "duracion"=>$camposInteres["campo[duracion]"],
    "fecha"=>$camposInteres["campo[fecha]"],
    "monto"=>$camposInteres["campo[monto]"],
    "monto_afianzado"=>$camposInteres["campo[monto_afianzado]"],
    "acreedor"=>$camposInteres["campo[acreedor]"],
    "porcentaje_acreedor"=>$camposInteres["campo[monto_afianzado]"],
    "ubicacion"=>$camposInteres["campo[ubicacion]"],
    "observaciones"=>$camposInteres["campo[observaciones]"],
    "estado"=>$camposInteres["campo2[estado]"],
    "tipo_id"=>$camposInteres["campo[tipo_id]"],
    "tipo_propuesta"=>$camposInteres["campo[tipo_propuesta]"],
    "validez_fianza_pr"=>$camposInteres["campo[validez_fianza_pr]"],
    "tipo_fianza"=>$camposInteres["campo[tipo_fianza]"],
    "asignado_acreedor"=>$camposInteres["campo[acreedor]"],
    "fecha_concurso"=>$camposInteres["campo[fecha_concurso]"],
    "acreedor_opcional"=>$camposInteres["campo[acreedor_opcional]"],
    "validez_fianza_opcional"=>$camposInteres["campo[validez_fianza_opcional]"],
    "tipo_propuesta_opcional"=>$camposInteres["campo[tipo_propuesta_opcional]"],
    "detalle_certificado"=>$camposInteres["campodetalle[certificado]"],
    "detalle_suma_asegurada"=>$camposInteres["campodetalle[suma_asegurada]"],
    "detalle_prima"=>$camposInteres["campodetalle[prima_anual]"],
    "detalle_deducible"=>$camposInteres["campodetalle[deducible]"]
    ];  


    $genericObject->create($clause);




}elseif($policyType == 7){

    $result =$this->interesesAseguradosRep->where("interesestable_type",$policyType)
    ->where("interesestable_id",$aditionalParam['id_interes'])
    ->select("numero")
    ->first();

    if(!count($result)){
        $total=$this->interesesAseguradosRep->where($clause)->count();
        $numero= Util::generar_codigo('UBI', count($total) + 1);
    }else{
        $numero = $result->numero;
    }
    $clause =[
    "uuid_ubicacion"=>hex2bin($camposInteres["campo[uuid]"]),
    "empresa_id"=>$this->empresa_id,
    "id_poliza"=>$p->id,
    "numero"=>$numero,
    "nombre"=>$camposInteres["campo[nombre]"],
    "direccion"=>$camposInteres["campo[direccion]"],
    "edif_mejoras"=>$camposInteres["campo[edif_mejoras]"],
    "contenido"=>$camposInteres["campo[contenido]"],
    "maquinaria"=>$camposInteres["campo[maquinaria]"],
    "inventario"=>$camposInteres["campo[inventario]"],
    "acreedor"=>$camposInteres["campo[acreedor]"],
    "porcentaje_acreedor"=>$camposInteres["campo[porcentaje_acreedor]"],
    "observaciones"=>$camposInteres["campo[observaciones]"],
    "estado"=>$camposInteres["campo2[estado]"],
    "tipo_id"=>$camposInteres["campo[id]"],
    "acreedor_opcional"=>$camposInteres["campo[acreedor_opcional]"],
    "detalle_certificado"=>$camposInteres["campodetalle[certificado]"],
    "detalle_suma_asegurada"=>$camposInteres["campodetalle[suma_asegurada]"],
    "detalle_prima"=>$camposInteres["campodetalle[prima_anual]"],
    "detalle_deducible"=>$camposInteres["campodetalle[deducible]"],
    ]; 

    $msg = PolizasUbicacion::create($clause);

}elseif($policyType == 8){


    $result =$this->interesesAseguradosRep->where("interesestable_type",$policyType)
    ->where("interesestable_id",$aditionalParam['id_interes'])
    ->select("numero")
    ->first();

    if(!count($result)){
        $total=$this->interesesAseguradosRep->where($clause)->count();
        $numero= Util::generar_codigo('VEH', count($total) + 1);
    }else{
        $numero = $result->numero;
    }
    $clause= [
    "id_poliza"=>$p->id,
    "numero"=>$numero,
    "chasis"=>$camposInteres["campo[chasis]"],
    "uuid_vehiculo"=>hex2bin($camposInteres["campo[uuid]"]),
    "unidad"=>$camposInteres["campo[unidad]"],
    "marca"=>$camposInteres["campo[marca]"],
    "modelo"=>$camposInteres["campo[modelo]"],
    "placa"=>$camposInteres["campo[placa]"],
    "ano"=>$camposInteres["campo[ano]"],
    "motor"=>$camposInteres["campo[motor]"],
    "color"=>$camposInteres["campo[color]"],
    "capacidad"=>$camposInteres["campo[capacidad]"],
    "uso"=>$camposInteres["campo[uso]"],
    "condicion"=>$camposInteres["campo[condicion]"],
    "operador"=>$camposInteres["campo[operador]"],
    "extras"=>$camposInteres["campo[extras]"],
    "valor_extras"=>$camposInteres["campo[valor_extras]"],
    "acreedor"=>$camposInteres["campo[acreedor]"],
    "porcentaje_acreedor"=>$camposInteres["campo[porcentaje_acreedor]"],
    "observaciones"=>$camposInteres["campo[observaciones]"],
    "empresa_id"=>$this->empresa_id,
    "detalle_certificado"=>$camposInteres["campodetalle[certificado]"],
    "detalle_suma_asegurada"=>$camposInteres["campodetalle[suma_asegurada]"],
    "detalle_prima"=>$camposInteres["campodetalle[prima_anual]"],
    "detalle_deducible"=>$camposInteres["campodetalle[deducible]"],
    "estado"=>$camposInteres["campo2[estado]"],
    ];

    $msg = PolizasVehiculo::create($clause);
}   
Capsule::commit();
}catch (Exception $e) {
    $msg = 'error'. __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n";
    Capsule::rollback();
} 
return $msg;
exit;
}
public function exportarPoliza($id_poliza = null) {
    $poliza = PolizasModel::where(['id' => $id_poliza])->get();

    $csv = array();
    $csvdata = array();
    $i = 0;
    foreach ($poliza AS $row) {
        $csvdata[$i]['cliente'] = utf8_decode($row->clientepolizafk->nombre_cliente);
        $csvdata[$i]['identificacion'] = utf8_decode($row->clientepolizafk->n_identificacion);
        $csvdata[$i]['telefono'] = utf8_decode($row->clientepolizafk->telefono);
        $csvdata[$i]['correo_electronico'] = utf8_decode($row->clientepolizafk->correo_electronico);
        $csvdata[$i]['direccion'] = utf8_decode($row->clientepolizafk->direccion);
        $csvdata[$i]['n_poliza'] = utf8_decode($row->numero);
        $csvdata[$i]['aseguradora'] = utf8_decode($row->aseguradorafk->nombre);
        $csvdata[$i]['plan'] = utf8_decode($row->planesfk->nombre);
        $csvdata[$i]['pagador'] = utf8_decode($row->vigenciafk->tipo_pagador);
        $csvdata[$i]['nombre_pagador'] = utf8_decode($row->vigenciafk->pagador);
        $csvdata[$i]['inicio_vigencia'] = utf8_decode($row->vigenciafk->vigencia_desde);
        $csvdata[$i]['fin_vigencia'] = utf8_decode($row->vigenciafk->vigencia_hasta);
        $csvdata[$i]['prima_total'] = utf8_decode(number_format($row->primafk->total, 2));
        $csvdata[$i]['cantidad_pagos'] = utf8_decode($row->primafk->cantidad_pagos);
        $csvdata[$i]['frecuancia_pagos'] = utf8_decode($row->primafk->frecuencia_pago);
        $csvdata[$i]['metodo_pago'] = utf8_decode($row->primafk->metodo_pago);
        $i++;
    }
        //we create the CSV into memory
    $csv = Writer::createFromFileObject(new SplTempFileObject());
    $headers = [
    'Cliente',
    'Identificacion',
    'Telefono',
    'Correo electronico',
    'Dirección',
    'N° poliza',
    'Aseguradora',
    'Plan',
    'Pagador',
    'Nombre pagador',
    'Inicio Vigencia',
    'Fin Vigencia',
    'Prima total',
    'Cantidad pagos',
    'Frecuencia pagos',
    'Metodo pago',
    ];
    $i++;
    $csvspace[$i]['espacio'] = "";
    $i++;
    $headers2[$i]['interes_asegurado'] = utf8_decode('N° Interés asegurado');
    $headers2[$i]['n_certificado'] = utf8_decode('N° Certificado');
    $headers2[$i]['fecha_inclusion'] = utf8_decode('fecha_inclusion');
    $headers2[$i]['fecha_exclusion'] = utf8_decode('fecha_exclusion');
    $headers2[$i]['Estado'] = utf8_decode('Estado');


    if ($poliza[0]->id_tipo_int_asegurado == 1) {
        $interes_asegurado = PolizasArticulo::listar_articulo_provicional(NULL, NULL, NULL, NULL, NULL, $poliza[0]->id);
    } elseif ($poliza[0]->id_tipo_int_asegurado == 2) {
        $interes_asegurado = PolizasCarga::listar_carga_provicional(NULL, NULL, NULL, NULL, NULL, $poliza[0]->id);
    } elseif ($poliza[0]->id_tipo_int_asegurado == 3) {
        $interes_asegurado = PolizasAereo::listar_aereo_provicional(NULL, NULL, NULL, NULL, NULL, $poliza[0]->id);
    } elseif ($poliza[0]->id_tipo_int_asegurado == 4) {
        $interes_asegurado = PolizasMaritimo::listar_maritimo_provicional(NULL, NULL, NULL, NULL, NULL, $poliza[0]->id);
    } elseif ($poliza[0]->id_tipo_int_asegurado == 5) {
        $interes_asegurado = PolizasPersonas::listar_personas_provicional(NULL, NULL, NULL, NULL, NULL, $poliza[0]->id);
    } elseif ($poliza[0]->id_tipo_int_asegurado == 6) {
        $interes_asegurado = PolizasProyecto::listar_proyecto_provicional(NULL, NULL, NULL, NULL, NULL, $poliza[0]->id);
    } elseif ($poliza[0]->id_tipo_int_asegurado == 7) {
        $interes_asegurado = PolizasUbicacion::listar_ubicacion_provicional(NULL, NULL, NULL, NULL, NULL, $poliza[0]->id);
    } elseif ($poliza[0]->id_tipo_int_asegurado == 8) {
        $interes_asegurado = PolizasVehiculo::listar_vehiculo_provicional(NULL, NULL, NULL, NULL, NULL, $poliza[0]->id);
    }

    $i++;
    foreach ($interes_asegurado AS $value) {
        $csvdata2[$i]["interes_asegurado"] = utf8_decode($value->numero);
        $csvdata2[$i]["n_certificado"] = utf8_decode($value->detalle_certificado);
        $csvdata2[$i]["fecha_inclusion"] = utf8_decode($value->fecha_inclusion);
        $csvdata2[$i]["fecha_exclusion"] = "";
        $csvdata2[$i]["Estado"] = utf8_decode($value->estado);
        $i++;
    }

    $decodingHeaders = array_map("utf8_decode", $headers);
    $csv->insertOne($decodingHeaders);
    $csv->insertAll($csvdata);
    $csv->insertAll($csvspace);
    $csv->insertAll($headers2);
    if (isset($csvdata2)) { $csv->insertAll($csvdata2); }    
    $csv->output("poliza-" . date('y-m-d') . ".csv");
    exit();
}

public function obtener_politicas() {
    echo json_encode($this->politicas);
    exit;
}

public function obtener_politicas_general() {
    echo json_encode($this->politicas_general);
    exit;
}

public function exportar_factura($id_poliza = null, $id_factura = null){

    $factura = FacturaSeguro::where(['id_poliza' => $id_poliza, 'codigo' => $id_factura ])->first();


    $nombre = $factura->codigo;
    $formulario = "formularioFactura";

    $data = ['datos' => $factura/*, 'centro_facturacion' => $centroFacturacion, 'total_participacion' => $totalParticipacion, 'interes_asegurado' => $interes_asegurado */];
    $dompdf = new Dompdf();
    $html = $this->load->view('pdf/' . $formulario, $data, true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream($nombre); //, array("Attachment" => false)
    //exit(0);
}

function ajax_get_asociados() {

    $poliza = $_POST['idpoliza'];

    $inter = PolizasPersonas::where("id_poliza", $poliza)->where("detalle_relacion", "Principal")->select("detalle_certificado", "id_interes as id", "nombrePersona")->get();

    $response = new stdClass();
    $response->inter = array();
    $response->inter = $inter->toArray();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($response))->_display();

    exit;
}

function ajax_get_cliente(){
    $id_poliza = $this->input->post('id_poliza');
    $cliente_id = PolizasModel::where(['id' => $id_poliza])->select('cliente')->first();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cliente_id))->_display();
    exit;

}

function ajax_get_cobro_agendado(){

    $id_poliza = $this->input->post('id_poliza');
    $modo_agendado = $this->input->post('modo_cobro');
    $response = new stdClass();
    $response->datos_cobro = array();

    if($modo_agendado == 'individual'){


        $datosCobros = Cobros_seguros::where(['empezable_id' => $id_poliza, 'estado' => 'agendado'])->first();
        if(count($datosCobros)){
            $response->cobro_agendado = 1;
            $response->datos_cobro = ['numero_cobro' => $datosCobros->codigo, 'fecha_cobro' => date('d-m-Y' ,strtotime($datosCobros->created_at)), 'uuid_cobro' => $datosCobros->uuid_cobro];

        }else{

            $datosPolizas = PolizasModel::where(['id' => $id_poliza])->first();
            $cont_facturas = 0;
            foreach ($datosPolizas->facturasegurofk as $key => $value) {
                if( ($value['estado'] == 'por_cobrar' && $value['saldo'] == NULL) || ($value['estado'] == 'cobrado_parcial' && $value['saldo'] == NULL ) ){
                    if($value['total'] > 0 ){
                        $cont_facturas++;
                    }else{
                        $cont_facturas = 0;
                    }
                }elseif( ($value['estado'] == 'por_cobrar' && $value['saldo'] != NULL) || ($value['estado'] == 'cobrado_parcial' && $value['saldo'] != NULL) ){
                    if($value['saldo'] > 0 ){
                        $cont_facturas++;
                    }else{
                        $cont_facturas = 0;
                    }
                }  
            }
            if($cont_facturas > 0){
                $response->cobro_agendado = 2;
            }else{
                $response->cobro_agendado = 3;
            }
        }

    }elseif($modo_agendado == 'masivo'){

        $id_cliente = $this->input->post('id_cliente');
        $datosFacturas = FacturaSeguro::whereIn('id_poliza',$id_poliza)->select('id','codigo')->get();
        $datosCobros = Cobros_seguros::where(['empezable_id' => $id_cliente, 'estado' => 'agendado'])->first();
        if(count($datosCobros) == 0){
            $datosCobros = Cobros_seguros::whereIn('empezable_id',$id_poliza)->where(['estado' => 'agendado'])->first();
        }
        $cont = 0;
        if(count($datosCobros)){
            foreach ($datosCobros->cobros_facturas as $key => $cob) {
                foreach ($datosFacturas as $key => $fac) {
                    if($fac['id'] == $cob['cobrable_id']){
                        $cont++;
                    }            
                }
            }
        }else{
            $datosPolizas = PolizasModel::whereIn('id',$id_poliza)->first();
            $cont_facturas = 0;
            foreach ($datosPolizas->facturasegurofk as $key => $value) {
                if( ($value['estado'] == 'por_cobrar' && $value['saldo'] == NULL) || ($value['estado'] == 'cobrado_parcial' && $value['saldo'] == NULL ) ){
                    if($value['total'] > 0 ){
                        $cont_facturas++;
                    }else{
                        $cont_facturas = 0;
                        break;
                    }
                }elseif( ($value['estado'] == 'por_cobrar' && $value['saldo'] != NULL) || ($value['estado'] == 'cobrado_parcial' && $value['saldo'] != NULL) ){
                    if($value['saldo'] > 0 ){
                        $cont_facturas++;
                    }else{
                        $cont_facturas = 0;
                        break;
                    }
                }  
            }
            
        }
        if($cont > 0){
            $response->cobro_agendado = 1;
            $response->datos_cobro = ['numero_cobro' => $datosCobros->codigo, 'fecha_cobro' => date('d-m-Y' ,strtotime($datosCobros->created_at)), 'uuid_cobro' => $datosCobros->uuid_cobro];
        }else{
            if($cont_facturas > 0){
                $response->cobro_agendado = 2;
            }else{
                $response->cobro_agendado = 3;
            }
        }
    }
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
    exit;

}


function ajax_carga_acreedores_vida_colectivo(){
    $acreedores = $this->PolizasRepository->verAcreedoresDetalle($_POST['idinteres_detalle']);
    if (count($acreedores) == 0) {
        $acreedores = [];
    } 

        //$acreedores = $acreedores->toArray();    

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($acreedores))->_display();

    exit;


}

}
//*************************************************************************************************
//                         Evento para expirar las facturas
//CREATE EVENT expiration_policy
//ON SCHEDULE EVERY 1 DAY STARTS '2017-25-01 00:00:00'
//DO
//UPDATE pol_polizas set estado ="Expirada" where estado ="Facturada" and `fin_vigencia` < now()
//
//
//**************************************************************************************************
?>