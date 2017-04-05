<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Colaboradores
 * 
 * Modulo para administrar la creacion, edicion de solicitudes.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  05/22/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\Ajustadores\Models\Ajustadores as AjustadoresModel;
use Flexio\Modulo\Ajustadores\Models\AjustadoresContacto as AjustadoresContactoModel;
use Flexio\Modulo\Cliente\Repository\ClienteRepository as clienteRepository;
use Flexio\Modulo\Reclamos\Repository\ReclamosRepository as reclamosRepository;
use Flexio\Modulo\Reclamos\Models\Reclamos as ReclamosModel;
use Flexio\Modulo\Cliente\Models\Cliente as clienteModel;
use Flexio\Modulo\Contabilidad\Models\Impuestos as impuestosModel;
use Flexio\Modulo\Solicitudes\Models\Solicitudes as solicitudesModel;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_catModel;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_detalles as InteresesAsegurados_detalles;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados as InteresesAsegurados;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable as centroModel;
use Flexio\Modulo\CentroFacturable\Repository\CentroFacturableRepository as centroRepository;
use Flexio\Modulo\Ramos\Repository\RamoRepository as RamoRepository;
use Flexio\Modulo\Ramos\Models\Ramos as Ramos;
use Flexio\Modulo\Polizas\Models\Polizas as Polizas;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Models\CatalogoTipoPoliza;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo;
use Flexio\Modulo\SegCatalogo\Repository\SegCatalogoRepository as SegCatalogoRepository;
use Flexio\Modulo\SegInteresesAsegurados\Repository\SegInteresesAseguradosRepository as SegInteresesAseguradosRepository;
use Flexio\Modulo\CatalogoTPoliza\Models\CatalogoTPoliza as CatalogoTPoliza;
use Flexio\Modulo\Agentes\Models\Agentes;
use Flexio\Modulo\Planes\Models\Planes;
use Flexio\Modulo\Planes\Repository\PlanesRepository;
use Flexio\Modulo\Planes\Models\PlanesComisiones;
use Flexio\Modulo\Coberturas\Models\Coberturas as coberturaModel;
use Flexio\Modulo\aseguradoras\Repository\AseguradorasRepository as AseguradorasRepository;
use Flexio\Modulo\Planes\Models\Deducibles;
use Flexio\Modulo\Reclamos\Models\ReclamosCoberturas;
use Flexio\Modulo\Reclamos\Models\ReclamosDeduccion;
use Flexio\Modulo\Reclamos\Models\ReclamosAccidentes;
use Flexio\Modulo\Reclamos\Models\ReclamosDocumentacion as reclamosDocumentosModel;
use Flexio\Modulo\Documentos\Repository\DocumentosRepository as DocumentosRepository;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository as PoliticasRepository;
use Dompdf\Dompdf;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;
use Flexio\Modulo\Ramos\Models\RamosUsuarios;
use Flexio\Modulo\Reclamos\Models\ReclamosBitacora as bitacoraModel;
use Flexio\Modulo\InteresesAsegurados\Models\ArticuloAsegurados as ArticuloModel;
use Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados as CargaModel;
use Flexio\Modulo\InteresesAsegurados\Models\AereoAsegurados as AereoModel;
use Flexio\Modulo\InteresesAsegurados\Models\MaritimoAsegurados as MaritimoModel;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas as PersonasModel;
use Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados as ProyectoModel;
use Flexio\Modulo\InteresesAsegurados\Models\UbicacionAsegurados as UbicacionModel;
use Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados as VehiculoModel;
use Flexio\Modulo\InteresesAsegurados\Repository\InteresesAseguradosRepository as interesesAseguradosRep;
use Flexio\Modulo\Polizas\Models\PolizasArticulo;
use Flexio\Modulo\Polizas\Models\PolizasCarga;
use Flexio\Modulo\Polizas\Models\PolizasAereo;
use Flexio\Modulo\Polizas\Models\PolizasMaritimo;
use Flexio\Modulo\Polizas\Models\PolizasPersonas;
use Flexio\Modulo\Polizas\Models\PolizasProyecto;
use Flexio\Modulo\Polizas\Models\PolizasUbicacion;
use Flexio\Modulo\Polizas\Models\PolizasVehiculo;
use Flexio\Modulo\Polizas\Models\PolizasSalud;
use Flexio\Modulo\Reclamos\Models\ReclamosArticulo;
use Flexio\Modulo\Reclamos\Models\ReclamosCarga;
use Flexio\Modulo\Reclamos\Models\ReclamosAereo;
use Flexio\Modulo\Reclamos\Models\ReclamosMaritimo;
use Flexio\Modulo\Reclamos\Models\ReclamosPersonas;
use Flexio\Modulo\Reclamos\Models\ReclamosProyecto;
use Flexio\Modulo\Reclamos\Models\ReclamosUbicacion;
use Flexio\Modulo\Reclamos\Models\ReclamosVehiculo;
use Flexio\Modulo\Reclamos\Models\ReclamosDetalleSalud;
use Flexio\Modulo\Catalogos\Models\RamosDocumentos as RamosDocumentos;
use Flexio\Modulo\Proveedores\Models\Proveedores;
use Flexio\Modulo\Acreedores\Repository\AcreedoresRepository as AcreedoresRep;

class Reclamos extends CRM_Controller {

    /**
     * @var int
     */
    protected $usuario_id;

    /**
     * @var int
     */
    protected $empresa_id;

    /**
     * @var int
     */
    protected $modulo_id;

    /**
     * @var string
     */
    protected $nombre_modulo;
    private $acreedoresRep;
    private $clienteModel;
    private $impuestosModel;
    private $solicitudesModel;
    private $solicitudesVigenciaModel;
    private $solicitudesPrimaModel;
    private $centroModel;
    private $InteresesAsegurados_catModel;
    protected $clienteRepository;
    protected $PlantillaRepository;
    protected $DocumentosRepository;
    protected $reclamosRepository;
    protected $centroRepository;
    protected $ramoRepository;
    protected $coberturaModel;
    protected $AseguradorasRepository;
    protected $deduciblesModel;
    protected $solicitudesCoberturas;
    protected $solicitudesDeduccion;
    private $Participacion;
    private $reclamosDocumentosModel;
    protected $politicas;
    protected $politicas_general;
    protected $PoliticasRepository;
    private $bitacoraModel;
    private $uuid_reclamo;
    private $ArticuloModel;
    private $CargaModel;
    private $AereoModel;
    private $MaritimoModel;
    private $PersonasModel;
    private $ProyectoModel;
    private $UbicacionModel;
    private $VehiculoModel;
    private $interesesAseguradosRep;
    private $RamosDocumentos;
    private $AcreedoresRep;

    /**
     * @var string
     */
    protected $upload_folder = './public/uploads/';

    function __construct() {
        parent::__construct();


        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuarios::findByUuid($uuid_usuario);

        $this->usuario_id = $usuario->id;

        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;

        $this->roles = $this->session->userdata("roles");

        $clause['empresa_id'] = $this->empresa_id;
        $clause['modulo'] = 'solicitudes';
        $clause['usuario_id'] = $this->usuario_id;
        $clause['role_id'] = $this->roles;

        $this->empresa_id = $empresa->id;
        $this->clienteRepository = new clienteRepository();
        $this->reclamosRepository = new reclamosRepository();
        $this->centroRepository = new centroRepository();
        $this->clienteModel = new clienteModel();
        $this->planesModel = new PlanesRepository();
        $this->impuestosModel = new impuestosModel();
        $this->solicitudesModel = new solicitudesModel();
        $this->InteresesAsegurados_catModel = new InteresesAsegurados_catModel();
        $this->centroModel = new centroModel();
        $this->ramoRepository = new RamoRepository();
        $this->SegCatalogoRepository = new SegCatalogoRepository();
        $this->SegInteresesAseguradosRepository = new SegInteresesAseguradosRepository();
        $this->CatalogoTPoliza = new CatalogoTPoliza();
        $this->AseguradorasRepository = new AseguradorasRepository();
        $this->deduciblesModel = new Deducibles();
        $this->coberturaModel = new coberturaModel();
        $this->reclamosDocumentosModel = new reclamosDocumentosModel();
        $this->load->module(array('documentos'));
        $this->DocumentosRepository = new DocumentosRepository();
        $this->PoliticasRepository = new PoliticasRepository();
        $this->bitacoraModel = new bitacoraModel();

        $this->ArticuloModel = new ArticuloModel();
        $this->CargaModel = new CargaModel();
        $this->AereoModel = new AereoModel();
        $this->MaritimoModel = new MaritimoModel();
        $this->PersonasModel = new PersonasModel();
        $this->ProyectoModel = new ProyectoModel();
        $this->UbicacionModel = new UbicacionModel();
        $this->VehiculoModel = new VehiculoModel();
        $this->interesesAseguradosRep = new interesesAseguradosRep();
        $this->RamosDocumentos = new RamosDocumentos();

        $this->AcreedoresRep = new AcreedoresRep();


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
        if (isset($politicas_transaccion_general2[1]['politica_estado'])) {
            $this->politicasgenerales2 = $politicas_transaccion_general2[1]['politica_estado'];
        } else {
            $this->politicasgenerales2 = "";
        }
    }

    //--------------------------------------------------------------------------

    public function listar() {

        if (!$this->auth->has_permission('acceso', 'reclamos/listar')) {
            // No, tiene permiso, redireccionarlo.
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ingresar a listar Reclamos', 'titulo' => 'Reclamos ');

            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url('/'));
        }

        if ($this->auth->has_permission('editar__cambiarEstado', 'reclamos/editar')) {
            $permiso_estado = 1;
        }else{
            $permiso_estado = 0;
        }

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje),
            "permiso_estado" => $permiso_estado
        ));

        $data = array();

        $this->_Css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/listar.js'
        ));


        //Definir mensaje
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));




        //Verificar permisos para crear
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Reclamos',
            "ruta" => array(
                0 => array("nombre" => "Reclamos", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Reclamos</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );

        if ($this->auth->has_permission('acceso', 'reclamos/crear')) {
            $breadcrumb["menu"] = array(
                "url" => 'javascript:',
                "clase" => 'modalOpcionesCrear',
                "nombre" => "Crear"
            );
            $menuOpciones["#cambiarEstadoReclamosLnk"] = "Cambiar estado";
            //$menuOpciones["#imprimirCartaReclamosLnk"] = "Imprimir carta";
            $menuOpciones["#exportarReclamosLnk"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones;
        }

        //Menu para crear
        $clause = array('empresa_id' => $this->empresa_id);
        //catalogo para buscador        
        $data['menu_crear'] = $this->ramoRepository->listar_cuentas($clause);
        //catalogo para buscador        
        $data['aseguradoras'] = Aseguradoras::where($clause)->get();
        $data['tipo'] = CatalogoTPoliza::get();
        $data['usuarios'] = Usuarios::where(['estado' => 'Activo', 'usuarios_has_empresas.empresa_id' => $this->empresa_id])
                        ->join('usuarios_has_empresas', 'usuarios_has_empresas.usuario_id', '=', 'usuarios.id')->orderBy('nombre', 'asc')->get();
        $data['clientes'] = $this->clienteModel->where(['estado' => 'activo', 'empresa_id' => $this->empresa_id])->orderBy('nombre', 'asc')->get();


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


        $this->template->agregar_titulo_header('Listado de Reclamos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    //--------------------------------------------------------------------------

    public function crear($id_ramo = null, $id_interes = null) {

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }

        if (!$this->auth->has_permission('acceso', 'reclamos/crear')) {
            // No, tiene permiso, redireccionarlo.
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ingresar a crear', 'titulo' => 'Reclamos ');

            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url('reclamos/listar'));
        } else {
            $acceso = 1;
        }

        if (empty($id_ramo)) {
            $ramo_id = !empty($_POST['ramo_id']) ? $_POST['ramo_id'] : '';
        } else {
            $ramo_id = $id_ramo;
        }

        $solicitudes_titulo = Ramos::find($ramo_id);
        $titulo = $solicitudes_titulo->nombre;
        $ramo = $solicitudes_titulo->nombre;
        $tipo_poliza = $solicitudes_titulo->id_tipo_poliza;
        $codigo_ramo = $solicitudes_titulo->codigo_ramo;
        $id_ramo = $solicitudes_titulo->id;
        $idpadre = $solicitudes_titulo->padre_id;
        $tipo_interes_asegurado = $solicitudes_titulo->id_tipo_int_asegurado;

        //-----------------------------------------
        //Ramo
        $ramocadena = $ramo;
        while ($idpadre != 0) {
            $ram = Ramos::where('id', $idpadre)->first();
            $id_ramo = $ram->id;
            $idpadre = $ram->padre_id;
            $ramocadena = $ram->nombre . "/" . $ramocadena;
        }
        $ram1 = Ramos::where('id', $id_ramo)->first();
        $nombrepadre = $ram1->nombre;

        if (strpos($ramocadena, "Salud")) {
            $salud = 1;
        }else{
            $salud = 0;
        }

        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }
        //-----------------------------------------
        
        //Catalogos
        $catalogo_clientes = $this->SegInteresesAseguradosRepository->listar_catalogo('Identificacion', 'orden');
        //Estados de Reclamos
        $estado = $this->SegCatalogoRepository->listar_catalogo('estado_reclamo', 'orden');
        if ($tipo_interes_asegurado != 8) {
            unset($estado[5]);
        }
        //Causa de Reclamo
        $causa = $this->SegCatalogoRepository->listar_catalogo('causa_reclamo', 'orden');
        //Accidente Opciones
        $accidente = $this->SegCatalogoRepository->listar_catalogo('accidente_reclamo', 'orden');
        //Tipo Salud Opciones
        $tiposalud = $this->SegCatalogoRepository->listar_catalogo('tipo_salud_reclamo', 'orden');
        //Ajustadores
        $ajustadoreslista = AjustadoresModel::where("empresa_id", $this->empresa_id)->where("estado", "Activo")->select("id", "nombre", "ruc", "identificacion")->get();

        //-----------------------------------------

        if (!empty($id_interes)) {
            $selInteres = $id_interes;
        } else {
            $selInteres = '';
        }
        //-----------------------------------------

        if ($this->auth->has_permission('acceso', 'reclamos/editar asignación')) {
            $usersList = Usuarios::join("seg_ramos_usuarios", "seg_ramos_usuarios.id_usuario", "=", "usuarios.id")->where(array("usuarios.estado" => 'Activo', "seg_ramos_usuarios.id_ramo" => $ramo_id))->get();
            if ($usersList->count() == 0) {
                $usersList = 0;
            }
        } else {
            $usersList = Usuarios::join("seg_ramos_usuarios", "seg_ramos_usuarios.id_usuario", "=", "usuarios.id")->where(array("usuarios.estado" => 'Activo', "seg_ramos_usuarios.id_ramo" => $ramo_id, "usuarios.id" => $this->usuario_id))->get();
            if ($usersList->count() == 0) {
                $usersList = 0;
            }
        }
        //-----------------------------------------


        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/modules/reclamos/formulario.js',
            'public/assets/js/modules/reclamos/crear.vue.js',
            'public/assets/js/modules/reclamos/component.vue.js',
            'public/assets/js/modules/reclamos/plugins.js',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js'
        ));
        
        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso,
            "ramo_id" => $ramo_id,
            "catalogo_clientes" => $catalogo_clientes,
            "ramo" => $ramo,
            "ajustadoreslista" => $ajustadoreslista,
            "id_tipo_poliza" => $tipo_poliza,
            "causa" => $causa,
            "accidente" => $accidente,
            "tiposalud" => $tiposalud,
            "codigo_ramo" => $codigo_ramo,
            "nombre_padre" => $nombrepadre,
            "estado_reclamos" => $estado,
            "ramoscadena" => $ramocadena,
            "id_tipo_int_asegurado" => $tipo_interes_asegurado,
            //********************************************************************
            "cliente" => "undefined",
            "editar" => "undefined",
            "asegurada" => "undefined",
            "plan" => "undefined",
            "vigencia" => "undefined",
            "prima" => "undefined",
            "estado" => "undefined",
            "participacion" => "undefined",
            "observaciones" => "undefined",
            "uuid_solicitudes" => "undefined",
            "comision" => "undefined",
            "permisos_editar" => $this->auth->has_permission('acceso', 'reclamos/editar') == true ? 1 : 0,
            //********************************************************************
            "desde" => "reclamos",
            "grupogbd" => "",
            "direcciongbd" => "",
            "documentaciones" => "",
            "documentacion_editar" => "",
            "selInteres" => $selInteres,
            //***************
            "usuario_id" => $this->usuario_id,
            "usersList" => $usersList,
            "editar_asignado" => 1,
            "pol" => $_POST['poliza_id'],
            "indcolec" => '',
            "uuid_reclamos" => '',
            "permiso_asignar" => 1,
            "permiso_editar" => 1,
            "documentacionesgbd" => "",
            "validasalud" => $salud,
            "regresar_poliza" => ""
        ));



        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Reclamos: Crear / ' . $titulo,
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<a href="' . base_url() . 'reclamos/listar">Reclamos</a>', "activo" => false),
                2 => array("nombre" => '<b>Crear</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );

        $data = array();
        $data['mensaje'] = $mensaje;
        $data['id_ramo'] = $ramo_id;
        $data['tipo_interes'] = $tipo_interes_asegurado;
        $this->template->agregar_titulo_header('Reclamos: Crear');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    //--------------------------------------------------------------------------

    public function editar($uuid_reclamos = null) {

        $this->uuid_reclamo = $uuid_reclamos;

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));

        if (!$this->auth->has_permission('acceso', 'reclamos/ver') && !$this->auth->has_permission('acceso', 'reclamos/editar')) {
            // No, tiene permiso, redireccionarlo.
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ingresar a editar', 'titulo' => 'Reclamos ');

            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url('reclamos/listar'));
        } else {
            $acceso = 1;
        }

        if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
            $ceditar = 1;
        } else {
            $ceditar = 0;
        }

        if ($this->auth->has_permission('acceso', 'reclamos/editar')) {
            $permiso_editar = 1;
        }else{
            $permiso_editar = 0;
        }

        if ($this->auth->has_permission('editar__cambiarEstado', 'reclamos/editar')) {
            $permiso_estado = 1;
        }else{
            $permiso_estado = 0;
        }

        if ($this->auth->has_permission('editar__asignarA', 'reclamos/editar')) {
            $permiso_asignar = 1;
        }else{
            $permiso_asignar = 0;
        }


        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/formulario.js',
            'public/assets/js/modules/reclamos/crear.vue.js',
            'public/assets/js/modules/reclamos/component.vue.js',
            'public/assets/js/modules/reclamos/plugins.js'
        ));

        $reclamos = $this->reclamosRepository->verReclamos(hex2bin(strtolower($uuid_reclamos)));

        $accidentes_reclamos = $this->reclamosRepository->verAccidentes($reclamos->id);        
        if (count($accidentes_reclamos) == 0) {
            $accidentes_reclamos = 'undefined';
        }
        $coberturas_reclamos = $this->reclamosRepository->verCoberturas($reclamos->id);
        if (count($coberturas_reclamos) == 0) {
            $coberturas_reclamos = 'undefined';
        }
        $deducciones_reclamos = $this->reclamosRepository->verDeducciones($reclamos->id);
        if (count($deducciones_reclamos) == 0) {
            $deducciones_reclamos = 'undefined';
        }


        $reclamos_titulo = Ramos::find($reclamos->id_ramo);
        $titulo = $reclamos_titulo->nombre;
        $ramo = $reclamos_titulo->nombre;
        $tipo_poliza = $reclamos_titulo->id_tipo_poliza;
        $tipo_interes_asegurado = $reclamos->tipo_interes;

        $id_ramo = $reclamos_titulo->id;
        $idpadre = $reclamos_titulo->padre_id;

        //-----------------------------------------
        //Ramo
        $ramocadena = $ramo;
        while ($idpadre != 0) {
            $ram = Ramos::where('id', $idpadre)->first();
            $id_ramo = $ram->id;
            $idpadre = $ram->padre_id;
            $ramocadena = $ram->nombre . "/" . $ramocadena;
        }
        $ram1 = Ramos::where('id', $id_ramo)->first();
        $nombrepadre = $ram1->nombre;

        if (strpos($ramocadena, "Salud")) {
            $salud = 1;
        }else{
            $salud = 0;
        }
        //-------------------------------------------


        
        //Catalogos
        $catalogo_clientes = $this->SegInteresesAseguradosRepository->listar_catalogo('Identificacion', 'orden');
        //Estados de Reclamos
        $estado = $this->SegCatalogoRepository->listar_catalogo('estado_reclamo', 'orden');
        if ($tipo_interes_asegurado != 8) {
            unset($estado[5]);
        }
        //Causa de Reclamo
        $causa = $this->SegCatalogoRepository->listar_catalogo('causa_reclamo', 'orden');
        //Accidente Opciones
        $accidente = $this->SegCatalogoRepository->listar_catalogo('accidente_reclamo', 'orden');
        //Tipo Salud Opciones
        $tiposalud = $this->SegCatalogoRepository->listar_catalogo('tipo_salud_reclamo', 'orden');
        //Ajustadores
        $ajustadoreslista = AjustadoresModel::where("empresa_id", $this->empresa_id)->where("estado", "Activo")->select("id", "nombre", "ruc", "identificacion")->get();

        //-----------------------------------------

        if (!empty($id_interes)) {
            $selInteres = $id_interes;
        } else {
            $selInteres = '';
        }
        //-----------------------------------------

        $usersList = Usuarios::join("seg_ramos_usuarios", "seg_ramos_usuarios.id_usuario", "=", "usuarios.id")->where(array("usuarios.estado" => 'Activo', "seg_ramos_usuarios.id_ramo" => $reclamos->id_ramo))->get();
            if ($usersList->count() == 0) {
                $usersList = 0;
            }
        //-----------------------------------------

        $reclamoInfo = ReclamosModel::where("id", $reclamos->id)->first();
        $reclamoInfo->uuid_reclamos = bin2hex($reclamoInfo->uuid_reclamos);

        $reclamoInfoAcc = ReclamosAccidentes::where("id_reclamo", $reclamos->id)->select("id_tipo_accidente")->get();
        $acci = array();
        foreach ($reclamoInfoAcc as $value) {
            array_push($acci, $value['id_tipo_accidente']);
        }

        $id_poliza_interes = 0;
        $reclamoInfoCob = ReclamosCoberturas::where("id_reclamo", $reclamos->id)->select("id_poliza_cobertura")->get();
        $cob = array();
        foreach ($reclamoInfoCob as $value) {
            array_push($cob, $value['id_poliza_cobertura']);
        }

        $reclamoInfoDed = ReclamosDeduccion::where("id_reclamo", $reclamos->id)->select("id_poliza_deduccion", "valor_deduccion")->get();
        $ded = array();
        $valorded = array();
        foreach ($reclamoInfoDed as $value) {
            array_push($ded, $value['id_poliza_deduccion']);
            array_push($valorded, $value['valor_deduccion']);
        }


        //------------------------------------------------------------------
        $cont_nivel1 = count($this->RamosDocumentos->where(['id_ramo' => $reclamos->id_ramo])
                        ->where('estado', "=", "Activo")
                        ->where('modulo', "=", "reclamo")
                        ->get());

        $ramopadre = Ramos::where('id', $reclamos->id_ramo)
                ->get();

        foreach ($ramopadre as $item) {
            if (isset($item)) {
                $id_ramo_padre = $item['padre_id'];
            } else {
                $id_ramo_padre = -1;
            }
        }
        $cont_nivel2 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre])
                        ->where('estado', "=", "Activo")
                        ->where('modulo', "=", "reclamo")
                        ->get()->toArray());

        $ramopadre2 = Ramos::where('id', $id_ramo_padre)
                        ->get()->toArray();
        foreach ($ramopadre2 as $item) {
            if (isset($item)) {
                $id_ramo_padre2 = $item['padre_id'];
            } else {
                $id_ramo_padre2 = -1;
            }
        }
        if ($cont_nivel2 > 0) {
            $cont_nivel3 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre2])
                            ->where('estado', "=", "Activo")
                            ->where('modulo', "=", "reclamo")
                            ->get()->toArray());
            $ramopadre3 = Ramos::where('id', $id_ramo_padre2)
                            ->get()->toArray();

            foreach ($ramopadre3 as $item) {

                if (isset($item)) {
                    $id_ramo_padre3 = $item['padre_id'];
                } else {
                    $id_ramo_padre3 = -1;
                }
            }
        } else {
            $id_ramo_padre3 = -1;
        }
        $cont_nivel4 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre3])
                        ->where('estado', "=", "Activo")
                        ->where('modulo', "=", "reclamo")
                        ->get());
        if ($cont_nivel1 > 0) {
            $documentaciones = $this->RamosDocumentos->where(['id_ramo' => $reclamos->id_ramo])
                    ->where('estado', "=", "Activo")
                    ->where('modulo', "=", "reclamo")
                    ->get();
        } else if ($cont_nivel2 > 0) {
            $documentaciones = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre])
                    ->where('estado', "=", "Activo")
                    ->where('modulo', "=", "reclamo")
                    ->get();
        } else if ($cont_nivel2 > 0 && $cont_nivel3 > 0) {
            $documentaciones = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre2])
                    ->where('estado', "=", "Activo")
                    ->where('modulo', "=", "reclamo")
                    ->get();
        } else if ($cont_nivel4 > 0) {
            $documentaciones = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre3])
                    ->where('estado', "=", "Activo")
                    ->where('modulo', "=", "reclamo")
                    ->get();
        } else {
            $documentaciones = 0;
        }
        if (isset($reclamoInfo->uuid_reclamos) && ($reclamoInfo->uuid_reclamos != "")) {
            $rec = $this->reclamosRepository->verReclamos(hex2bin(strtolower($reclamoInfo->uuid_reclamos)));
            $documentacionesgbd = $this->reclamosDocumentosModel->where(['id_reclamo' => $rec->id])->select('valor')->get();
        } else {
            $documentacionesgbd = "";
        }
        //------------------------------------------------------------------

        $data = array();
        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
            "acceso" => $acceso,
            "ramo_id" => $reclamos->id_ramo,
            "catalogo_clientes" => $catalogo_clientes,
            "ramo" => $ramo,
            "codigo_ramo" => '',
            "ramoscadena" => '',
            "ajustadoreslista" => $ajustadoreslista,
            "id_tipo_poliza" => $tipo_poliza,
            "causa" => $causa,
            "accidente" => $accidente,
            "tiposalud" => $tiposalud,
            "estado_reclamos" => $estado,
            "id_tipo_int_asegurado" => $tipo_interes_asegurado,
            "uuid_reclamos" => $reclamoInfo->uuid_reclamos,
            "permisos_editar" => $this->auth->has_permission('acceso', 'reclamos/editar') == true ? 1 : 0,
            //********************************************************************
            "desde" => "reclamos",
            "selInteres" => $selInteres,
            "editar" => 1,
            //***************
            "usuario_id" => $this->usuario_id,
            "usersList" => $usersList,
            "editar_asignado" => 1,
            "pol" => $reclamos->id_poliza,
            //***************
            "usuario_id" => $reclamos->id_usuario,
            "usersList" => $usersList,
            "numero_reclamo" => $reclamos->numero, 
            "reclamos" => json_encode($reclamoInfo),
            "reclamosAccidentes" => json_encode($acci),
            "reclamosCoberturas" => json_encode($cob),
            "reclamosDeduccion" => json_encode($ded),
            "saludDeduccion" => json_encode($valorded),
            "id_reclamo" => $reclamos->id,
            "indcolec" => '' ,
            "permiso_estado" => $permiso_estado,
            "permiso_editar" => $permiso_editar,
            "permiso_asignar" => $permiso_asignar,
            "documentaciones" => $documentaciones,
            "documentacionesgbd" => $documentacionesgbd != "" ? $documentacionesgbd : "",
            "validasalud" => $salud,
            "cliente" => "undefined",
            "regresar_poliza" => ""
        ));

        $titulo = $reclamos->numero;

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Reclamos: N°. ' . $titulo,
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<a href="' . base_url() . 'reclamos/listar">Reclamos</a>', "activo" => false),
                2 => array("nombre" => '<b>' . $titulo . '</b>', "activo" => true),
            ),
            "filtro" => false,
            "menu" => array(
                'url' => 'javascript:',
                'nombre' => "Acción",
                "opciones" => array(
                    //"reclamos/bitacora/" . strtoupper(bin2hex($uuid_reclamos)) => "Bitacora",
                )
            ),
            //"historial" => true,
        );
        if ($this->auth->has_permission('acceso', 'reclamos/crear')) {
            $breadcrumb["menu"] = array(
                "url" => 'javascript:',
                "clase" => 'modalOpcionesCrear',
                "nombre" => "Acción"
            );
            $menuOpciones["#imprimirReclamosLnk"] = "Imprimir";
            $menuOpciones["#exportarReclamosLnk"] = "Exportar";
            $menuOpciones["#subirDocumento"] = "Subir Documento";
            $breadcrumb["menu"]["opciones"] = $menuOpciones;
        }
        $data['subpanels'] = [];
        $data['mensaje'] = $mensaje;
        $data['id_ramo'] = $reclamos->ramo_id;
        $data['tipo_interes'] = $tipo_interes_asegurado;
        $this->template->agregar_titulo_header('Reclamos: Editar');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    //--------------------------------------------------------------------------

    function guardar() {
        if ($_POST) {
            //print_r($_POST["campo"]);
            unset($_POST["camporeclamo"]["guardar"]);
            $campo = Util::set_fieldset("camporeclamo");
            $campointeres = Util::set_fieldset("campo");    
            $campodocumentacion = Util::set_fieldset("campodocumentacion");
            Capsule::beginTransaction();
            try {
                if (empty($campo['uuid'])) {
                    unset($campo['uuid']);
                    //Crear en Solicitudes
                    $campo["uuid_reclamos"] = Capsule::raw("ORDER_UUID(uuid())");
                    $campo['empresa_id'] = $this->empresa_id;
                    $clause['empresa_id'] = $this->empresa_id;
                    $clause['id_ramo'] = $campo["id_ramo"];
                    $total = $this->reclamosRepository->listar($clause);
                    $year = Carbon::now()->format('y');
                    $codigo = Util::generar_codigo($_POST['codigo_ramo'] . "-" . $year, count($total) + 1);
                    $campo["numero"] = $codigo;
                    $campo["id_poliza"] = $campo['id_poliza'];
                    $tipo_interes = $campo['tipo_interes'];                    
                    $reclamos = ReclamosModel::create($campo);

                    //Agregar Coberturas al Reclamo
                    if (isset($_POST['campocoberturas']) && $_POST['campocoberturas'] != "") {
                        $cob = trim( $_POST['campocoberturas'] ,",");
                        $cob1 = explode(",", $cob);
                        $polizacob = new Flexio\Modulo\Polizas\Models\PolizasCobertura;
                        $coberturas1 = $polizacob->whereIn("id", $cob1)->select("cobertura", "valor_cobertura", "id")->get()->toArray();
                        foreach ($coberturas1 as $value) {
                            $cam = array("cobertura" => $value['cobertura'], "valor_cobertura" => $value['valor_cobertura'], "id_reclamo" => $reclamos->id, "id_poliza_cobertura" => $value['id']);
                            $cobertura_pol = ReclamosCoberturas::create($cam);
                        }
                    }
                    //Agregar Deducciones al Reclamo
                    if (isset($_POST['campodeducciones']) && $_POST['campodeducciones'] != "") {
                        $ded = trim( $_POST['campodeducciones'] ,",");
                        $ded1 = explode(",", $ded);

                        $polizaded = new Flexio\Modulo\Polizas\Models\PolizasDeduccion;
                        $deducciones1 = $polizaded->whereIn("id", $ded1)->select("deduccion", "valor_deduccion", "id")->get()->toArray();
                        foreach ($deducciones1 as $value) {
                            $cam = array("deduccion" => $value['deduccion'], "valor_deduccion" => $value['valor_deduccion'], "id_reclamo" => $reclamos->id, "id_poliza_deduccion" => $value['id']);
                            $deduccion_pol = ReclamosDeduccion::create($cam);
                        }
                    }

                    $campointeres['id_reclamo'] = $reclamos->id;
                    $campointeres['empresa_id'] = $this->empresa_id;
                    $campointeres['numero'] = $reclamos->numero; 

                    if ($tipo_interes == 1) {
                        $campointeres['uuid_articulo'] = Capsule::raw("ORDER_UUID(uuid())");
                        $interes = ReclamosArticulo::create($campointeres);
                    }else if ($tipo_interes == 2) {
                        $interes = ReclamosCarga::create($campointeres);
                    }else if ($tipo_interes == 3) {
                        $interes = ReclamosAereo::create($campointeres);
                    }else if ($tipo_interes == 4) {
                        $campointeres['uuid_casco_maritimo'] = Capsule::raw("ORDER_UUID(uuid())");
                        $interes = ReclamosMaritimo::create($campointeres);
                    }else if ($tipo_interes == 5) {
                        $campointeres = PolizasPersonas::where("id", $campo['id_interes_asegurado'])->select("nombrePersona", "identificacion", "fecha_nacimiento", "estado_civil", "nacionalidad", "sexo", "estatura", "peso", "telefono_residencial", "telefono_oficina", "direccion_residencial", "direccion_laboral", "observaciones", "empresa_id", "telefono_principal", "direccion_principal", "estado", "correo", "detalle_certificado")->first()->toArray();
                        $campointeres['id_reclamo'] = $reclamos->id;
                        $campointeres['empresa_id'] = $this->empresa_id;
                        $campointeres['numero'] = $reclamos->numero; 
                        unset($campointeres['id']);
                        $interes = ReclamosPersonas::create($campointeres);
                    }else if ($tipo_interes == 6) {
                        $campointeres['uuid_proyecto'] = Capsule::raw("ORDER_UUID(uuid())");
                        $interes = ReclamosProyecto::create($campointeres);
                    }else if ($tipo_interes == 7) {
                        $campointeres['uuid_ubicacion'] = Capsule::raw("ORDER_UUID(uuid())");
                        $interes = ReclamosUbicacion::create($campointeres);
                    }else if ($tipo_interes == 8) {
                        $campointeres['uuid_vehiculo'] = Capsule::raw("ORDER_UUID(uuid())");
                        $interes = ReclamosVehiculo::create($campointeres);
                        $accidentes = $_POST['campoaccidente'];
                        foreach ($accidentes as $value) {
                            $acc=array();
                            $acc['id_reclamo'] = $reclamos->id;
                            $acc['id_tipo_accidente'] = $value;
                            $accidente = ReclamosAccidentes::create($acc);
                        }
                    } 

                    $camposalud = array("id_reclamo" => $reclamos->id);
                    ReclamosDetalleSalud::where("detalle_unico",$_POST['camporeclamo_salud']['detalle_unico'])->update($camposalud);

                    //guardar tabla documentacion
                    if (isset($campodocumentacion['opcion']) && $campodocumentacion['opcion'] != "") {
                        $arreglo_documentacion = explode(",", $campodocumentacion['opcion']);
                        $cantidad_doc = $campodocumentacion['cantidad_check'];

                        for ($h = 0; $h <= count($arreglo_documentacion); $h++) {
                            if (isset($arreglo_documentacion[$h]) && $arreglo_documentacion[$h] != '') {
                                $campodocumentacion['id_reclamo'] = $reclamos->id;
                                $campodocumentacion['valor'] = $arreglo_documentacion[$h];
                                $this->reclamosDocumentosModel->create($campodocumentacion);
                            }
                        }
                    }

                    //Subir documentos
                    if (!empty($_FILES['file'])) {
                        $id_rec = $reclamos->id;
                        $modeloInstancia = ReclamosModel::find($id_rec);

                        $this->documentos->subir($modeloInstancia);
                    }                  


                } else {
                    //Actualizar en Tabla Reclamos
                    $reclamo = ReclamosModel::findByUuid($campo['uuid']);
                    $idreclamo = $reclamo->id;
                    $codigo = $reclamo->numero;
                    unset($campo['uuid']);
                    $tipo_interes = $campo['tipo_interes'];
                    unset($campo['tipo_interes']);                 
                    $reclamos = ReclamosModel::where("id", $idreclamo)->update($campo);

                    //$c = ReclamosCoberturas::where("id_reclamo", $idreclamo)->delete();
                    //Agregar Coberturas al Reclamo
                    if (isset($_POST['campocoberturas']) && $_POST['campocoberturas'] != "") {
                        $cob = trim( $_POST['campocoberturas'] ,",");
                        $cob1 = explode(",", $cob);

                        $coberturaanterior = ReclamosCoberturas::where("id_reclamo", $idreclamo)->select("id_poliza_cobertura")->get()->toArray();
                        $cobviejo = array();
                        foreach ($coberturaanterior as $val) { array_push($cobviejo, $val['id_poliza_cobertura']); }

                        $polizacob = new Flexio\Modulo\Polizas\Models\PolizasCobertura;
                        $coberturas1 = $polizacob->whereIn("id", $cob1)->select("cobertura", "valor_cobertura", "id")->get()->toArray();

                        foreach ($coberturas1 as $value) {
                            $cam = array("cobertura" => $value['cobertura'], "valor_cobertura" => $value['valor_cobertura'], "id_reclamo" => $idreclamo, "id_poliza_cobertura" => $value['id']);
                            if (!in_array($value['id'], $cobviejo)) {
                                $cobertura_pol = ReclamosCoberturas::create($cam);
                            }                            
                        }
                        $cober1 = ReclamosCoberturas::where("id_reclamo", $idreclamo)->whereNotIn("id_poliza_cobertura", $cob1)->delete();
                    }
                    //$d = ReclamosDeduccion::where("id_reclamo", $idreclamo)->delete();
                    //Agregar Deducciones al Reclamo
                    if (isset($_POST['campodeducciones']) && $_POST['campodeducciones'] != "") {
                        $ded = trim( $_POST['campodeducciones'] ,",");
                        $ded1 = explode(",", $ded);

                        $deduccionanterior = ReclamosDeduccion::where("id_reclamo", $idreclamo)->select("id_poliza_deduccion")->get()->toArray();
                        $dedviejo = array();
                        foreach ($deduccionanterior as $val) { array_push($dedviejo, $val['id_poliza_deduccion']); }

                        $polizaded = new Flexio\Modulo\Polizas\Models\PolizasDeduccion;
                        $deducciones1 = $polizaded->whereIn("id", $ded1)->select("deduccion", "valor_deduccion", "id")->get()->toArray();

                        foreach ($deducciones1 as $value) {
                            $cam = array("deduccion" => $value['deduccion'], "valor_deduccion" => $value['valor_deduccion'], "id_reclamo" => $idreclamo, "id_poliza_deduccion" => $value['id']);
                            if (!in_array($value['id'], $dedviejo)) {
                                $deduccion_pol = ReclamosDeduccion::create($cam);
                            }
                        }
                        $deduc1 = ReclamosDeduccion::where("id_reclamo", $idreclamo)->whereNotIn("id_poliza_deduccion", $ded1)->delete();
                    }




                    $campointeres['id_reclamo'] = $idreclamo;
                    $campointeres['empresa_id'] = $this->empresa_id;
                    $campointeres['numero'] = $reclamo->numero; 

                    unset($campointeres['tipo_id']);

                    if ($tipo_interes == 1) {
                        unset($campointeres['uuid_articulo']);
                        $interes = ReclamosArticulo::where("id_reclamo", $idreclamo)->update($campointeres);
                    }else if ($tipo_interes == 2) {
                        $interes = ReclamosCarga::where("id_reclamo", $idreclamo)->update($campointeres);
                    }else if ($tipo_interes == 3) {
                        $interes = ReclamosAereo::where("id_reclamo", $idreclamo)->update($campointeres);
                    }else if ($tipo_interes == 4) {
                        unset($campointeres['uuid_casco_maritimo']);
                        $interes = ReclamosMaritimo::where("id_reclamo", $idreclamo)->update($campointeres);
                    }else if ($tipo_interes == 5) {
                        $campointeres = PolizasPersonas::where("id", $campo['id_interes_asegurado'])->select("nombrePersona", "identificacion", "fecha_nacimiento", "estado_civil", "nacionalidad", "sexo", "estatura", "peso", "telefono_residencial", "telefono_oficina", "direccion_residencial", "direccion_laboral", "observaciones", "empresa_id", "telefono_principal", "direccion_principal", "estado", "correo", "detalle_certificado")->first()->toArray();
                        $interes = ReclamosPersonas::where("id_reclamo", $idreclamo)->update($campointeres);
                    }else if ($tipo_interes == 6) {
                        unset($campointeres['uuid_proyecto']);
                        $interes = ReclamosProyecto::where("id_reclamo", $idreclamo)->update($campointeres);
                    }else if ($tipo_interes == 7) {
                        unset($campointeres['uuid_ubicacion']);
                        $interes = ReclamosUbicacion::where("id_reclamo", $idreclamo)->update($campointeres);
                    }else if ($tipo_interes == 8) {                        
                        $interes = ReclamosVehiculo::where("id_reclamo", $idreclamo)->update($campointeres);
                        $accidentes = $_POST['campoaccidente'];
                        $accidentesviejos = ReclamosAccidentes::where("id_reclamo", $idreclamo)->select("id_tipo_accidente")->get()->toArray();
                        $accviejo = array();
                        foreach ($accidentesviejos as $value) { array_push($accviejo, $value['id_tipo_accidente']); }
                        //$accidente1 = ReclamosAccidentes::where("id_reclamo", $idreclamo)->delete();
                        $accinuevos = array();
                        foreach ($accidentes as $value) {
                            $acc=array();
                            $acc['id_reclamo'] = $idreclamo;
                            $acc['id_tipo_accidente'] = $value;
                            array_push($accinuevos, $value);
                            if (!in_array($value, $accviejo)) {
                                $accidente = ReclamosAccidentes::create($acc);
                            }
                        }
                        $accidente1 = ReclamosAccidentes::where("id_reclamo", $idreclamo)->whereNotIn("id_tipo_accidente", $accinuevos)->delete();
                    } 

                    $camposalud = array("id_reclamo" => $idreclamo);
                    ReclamosDetalleSalud::where("detalle_unico",$_POST['camporeclamo_salud']['detalle_unico'])->update($camposalud);

                    //guardar tabla documentacion
                    if (isset($campodocumentacion['opcion']) && $campodocumentacion['opcion'] != "") {
                        $this->reclamosDocumentosModel->where(['id_reclamo' => $idreclamo])->delete();

                        $arreglo_documentacion = explode(",", $campodocumentacion['opcion']);
                        $cantidad_doc = $campodocumentacion['cantidad_check'];

                        for ($h = 0; $h <= count($arreglo_documentacion); $h++) {
                            if ($arreglo_documentacion[$h] != '') {

//                                $documentacion = count($this->solicitudesDocumentosModel->where('id_solicitud', "=", $id_solicitud)->where('valor', "=", "" . $arreglo_documentacion[$h] . "")->get()->toArray());
//                                if ($documentacion == 0) {
                                $campodocumentacion['id_reclamo'] = $idreclamo;
                                $campodocumentacion['valor'] = "" . $arreglo_documentacion[$h] . "";
                                $this->reclamosDocumentosModel->create($campodocumentacion);
//                                }
                            }
                        }
                    }

                    //Subir documentos
                    if (!empty($_FILES['file'])) {
                        $id_rec = $idreclamo;
                        $modeloInstancia = ReclamosModel::find($id_rec);
                        $this->documentos->subir($modeloInstancia);
                    }  

                }
                Capsule::commit();
            } catch (ValidationException $e) {
                log_message('error', $e);
                Capsule::rollback();
            }
            //if (!is_null($reclamos) !is_null($solicitudesprima) || !is_null($solicitudesparticipacion) || !is_null($solicitudes) )) {
            if (!is_null($reclamos)){
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Reclamo ' . $codigo . '');
            } else {
                $mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su reclamo no fue procesado');
            }
        } else {
            $mensaje = array('class' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su reclamo no fue procesada');
        }

        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('reclamos/listar'));
    }

    
    //-------------------------------------------------------------------------

    public function ocultotabla() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tabla.js'
        ));

        $this->load->view('tabla');
    }

    //--------------------------------------------------------------------------

    public function ajax_listar($grid = NULL) {


        $clause = array(
            "usuario_id" => $this->usuario_id
        );

        $clause['empresa_id'] = $this->empresa_id;


        if ($this->input->post('uuid_aseguradora')) {
            $ase = $this->AseguradorasRepository->verAseguradora(hex2bin(strtolower($this->input->post('uuid_aseguradora'))));
            $id_aseguradora = $ase->id;

            $clause['aseguradora_id'] = $id_aseguradora;
        }
        //**************************************************
        // clause modulo clientes detalle tab solicitudes
        //**************************************************
        $uuid = $this->input->post('uuid');

        $modulo = $this->input->post('modulo');
        
        //**************************************************
        // clause modulo clientes detalle tab solicitudes
        //**************************************************
        $no_poliza = $this->input->post('no_poliza', true);
        $no_caso = $this->input->post('no_caso', true);
        $no_certificado = $this->input->post('no_certificado', true);
        $cliente = $this->input->post('cliente', true);
        $aseguradora = $this->input->post('aseguradora', true);
        $ramo = $this->input->post('ramo', true);
        $ramo_id = $this->input->post('ramo_id', true);
        $inicio_creacion = $this->input->post('fecha_inicio', true);
        $fin_creacion = $this->input->post('fecha_fin', true);
        $usuario = $this->input->post('usuario', true);
        $estado = $this->input->post('estado', true);
        $cliente = $this->input->post('cliente', true);
        $aseguradora = $this->input->post('aseguradora', true);
        $tipo_poliza = $this->input->post('id_tipo_poliza', true);

        if (!empty($no_poliza)) {
            $clause["numero_poliza"] = $no_poliza;
        }
        if (!empty($no_caso)) {
            $clause["numero_caso"] = $no_caso;
        }
        if (!empty($no_certificado)) {
            $clause["numero_certificado"] = $no_certificado;
        }
        if (!empty($cliente)) {
            $clause["id_cliente"] = $cliente;
        }
        if (!empty($aseguradora)) {
            $clause["aseguradora_id"] = $aseguradora;
        }
        if (!empty($ramo)) {
            $clause["ramo"] = $ramo;
        }
        if (!empty($ramo_id)) {
            $clause["ramo_id"] = $ramo_id;
        }
        if (!empty($inicio_creacion)) {
            $fecha_inicio = Carbon::createFromFormat('m/d/Y', $inicio_creacion, 'America/Panama')->format('Y-m-d');
            $clause["fecha_desde"] = $fecha_inicio;
        }
        if (!empty($fin_creacion)) {
            $fecha_fin = Carbon::createFromFormat('m/d/Y', $fin_creacion, 'America/Panama')->format('Y-m-d');
            $clause["fecha_hasta"] = $fecha_fin;
        }  
        if (!empty($usuario)) {
            $clause["id_usuario"] = $usuario;
        }
        if (!empty($estado)) {
            $clause["estado"] = $estado;
        }
        $clause["empresa_id"] = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->reclamosRepository->listar_reclamos($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = $this->reclamosRepository->listar_reclamos($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows)) {
            foreach ($rows AS $i => $row) {
                $uuid_reclamos = bin2hex($row->uuid_reclamos);
                $uuid_polizas = bin2hex($row->uuid_polizas);
                $uuid_cliente = bin2hex($row->uuid_cliente);
                //$uuid_aseguradora = bin2hex($row->aseguradora->uuid_aseguradora);
                //$now = Carbon::now();
                $url = base_url("reclamos/editar/$uuid_reclamos");
                $urlpolizas = base_url("polizas/editar/$uuid_polizas");
                $urlbitacora = base_url("reclamos/bitacora/$uuid_reclamos");

                //$hidden_options = ""; 
                $link_option='<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                $hidden_options = '<a href="' . $url . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success" >Ver Reclamo</a>';
                $hidden_options .= '<a href="' . $urlpolizas . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success" >Ver Póliza</a>';
                //$hidden_options .= '<a href="' . $urlbitacora . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success bitacora_solicitud" data-type="' . $row['id'] . '" >Bitácora</a>';
                //$hidden_options .= '<a href="" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success subir_archivos_solicitudes" data-type="' . $row['id'] . '" >Subir Archivos</a>';

                //Colores del Estado
                $estado_color = $row->estado == "Legal" ? 'background-color: #F8AD46' : ( $row->estado == "En pago" ? 'background-color: blue' : ($row->estado == "Cerrado" ? 'background-color: #5cb85c' : ($row->estado == "Pendiente doc." ? 'background-color: gold' : ($row->estado == "Anulado" ? 'background-color: #000000' : 'background-color: #5bc0de'))));

                $modalstateanalisis = '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En analisis" class="btn btn-block btn-outline massive" id="en_analisis" style="background-color: #5bc0de; color: white;">En analisis</a>';
                $modalstatepago = '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En pago" class="btn btn-block btn-outline massive" id="en_pago" style="background-color: blue; color: white;">En pago</a>';
                $modalstatependiente = '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente doc." class="btn btn-block btn-outline massive" id="pendiente_doc" style="background-color: gold; color: white;">Pendiente doc.</a>';
                $modalstatecerrado = '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Cerrado" class="btn btn-block btn-outline massive" id="cerrado" style="background-color: #5cb85c; color: white;">Cerrado</a>';
                $modalstateanulado = '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulado" class="btn btn-block btn-outline massive" id="anulado" style="background-color: #000000; color: white;">Anulado</a>';
                $modalstatelegal = '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Legal" class="btn btn-block btn-outline massive" id="legal" style="background-color: #F8AD46; color: white;">Legal</a>';

                $modalstate = "";
                
                $politicas_general = $this->politicas_general;
                $politicas = $this->politicas;
                $validar_politicas = $this->politicasgenerales;
                $validar_politicas2 = $this->politicasgenerales2;
                if ($politicas_general > 0) {
//                    if (in_array(21, $politicas) || in_array(22, $politicas)) {

                    if (in_array(21, $politicas) && $validar_politicas == 21) {
                        
                    } else if (in_array(22, $politicas) && $validar_politicas == 22) {
                        
                    } else if (in_array(21, $politicas) && in_array(22, $politicas) && $validar_politicas == 21 && $validar_politicas == 22) {
                        
                    } else if ($validar_politicas == 21 && $validar_politicas2 == 22) {
                        
                    } else if ($validar_politicas == 21) {
                        
                    } else if ($validar_politicas == 22) {
                        
                    }
                } else {                    
                    switch ($row->estado) {
                        case 'Legal':
                            $modalstate = $modalstateanalisis.$modalstatepago.$modalstatependiente.$modalstatecerrado.$modalstateanulado;
                            break;
                        case 'Pendiente doc.':
                            $modalstate = $modalstatelegal.$modalstateanalisis.$modalstatepago.$modalstatecerrado.$modalstateanulado;
                            break;
                        case 'En analisis':
                            $modalstate = $modalstatelegal.$modalstatepago.$modalstatependiente.$modalstatecerrado.$modalstateanulado;
                            break;
                        case 'En pago':
                            $modalstate = $modalstatelegal.$modalstateanalisis.$modalstatependiente.$modalstatecerrado.$modalstateanulado;
                            break;
                        case 'Cerrado':
                            //$modalstate = $modalstatelegal.$modalstateanalisis.$modalstatepago.$modalstatependiente.$modalstateanulado;ç
                            $modalstate = "";
                            break;
                        case 'Anulado':
                            //$modalstate = $modalstatelegal.$modalstateanalisis.$modalstatepago.$modalstatependiente.$modalstatecerrado;
                            $modalstate = "";
                            break;
                        default:                            
                            break;
                    }                    
                }

                
                $modalanulado = '<div class="row"><div class="col-md-6 form-group"><label>Reclamo</label><br><input type="text" name="nreclamo" class="form-control" value="' . $row->recnumero . '" disabled=""></div><div class="col-md-6 form-group"><label>Cliente</label><br><input type="text" class="form-control" name="ncliente" value="' . $row->clinombre . '" disabled=""></div></div><div class="row"><div class="col-md-12 form-group"><label>Razón</label><br><textarea name="motivoanular" id="motivoanula" class="form-control"></textarea></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><input type="hidden" name="id_reclamo" class="form-control" value="' . $row->id . '" disabled=""><button class="btn btn-success massive" id="guardaranular" data-estado-anterior="' . $row->estado . '" data-estado="Anulado">Guardar</button></div></div>';

                $modalcerrado = '<div class="row"><div class="col-md-6 form-group"><label>Reclamo</label><br><input type="text" name="nreclamo" class="form-control" value="' . $row->recnumero . '" disabled=""></div><div class="col-md-6 form-group"><label>Cliente</label><br><input type="text" class="form-control" name="ncliente" value="' . $row->clinombre . '" disabled=""></div></div><div class="row"><div class="col-md-12 form-group"><label>Razón</label><br><textarea name="motivocerrar" id="motivocerrar" class="form-control"></textarea></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><input type="hidden" name="id_reclamo" class="form-control" value="' . $row->id . '" disabled=""><button class="btn btn-success massive" id="guardarcerrar" data-estado-anterior="' . $row->estado . '" data-estado="Cerrado">Guardar</button></div></div>';

                $actualizacion = explode(" ", $row->updated_at);

                if ($row->estado != "Cerrado" && $row->estado != "Anulado") {
                    $fecha_actual = strtotime(date("Y-m-d H:i:00",time()));
                    $fecha_entrada = strtotime("".$row->fecha_seguimiento." 00:00:00");
                    if ($fecha_actual > $fecha_entrada && $row->fecha_seguimiento != "" && $row->fecha_seguimiento != null) {
                        $seguimiento = '<span style="color:white; background-color:red" class="btn btn-xs btn-block">' . $row->fecha_seguimiento . '</span>';
                    }else{
                        $seguimiento = $row->fecha_seguimiento;
                    }
                }else{
                    $seguimiento = $row->fecha_seguimiento;
                }

                $response->rows[$i]["id"] = $row->id;
                $response->rows[$i]["cell"] = array(
                    $row->id,
                    '<a href="' . base_url('reclamos/editar/' . $uuid_reclamos) . '" style="color:blue;">' . Util::verificar_valor($row->recnumero) . '</a>',
                    '<a href="' . base_url('polizas/editar/' . $uuid_polizas) . '" style="color:blue;">' . Util::verificar_valor($row->polnumero) . '</a>',
                    $row->ramo,
                    $row->numero_caso,
                    '<a href="' . base_url('clientes/ver/' . $uuid_cliente.'?mod=recl') . '" style="color:blue;">' . Util::verificar_valor($row->clinombre) . '</a>',
                    /*'<a href="' . base_url('aseguradoras/editar/' . $uuid_aseguradora) . '" style="color:blue;">' . Util::verificar_valor($row->aseguradora->nombre) . '</a>',*/
                    $row->fecha,
                    $row->fecha_siniestro,
                    Util::verificar_valor($row->usunombre . " " . $row->usuapellido),
                    $actualizacion[0],
                    $seguimiento,
                    !empty($row->estado) ? '<span style="color:white; ' . $estado_color . '" class="btn btn-xs btn-block estadoReclamos" data-id="' . $row['id'] . '" data-estado="' . $row->estado . '">' . ucwords($row->estado) . '</span>' : "",
                    $link_option,
                    $hidden_options,
                    $modalstate,
                    $modalanulado,
                    $modalcerrado
                );
                $i++;
            }
        }
        echo json_encode($response);
        exit;
    }

    //--------------------------------------------------------------------------

    public function ajax_cambiar_estado_reclamos() {

        $campos = $this->input->post('campo');
        $ids = $campos['ids'];
        $empresa_id = $this->empresa_id;
        $campo = ['estado'=> $campos['estado']];

        try {
            $msg = $reclamo = ReclamosModel::where('empresa_id', $empresa_id)->whereIn('id',$ids)->update($campo);
        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        print json_encode($msg);
        exit;
    }

    //--------------------------------------------------------------------------

    public function ajax_verifica_poliza() {

        $Polizas = new Flexio\Modulo\Polizas\Models\Polizas;
        $campos = $_POST['campo'];
        try {
            $pol = $Polizas->where('numero', $campos['numero'])->count();
        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        echo $pol;
        exit;
    }

    //--------------------------------------------------------------------------

    
    function ajax_get_asociados() {

        $unico = $_POST['unico'];

        $inter = InteresesAsegurados_detalles::join("int_intereses_asegurados", "int_intereses_asegurados.id", "=", "int_intereses_asegurados_detalles.id_intereses")
                        ->join("int_personas", 'int_personas.id', '=', 'int_intereses_asegurados.interesestable_id')
                        ->where('detalle_unico', $unico)->where('detalle_relacion', 'Principal')->where('int_intereses_asegurados.empresa_id', $this->empresa_id)->select('int_personas.id', 'int_personas.nombrePersona', 'int_intereses_asegurados_detalles.detalle_certificado')->get();
        $response = new stdClass();
        $response->inter = array();
        $response->inter = $inter->toArray();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    //--------------------------------------------------------------------------

    function ajax_get_tipointereses() {

        $interes = $_POST['interes'];
        $interes = str_replace("Tab", "", $interes);
        if ($interes == "articulo") {
            $tipo = 1;
            $tbl = "int_articulo";
        } else if ($interes == "carga") {
            $tipo = 2;
            $tbl = "int_carga";
        } else if ($interes == "casco_aereo") {
            $tipo = 3;
            $tbl = "int_casco_aereo";
        } else if ($interes == "casco_maritimo") {
            $tipo = 4;
            $tbl = "int_casco_maritimo";
        } else if ($interes == "persona") {
            $tipo = 5;
            $tbl = "int_personas";
        } else if ($interes == "proyecto_actividad") {
            $tipo = 6;
            $tbl = "int_proyecto_actividad";
        } else if ($interes == "ubicacion") {
            $tipo = 7;
            $tbl = "int_ubicacion";
        } else if ($interes == "vehiculo") {
            $tipo = 8;
            $tbl = "int_vehiculo";
        } else {
            $tipo = 0;
            $tbl = "int_";
        }
        $inter = InteresesAsegurados::join('' . $tbl . '', '' . $tbl . '.id', '=', 'int_intereses_asegurados.interesestable_id')->where('int_intereses_asegurados.interesestable_type', $tipo)
                        ->where('int_intereses_asegurados.estado', 'Activo')
                        ->where('int_intereses_asegurados.empresa_id', $this->empresa_id)
                        ->where('int_intereses_asegurados.deleted', 0)->get();
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
            array_push($response->inter, array("id" => $value->interesestable_id, "numero" => $v));
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    //--------------------------------------------------------------------------

    function ajax_get_intereses() {

        $interes = $_POST['interes'];
        if ($interes == "") {
            $interes = "0";
        }
        $tipointeres = $_POST['tipointeres'];
        $tipointeres = str_replace("Tab", "", $tipointeres);
        if ($tipointeres == "articulo") {
            $tipo = 1;
        } else if ($tipointeres == "carga") {
            $tipo = 2;
        } else if ($tipointeres == "casco_aereo") {
            $tipo = 3;
        } else if ($tipointeres == "casco_maritimo") {
            $tipo = 4;
        } else if ($tipointeres == "persona") {
            $tipo = 5;
            $tipointeres = "personas";
        } else if ($tipointeres == "proyecto_actividad") {
            $tipo = 6;
        } else if ($tipointeres == "ubicacion") {
            $tipo = 7;
        } else if ($tipointeres == "vehiculo") {
            $tipo = 8;
        } else {
            $tipo = "0";
        }

        $inter = InteresesAsegurados::join("int_" . $tipointeres . "", "int_" . $tipointeres . ".id", "=", "int_intereses_asegurados.interesestable_id")->where('int_intereses_asegurados.interesestable_type', $tipo)
                ->where('int_intereses_asegurados.interesestable_id', $interes)
                ->where('int_intereses_asegurados.empresa_id', $this->empresa_id)
                //->select("int_intereses_asegurados.uuid_intereses AS uuid, int_".$tipointeres.".*")
                ->first();

        $response = new stdClass();
        $response->inter = array();
        $response->inter = $inter->toArray();
        if ($tipo != 2 && $tipo != 3 && $tipo != 5) {
            //$response->inter ['uuid_'.$tipointeres] = bin2hex($response->inter ['uuid_'.$tipointeres]);
            $tipointeres = str_replace("_actividad", "", $tipointeres);
            $response->inter ['uuid_' . $tipointeres] = "";
        }
        $response->inter ['tipointeres'] = $tipo;
        $response->inter ['uuid_intereses'] = bin2hex($response->inter ['uuid_intereses']);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }
    //-----------------------------------------------
    //Funcion ObtenerPolizas respecto al ramo
    function ajax_get_polizas() {

        $clause['empresa_id'] = $this->empresa_id;
        $clause['ramo_id'] = $_POST['ramo_id'];

        $polizas = Polizas::join("seg_aseguradoras", "seg_aseguradoras.id", "=", "pol_polizas.aseguradora_id")
                ->join("cli_clientes", "cli_clientes.id", "=", "pol_polizas.cliente")
                ->where("pol_polizas.ramo_id", $clause['ramo_id'])
                ->where("pol_polizas.empresa_id", $clause['empresa_id'])
                ->select('pol_polizas.id as id', 'pol_polizas.numero as numero', 'cli_clientes.nombre as cliente', 'seg_aseguradoras.nombre as aseguradora')
                ->get()
                ->toArray();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($polizas))->_display();
        exit;
    }

    //-----------------------------------------------
    //Funcion Reclamantes 
    function ajax_get_reclamantes() {

        $tipo_interes = $_POST['tipo_interes'] ;
        $pol = $_POST['poliza'];

        $reclamante = Polizas::join("cli_clientes", "cli_clientes.id", "=", "pol_polizas.cliente")->where("pol_polizas.id", $pol)->orderBy("cli_clientes.nombre","ASC")->select("cli_clientes.nombre", "cli_clientes.id", "cli_clientes.telefono", "cli_clientes.correo")->get()->toArray();

        if ($tipo_interes=="5") {
            //Personas
            $int = PolizasPersonas::where("id_poliza", $pol)->orderBy("numero","ASC")->select("nombrePersona", "telefono_residencial", "correo")->get();
            $i=1;
            foreach ($int as $value) {
                $ar = array("nombre"=>$value->nombrePersona, "telefono"=>$value->telefono, "correo"=>$value->correo);
                array_push($reclamante, $ar);
                $i++;
            }
        }else if ($tipo_interes=="8") {
            //Vehiculo
             $int = PolizasVehiculo::where("id_poliza", $pol)->orderBy("numero","ASC")->select("operador")->get();
            $i=1;
            foreach ($int as $value) {
                $ar = array("nombre"=>$value->operador);
                array_push($reclamante, $ar);
                $i++;
            }
        }      

        //print_r($reclamante);
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($reclamante))->_display();
        exit;
    }

    //------------------------------------------------

    //Funcion Coberturas y Deducciones
    function ajax_get_coberturas() {

        $pol = $_POST['poliza'];
        $interes = $_POST['poliza_interes'];
        //Coberturas de Poliza
        $polizacob = new Flexio\Modulo\Polizas\Models\PolizasCobertura;
        $coberturas = $polizacob->where("id_poliza", $pol)->where("id_poliza_interes", $interes)->get()->toArray();
        $polizaded = new Flexio\Modulo\Polizas\Models\PolizasDeduccion;
        $deducciones = $polizaded->where("id_poliza", $pol)->where("id_poliza_interes", $interes)->get()->toArray();

        $response = new stdClass();
        $response->coberturas = $coberturas;
        $response->deducion = $deducciones;
        
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();
        exit;
    }  


    //------------------------------------------------

    //Funcion Obtener Poliza Seleccionada
    function ajax_get_poliza() {

        $clause['empresa_id'] = $this->empresa_id;
        $clause['poliza_id'] = $_POST['poliza_id'];
        $tipo_interes = $_POST['tipo_interes'];

        $poliza = Polizas::where("id", $clause['poliza_id'])->where("empresa_id", $clause['empresa_id'])->first();
        $pol = array();

        if ($tipo_interes == 1) {            
            $acreedor_hipotecario = '';
            $porcentaje_acreedor = '';
        }else if ($tipo_interes == 2) {
            $pol_art = PolizasCarga::where("id_poliza", $clause['poliza_id'])->first();            
        }else if ($tipo_interes == 3) {
            $acreedor_hipotecario = '';
            $porcentaje_acreedor = '';
        }else if ($tipo_interes == 4) {
            $pol_art = PolizasMaritimo::where("id_poliza", $clause['poliza_id'])->first();            
        }else if ($tipo_interes == 5) {
            $acreedor_hipotecario = '';
            $porcentaje_acreedor = '';
        }else if ($tipo_interes == 6) {
            $pol_art = PolizasProyecto::where("id_poliza", $clause['poliza_id'])->first();            
        }else if ($tipo_interes == 7) {
            $pol_art = PolizasUbicacion::where("id_poliza", $clause['poliza_id'])->first();            
        }else if ($tipo_interes == 8) {
            $pol_art = PolizasVehiculo::where("id_poliza", $clause['poliza_id'])->first();            
        }

        if($tipo_interes == 2 || $tipo_interes == 4 || $tipo_interes == 6 || $tipo_interes == 7 || $tipo_interes == 8){
            if (isset($pol_art)) {
                if ($pol_art->acreedor == "otro") { 
                    $acreedor_hipotecario = isset($pol_art->acreedor_opcional) ? $pol_art->acreedor_opcional : '';
                }else{ 
                    $acreedor = Proveedores::where("id", $pol_art->acreedor)->first();
                    $acreedor_hipotecario = isset($acreedor->nombre) ? $acreedor->nombre : '';  
                }
                $porcentaje_acreedor = $pol_art->porcentaje_acreedor;
            }else{
                $acreedor_hipotecario = "";
                $porcentaje_acreedor = "";
            }            
        }

        if ($acreedor_hipotecario == null) { $acreedor_hipotecario = ""; }
        if ($porcentaje_acreedor == null) { $porcentaje_acreedor = ""; }

        $pol['idpoliza'] = isset($poliza->id) ? $poliza->id : '';
        $pol['numeropoliza'] = isset($poliza->numero) ? $poliza->numero : '';
        $pol['vigencia_desde'] = isset($poliza->vigenciafk->vigencia_desde) ? $poliza->vigenciafk->vigencia_desde : '';
        $pol['vigencia_hasta'] = isset($poliza->vigenciafk->vigencia_hasta) ? $poliza->vigenciafk->vigencia_hasta : '';
        $pol['nombre_cliente'] = isset($poliza->clientefk->nombre) ? $poliza->clientefk->nombre : '';
        $pol['id_cliente'] = isset($poliza->clientefk->id) ? $poliza->clientefk->id : '';
        $pol['id_aseguradora'] = isset($poliza->aseguradorafk->id) ? $poliza->aseguradorafk->id : '' ;
        $pol['nombre_aseguradora'] = isset($poliza->aseguradorafk->nombre) ? $poliza->aseguradorafk->nombre : '' ;
        $pol['acreedor_hipotecario'] = $acreedor_hipotecario;
        $pol['porcentaje_acreedor'] = $porcentaje_acreedor;
        
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($pol))->_display();

        exit;
    }

    //------------------------------------------------
    //Verifica Duplicidad de Reclamo
    
    public function existsIdentificacion() {
        
        //$campo = Util::set_fieldset("camporeclamo");
        //$campovalida = Util::set_fieldset("campovalida");
        $campo = $_POST["camporeclamo"];
        $campovalida = $_POST["campovalida"];
        $response = new stdClass();
            
        $numero_caso = $campo['numero_caso'];
        $ramo_id = $campo['id_ramo'];
        $aseguradora_id = $campovalida['aseguradora_id'];

        if (isset($campo['uuid'])) {
            $conta = ReclamosModel::where("numero_caso", $numero_caso)
                ->where("uuid_reclamos", hex2bin($campo['uuid']))
                ->count();
                //$agente =  AgentesModel::findById($campo['identificacion']);

            if( $conta>0 ){
                $response->existe =  false;
            }else{
                $reclamos = ReclamosModel::join("pol_polizas", "pol_polizas.id", "=", "rec_reclamos.id_poliza")
                    ->where("pol_polizas.aseguradora_id", $aseguradora_id)
                    ->where("rec_reclamos.numero_caso", $numero_caso)
                    ->where("rec_reclamos.id_ramo", $ramo_id)
                    ->count();
                    //$agente =  AgentesModel::findById($campo['identificacion']);

                if(is_null($reclamos) || $reclamos==0){
                    $response->existe =  false;
                }else{
                    //$response->existe =  true;
                    $response->existe =  true;
                }
            }
        }else{
            $reclamos = ReclamosModel::join("pol_polizas", "pol_polizas.id", "=", "rec_reclamos.id_poliza")
                ->where("pol_polizas.aseguradora_id", $aseguradora_id)
                ->where("rec_reclamos.numero_caso", $numero_caso)
                ->where("rec_reclamos.id_ramo", $ramo_id)
                ->count();
                //$agente =  AgentesModel::findById($campo['identificacion']);

            if(is_null($reclamos) || $reclamos==0){
                $response->existe =  false;
            }else{
                //$response->existe =  true;
                $response->existe =  true;
            }
        }
            
            
        echo json_encode($response);
        exit;
    }

    //-------------------------------------------------

    function ajax_get_intereses_poliza() {

        $poliza = $_POST['poliza'];
        $interes = isset($_POST['interes']) ? $_POST['interes'] : '';
        
        $tipointeres = $_POST['tipo_interes'];
        if ($tipointeres == 1) {
            $inter = PolizasArticulo::where("id_poliza", $poliza);
        } else if ($tipointeres == 2) {
           $inter = PolizasCarga::where("id_poliza", $poliza);
        } else if ($tipointeres == 3) {
            $inter = PolizasAereo::where("id_poliza", $poliza);
        } else if ($tipointeres == 4) {
            $inter = PolizasMaritimo::where("id_poliza", $poliza);
        } else if ($tipointeres == 5) {
            $inter = PolizasPersonas::where("id_poliza", $poliza);
        } else if ($tipointeres == 6) {
            $inter = PolizasProyecto::where("id_poliza", $poliza);
        } else if ($tipointeres == 7) {
            $inter = PolizasUbicacion::where("id_poliza", $poliza);
        } else if ($tipointeres == 8) {
            $inter = PolizasVehiculo::where("id_poliza", $poliza);
        } 

        if ($interes != "") {
            $inter = $inter->where("id", $interes);
        }
        $inter = $inter->first();

        $response = new stdClass();
        $response->inter = array();
        $response->inter = $inter != null ? $inter->toArray() : array();

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    //-------------------------------------------------

    function ajax_get_intereses_reclamo() {

        $reclamo = $_POST['reclamo'];
        $interes = isset($_POST['interes']) ? $_POST['interes'] : '';
        
        $tipointeres = $_POST['tipo_interes'];
        if ($tipointeres == 1) {
            $inter = ReclamosArticulo::where("id_reclamo", $reclamo);
        } else if ($tipointeres == 2) {
           $inter = ReclamosCarga::where("id_reclamo", $reclamo);
        } else if ($tipointeres == 3) {
            $inter = ReclamosAereo::where("id_reclamo", $reclamo);
        } else if ($tipointeres == 4) {
            $inter = ReclamosMaritimo::where("id_reclamo", $reclamo);
        } else if ($tipointeres == 5) {
            $inter = ReclamosPersonas::where("id_reclamo", $reclamo);
        } else if ($tipointeres == 6) {
            $inter = ReclamosProyecto::where("id_reclamo", $reclamo);
        } else if ($tipointeres == 7) {
            $inter = ReclamosUbicacion::where("id_reclamo", $reclamo);
        } else if ($tipointeres == 8) {
            $inter = ReclamosVehiculo::where("id_reclamo", $reclamo);
        } 

        if ($interes != "") {
            $inter = $inter->where("id", $interes);
        }
        $inter = $inter->first();

        $rec = ReclamosModel::where("id", $reclamo)->first();

        $response = new stdClass();
        $response->inter = array();
        //$response->reclamo = array();
        $response->inter = $inter != null ? $inter->toArray() : array();
        //$response->reclamo = $rec != null ? $rec->toArray() : array();
        $response->inter['id_interes_asegurado'] = $rec->id_interes_asegurado;

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    //------------------------------------------------------------------------------------------

    //Muestra Formulario para Crear y Editar
    function ocultoformulario($id_ramo = NULL, $tipo_interes = NULL) {        
                
        $this->assets->agregar_var_js(array(
        ));
        $data['tipo_interes'] = $tipo_interes;
        $data['id_ramo'] = $id_ramo;
        $this->load->view('formulario', $data);
    }
    //----------------------------------------------------------------

    function ajax_get_contactos_ajustador (){
        $ajusta = $_POST['ajustador'];

        $inter = AjustadoresContactoModel::where("ajustador_id", $ajusta)->select("nombre", "id")->get();
        $response = new stdClass();
        $response->inter = array();
        $response->inter = $inter->toArray();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    //----------------------------------------------------------------

    function ajax_get_contacto (){
        $contacto = $_POST['contacto'];

        $inter = AjustadoresContactoModel::where("id", $contacto)->select("nombre", "id", "telefono")->get();
        $response = new stdClass();
        $response->inter = array();
        $response->inter = $inter->toArray();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    //-------------------------------------------------------------------

    
    // Formularios por cada seccion
    function formulariopoliza($data = array()) {
        $this->load->view('formulariopoliza', $data);
    }

    function formulariodatosreclamo($tipo_interes = array()) {
        $data['tipo_interes'] = $tipo_interes;
        $this->load->view('formulariodatosreclamo', $data);
    }

    function formulariodetallereclamo($data = array()) {
        $this->load->view('formulariodetallereclamo', $data);
    }

    function formulariodocumentos($id_ramo) {

        if ($id_ramo != "" || $id_ramo != null) {
            $id_ramo_sol = $id_ramo;
        } else {
            $id_ramo_sol = -1;
        }

        $cont_nivel1 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo_sol])
                        ->where('estado', "=", "Activo")
                        ->where('modulo', "=", "reclamo")
                        ->get());
        $ramopadre = Ramos::where('id', $id_ramo_sol)
                ->get();
        if (isset($ramopadre[0])) {
            $id_ramo_padre = $ramopadre[0]->padre_id;
        } else {
            $id_ramo_padre = -1;
        }
        $cont_nivel2 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre])
                        ->where('estado', "=", "Activo")
                        ->where('modulo', "=", "reclamo")
                        ->get());

        $ramopadre2 = Ramos::where('id', $id_ramo_padre)
                        ->get()->toArray();
        if (isset($ramopadre2[0])) {
            $id_ramo_padre2 = $ramopadre2[0]['padre_id'];
        } else {
            $id_ramo_padre2 = -1;
        }
        $cont_nivel3 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre2])
                        ->where('estado', "=", "Activo")
                        ->where('modulo', "=", "reclamo")
                        ->get());
        $ramopadre3 = Ramos::where('id', $id_ramo_padre2)
                        ->get()->toArray();

        if (isset($ramopadre3[0])) {
            $id_ramo_padre3 = $ramopadre3[0]['padre_id'];
        } else {
            $id_ramo_padre3 = -1;
        }
        $cont_nivel4 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre3])
                        ->where('estado', "=", "Activo")
                        ->where('modulo', "=", "reclamo")
                        ->get());

        if ($cont_nivel1 > 0) {
            $documentacion = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_sol])
                    ->where('estado', "=", "Activo")
                    ->where('modulo', "=", "reclamo")
                    ->get();
        } else if ($cont_nivel2 > 0) {
            $documentacion = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre])
                    ->where('estado', "=", "Activo")
                    ->where('modulo', "=", "reclamo")
                    ->get();
        } else if ($cont_nivel3 > 0) {
            $documentacion = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre2])
                    ->where('estado', "=", "Activo")
                    ->where('modulo', "=", "reclamo")
                    ->get();
        } else if ($cont_nivel4 > 0) {
            $documentacion = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre3])
                    ->where('estado', "=", "Activo")
                    ->where('modulo', "=", "reclamo")
                    ->get();
        } else {
            $documentacion = array();
        }

        $data["campos"]["documentacion"] = $documentacion;

        $this->load->view('formulariodocumentos', $data);
    }

    function formulariopago($data = array()) {
        $this->load->view('formulariopago', $data);
    }

    function formulariointereses($tipo_interes = array()) {
        /* $clause = array('empresa_id' => $this->empresa_id);        
          $this->assets->agregar_var_js(array(
          ));
         */

        if ($this->auth->has_permission('editar__cambiarEstado', 'intereses_asegurados/editar/(:any)') == true) {
            $cestado = 1;
        } else {
            $cestado = 0;
        }
        if ($this->auth->has_permission('acceso', 'reclamos/editar participación') == true) {
            $editarParticipacion = 1;
        } else {
            $editarParticipacion = 0;
        }


        $this->_Css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/formulario.js',
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
            ));
        }

        
        $data['tipo_interes'] = $tipo_interes;
        $this->load->view('formulariointereses', $data);
    }
    //------------------------------------------------------------

    //Funciones para las tablas de cada Interes
    public function ocultotablaarticulo($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablaarticulo.js'
        ));

        $this->load->view('tablaarticulo', $data);
    }

    public function ocultotablacarga($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablacarga.js'
        ));

        $this->load->view('tablacarga', $data);
    }

    public function ocultotablaaereo($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablaaereo.js'
        ));

        $this->load->view('tablaaereo', $data);
    }

    public function ocultotablamaritimo($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablamaritimo.js'
        ));

        $this->load->view('tablamaritimo', $data);
    }

    public function ocultotablapersonas($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablapersonas.js'
        ));

        $this->load->view('tablapersonas', $data);
    }

    public function ocultotablaproyecto($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablaproyecto.js'
        ));

        $this->load->view('tablaproyecto', $data);
    }

    public function ocultotablaubicacion($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablaubicacion.js'
        ));

        $this->load->view('tablaubicacion', $data);
    }

    public function ocultotablavehiculo($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablavehiculo.js'
        ));

        $this->load->view('tablavehiculo', $data);
    }

    //-----------------------------------------------------------------------
    
    //Funciones de Cada Interes Asegurado para mostrar Tabla
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

                
                $link_option = '<button class="seleccionarpoliza btn btn-success btn-sm" type="button" data-certificado="'.$row['nombre'].'" data-id="' . $row['id'] . '" data-poliza="' . $row['id_poliza'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Seleccionar</span></button>';
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
                        $link_option
                        );
                $i++;
            }
        }
        print_r(json_encode($response));
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

            $link_option = '<button class="seleccionarpoliza btn btn-success btn-sm" type="button" data-certificado="'.$row['no_liquidacion'].'" data-id="' . $row['id'] . '" data-poliza="' . $row['id_poliza'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Seleccionar</span></button>';

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
                    $link_option
                    );
            $i++;
        }
    }
    print_r(json_encode($response));
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

            $link_option = '<button class="seleccionarpoliza btn btn-success btn-sm" type="button" data-certificado="'.$row['serie'].'" data-id="' . $row['id'] . '" data-poliza="' . $row['id_poliza'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Seleccionar</span></button>';

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
                    $link_option
                    );
            $i++;
        }
    }
    print_r(json_encode($response));
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

            $link_option = '<button class="seleccionarpoliza btn btn-success btn-sm" type="button" data-certificado="'.$row['serie'].'" data-id="' . $row['id'] . '" data-poliza="' . $row['id_poliza'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Seleccionar</span></button>';

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
                    $link_option
                    );
            $i++;
        }
    }
    print_r(json_encode($response));
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

            $link_option = '<button class="seleccionarpoliza btn btn-success btn-sm" type="button" data-certificado="'.$row['nombre_proyecto'].'" data-id="' . $row['id'] . '" data-poliza="' . $row['id_poliza'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Seleccionar</span></button>';

            $response->rows[$i]["id"] = $row['id'];
            $response->rows[$i]["cell"] = array(
                $row["numero"],
                $row['nombre_proyecto'],
                $row['no_orden'],
                $row['ubicacion'],
                $row['fecha_inclusion'],
                    '', //$row['fecha_exclusion'],
                    "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                    $link_option
                    );
            $i++;
        }
    }
    print_r(json_encode($response));
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

            $link_option = '<button class="seleccionarpoliza btn btn-success btn-sm" type="button" data-certificado="'.$row['nombre'].'" data-id="' . $row['id'] . '" data-poliza="' . $row['id_poliza'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Seleccionar</span></button>';

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
                $link_option
                );
            $i++;
        }
    }
    print_r(json_encode($response));
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

            $link_option = '<button class="seleccionarpoliza btn btn-success btn-sm" type="button" data-certificado="'.$row['chasis'].'" data-id="' . $row['id'] . '" data-poliza="' . $row['id_poliza'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Seleccionar</span></button>';

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
                    $link_option
                    );
            $i++;
        }
    }
    print_r(json_encode($response));
    exit;
}

public function ajax_listar_personas($grid = NULL) {


    $estado = $this->input->post('estado', true);
    $id_poliza = $this->input->post('id_poliza', true);
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
        "identificacion" =>  $this->input->post('identificacion', true),
        "no_certificado" => $this->input->post('no_certificado', true),
        "detalle_relacion" => $relacion,
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

            $link_option = '<button class="seleccionarpoliza btn btn-success btn-sm" type="button" data-certificado="'.$row['identificacion'].'" data-id="' . $row['id'] . '" data-poliza="' . $row['id_poliza'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Seleccionar</span></button>';
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
                    "level" => $row["detalle_int_asociado"] != 0 ? "1" : "0", //level
                    'parent' => $row["detalle_int_asociado"] == 0 ? "NULL" : (string) $row["detalle_int_asociado"], //parent
                    'isLeaf' => $row['detalle_int_asociado'] != 0 ? true : false, //isLeaf
                    'expanded' => false, //expended
                    'loaded' => true, //loaded
                    );
        }
    }
    print_r(json_encode($response));
    exit;
}

    //--------------------------------------------------------------------------------------------------

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
        $csvdata = array();

        $clause['id'] = $id;
        $clause['empresa_id'] = $this->empresa_id;

        $reclamos = $this->reclamosRepository->listar_reclamos($clause, NULL, NULL, NULL, NULL);
        if (empty($reclamos)) {
            return false;
        }
        $i = 0;
        $now = Carbon::now();
        foreach ($reclamos AS $row) {
            $csvdata[$i]['numero_reclamo'] = utf8_decode(Util::verificar_valor($row->recnumero));
            $csvdata[$i]["numero_poliza"] = utf8_decode(Util::verificar_valor($row->polnumero));
            $csvdata[$i]["numero_caso"] = utf8_decode(Util::verificar_valor($row->numero_caso));
            $csvdata[$i]["ramo"] = utf8_decode(Util::verificar_valor($row->ramo));
            $csvdata[$i]["cliente"] = utf8_decode(Util::verificar_valor($row->clinombre));
            $csvdata[$i]["fecha_registro"] = utf8_decode(Util::verificar_valor($row->fecha));
            $csvdata[$i]["fecha_siniestro"] = utf8_decode(Util::verificar_valor($row->fecha_siniestro));
            $csvdata[$i]["usuario"] = utf8_decode(Util::verificar_valor($row->usunombre . " " . $row->usuapellido));
            $csvdata[$i]["updated_at"] = utf8_decode(Carbon::createFromFormat('Y-m-d H:i:s', $row->updated_at)->format('d/m/Y'));            
            $csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->estado));
            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $headers = [
            'No. Reclamo',
            'No. Poliza',
            'No. Caso',
            'Ramo',
            'Cliente',
            'Fecha de Registro',
            'Fecha de Siniestro',
            'Usuario',
            'Actualizacion',
            'Estado',
        ];
        $decodingHeaders = array_map("utf8_decode", $headers);
        $csv->insertOne($decodingHeaders);
        $csv->insertAll($csvdata);
        $csv->output("Reclamos-" . date('y-m-d') . ".csv");
        exit();
    }

    public function exportarDocumentos() {
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
    }


    function ajax_get_persona_colectivo() {
        $clause['id'] = $_POST['ramo_id'];
        $ramo = Ramos::where($clause)->select('nombre', 'descripcion')->first();
        $response = new stdClass();
        $response->nombre = $ramo;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($ramo))->_display();
        exit;
    }



    public function formularioModal($data = NULL) {

        $this->assets->agregar_js(array(
                //'public/assets/js/modules/documentos/formulario.controller.js'
        ));

        $this->load->view('formularioModalDocumento', $data);
    }

    function comentariosformulario() {
        $uuid = $this->uuid_reclamo;
//      $uuid = $campo['uuid'];
        $bitacora = $this->reclamosRepository->verReclamos(hex2bin(strtolower($uuid)));


        $data = array();
        $fechas = $this->bitacoraModel;
        $data["Fecha"] = $fechas;
        $data["n_reclamo"] = $bitacora->id;
        $data["historial"] = bitacoraModel::join('usuarios', 'rec_reclamos_bitacora.usuario_id', '=', 'usuarios.id')
                        ->where('comentable_id', $bitacora->id)
                        ->where('comentable_type', 'Comentario')
                        ->select('rec_reclamos_bitacora.comentario', "rec_reclamos_bitacora.comentable_type", 'rec_reclamos_bitacora.created_at', "usuarios.nombre", "usuarios.apellido")->orderBy("created_at", "desc")->get()->toArray();

        $this->load->view('comentarios', $data);
    }

    function ajax_carga_comentarios() {
        $html = '';
        try {
            $id_reclamo = $_POST["n_reclamo"];

            $uuid = $this->uuid_reclamo;
            $fechas = $this->bitacoraModel;
//      $uuid = $campo['uuid'];
            $Bitacora = $this->reclamosRepository->verReclamos(hex2bin(strtolower($uuid)));
            $historial = bitacoraModel::join('usuarios', 'rec_reclamos_bitacora.usuario_id', '=', 'usuarios.id')
                            ->where('comentable_id', $id_reclamo)
                            ->where('comentable_type', 'Comentario')
                            ->select('rec_reclamos_bitacora.comentario', "rec_reclamos_bitacora.comentable_type", 'rec_reclamos_bitacora.created_at', "usuarios.nombre", "usuarios.apellido")
                            ->orderBy("created_at", "desc")->get()->toArray();
            foreach ($historial as $item) {
                //var_dump($item["created_at"]);die;
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
                                ' . $fechas->getCuantoTiempo($item["created_at"]) . '
                                <br>
                                <small>' . $item["created_at"] . '</small>
                                <div><small>' . $item["nombre"] . " " . $item["apellido"] . " " . $fechas->getHora($item["created_at"]) . '</small></div>
                            </span>
                            </div>
                    </div>';
            }
            $data = array();

            $data["n_reclamo"] = $id_reclamo;
        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
            die("error " . $e->getMessage() . " " . __METHOD__);
        }
        die($html);
    }

    function ajax_guardar_comentario() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }
        try {
            $tipo = "Comentario";
            $id_reclamo = $this->input->post('n_reclamo');
            $comentario = $this->input->post('comentario');
            $usuario = $this->session->userdata['id_usuario'];
            $id_empresa = $this->empresa_id;


            $comment = ['comentario' => $comentario, 'usuario_id' => $usuario, 'comentable_id' => $id_reclamo, 'comentable_type' => $tipo, 'empresa_id' => $id_empresa];

            $bus = ReclamosModel::where("id", $id_reclamo)->count();
            $msg = $id_reclamo;
            if ($bus != 0) {
                $msg = $this->bitacoraModel->create($comment);

                exit;
            }
        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        die(json_encode($msg));
    }

    public function bitacora($uuid_solicitudes = null) {
        $data = array();

        $this->_Css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/bitacora.js'
        ));
        $fechas = $this->bitacoraModel;
        $data["Fecha"] = $fechas;
        $bitacora = $this->reclamosRepository->verSolicitudes(hex2bin(strtolower($uuid_solicitudes)));
        $historial = bitacoraModel::join('usuarios', 'seg_solicitudes_bitacora.usuario_id', '=', 'usuarios.id')
                        ->where('comentable_id', $bitacora->id)
                        ->select('seg_solicitudes_bitacora.comentario', "seg_solicitudes_bitacora.comentable_type", 'seg_solicitudes_bitacora.created_at', "usuarios.nombre", "usuarios.apellido")
                        ->orderBy("created_at", "desc")->get()->toArray();
        $numero = $bitacora->numero;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Historial: Solicitud N° ' . $numero,
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => true),
                1 => array("nombre" => 'Solicitudes', "url" => "reclamos/listar", "activo" => true),
                3 => array("nombre" => $numero, "url" => "reclamos/editar/" . $uuid_solicitudes, "activo" => false),
                4 => array("nombre" => '<b>Bitácora</b>', "activo" => false)
            ),
            "filtro" => false,
            "menu" => array()
        );
        $breadcrumb["menu"] = array(
            "url" => 'javascript:',
            "clase" => 'crearAccion',
            "nombre" => "Acción "
        );

        $menuOpciones = array();
        $menuOpciones["#imprimirLnk"] = "Imprimir";

        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $data["campos"] = array(
            "campos" => array(
                "solicitud_n" => $numero,
                "historial" => $historial
            ),
        );

        $this->template->agregar_titulo_header('Historial: Solicitud N° ' . $numero);
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    //--------------------------------------------------------------------------

    public function ajax_cambioestado_bitacora() {

        $inf = array();
        try {
            $campos = $_POST['campo'];
            $id_comentario = $campos['id'];
            $tipo = $campos['tipo'];
            $estado = $campos['estado'];
            $estado_anterior = $campos['estado_anterior'];
            if (isset($campos['motivo'])) {
                $motivo = $campos['motivo'];
            } else {
                $motivo = "";
            }
            if (isset($campos['reclamo'])) {
                $reclamo = $campos['reclamo'];
            } else {
                $reclamo = "";
            }
            $usuario = $this->usuario_id;

            $comentario = "Estado Actual: " . $estado . "<br>Estado Anterior: " . $estado_anterior . "<br>Motivo: " . $motivo . "<br>";
            $fieldset["comentario"] = $comentario;
            $fieldset["comentable_type"] = $tipo;
            $fieldset["comentable_id"] = $id_comentario;
            $fieldset["usuario_id"] = $usuario;
            $fieldset["empresa_id"] = $this->empresa_id;

            $interesaseg = $this->bitacoraModel->create($fieldset);

        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
            $inf["msg"] = "Err";
        }
        die(json_encode($inf));
        exit;
    }

    public function tablatabsolicitudes($data = array()) {
        //If ajax request

        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablatab.js',
            'public/assets/js/modules/reclamos/routes.js'
        )); //'public/assets/js/modules/aseguradoras/tabla_ramos.js'
        //$this->aseguradora_id = $data['uuid_aseguradora'];

        $this->load->view('tabla', $data);
    }

    function tabladetalles($data = array()) {
        /* $this->assets->agregar_var_js(array(
          "modulo_id" => 57,
          )); */

        $this->load->view('tabladetalles', $data);
    }

    public function formularioModalDocumento($data = NULL) {

        $this->assets->agregar_js(array(
                //'public/assets/js/modules/documentos/formulario.controller.js'
        ));

        $this->load->view('formularioModalDocumento', $data);
    }

    function ajax_guardar_documentos() {
        if (empty($_POST)) {
            return false;
        }

        if ($this->input->post('id_reclamo', true)) {

            $id_reclamo = $this->input->post('id_reclamo', true);
            $modeloInstancia = ReclamosModel::find($id_reclamo);
        } elseif ($this->input->post('id', true)) {

            $intereses_id = $this->input->post('id', true);
            $intereses_type = $this->input->post('intereses_type', true);

            if ($intereses_type == "articulo") {
                $modeloInstancia = $this->ArticuloModel->find($intereses_id);
            } elseif ($intereses_type == "carga") {
                $modeloInstancia = $this->CargaModel->find($intereses_id);
            } elseif ($intereses_type == "casco_aereo") {
                $modeloInstancia = $this->AereoModel->find($intereses_id);
            } elseif ($intereses_type == "casco_maritimo") {
                $modeloInstancia = $this->MaritimoModel->find($intereses_id);
            } elseif ($intereses_type == "persona") {
                $modeloInstancia = $this->PersonasModel->find($intereses_id);
            } elseif ($intereses_type == "proyecto") {
                $modeloInstancia = $this->ProyectoModel->find($intereses_id);
            } elseif ($intereses_type == "ubicacion") {
                $modeloInstancia = $this->UbicacionModel->find($intereses_id);
            } elseif ($intereses_type == "vehiculo") {
                $modeloInstancia = $this->VehiculoModel->find($intereses_id);
            }
        }

        $this->documentos->subir($modeloInstancia);
        $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado los documentos satisfactoriamente');
        $this->session->set_flashdata('mensaje', $mensaje);

        redirect(base_url('reclamos/editar/' . bin2hex($modeloInstancia->uuid_reclamos) . ''));
    }

    public function formularioModalEditar($data = NULL) {

        $this->assets->agregar_var_js(array(
            "numero" => "",
            'data' => "",
        ));

        $this->load->view('formularioModalDocumentoEditar');
    }

    public function imprimirReclamos($id_reclamo = null) {

        if ($id_reclamo == null) {
            return false;
        }

        $accidente = array();

        $reclamos = ReclamosModel::where(['id' => $id_reclamo])->first();
        $tipo_interes = $reclamos->tipo_interes;

        $poliza = Polizas::where("id", $reclamos->id_poliza)->where("empresa_id", $this->empresa_id)->first();
        $pol = array();

        if ($tipo_interes == 1) {  
            $interes = ReclamosArticulo::where("id_reclamo", $id_reclamo)->get();          
            $acreedor_hipotecario = '';
            $porcentaje_acreedor = '';
        }else if ($tipo_interes == 2) {
            $interes = ReclamosCarga::where("id_reclamo", $id_reclamo)->get();
            $pol_art = PolizasCarga::where("id_poliza", $reclamos->id_poliza)->first();            
        }else if ($tipo_interes == 3) {
            $interes = ReclamosAereo::where("id_reclamo", $id_reclamo)->get();
            $acreedor_hipotecario = '';
            $porcentaje_acreedor = '';
        }else if ($tipo_interes == 4) {
            $interes = ReclamosMaritimo::where("id_reclamo", $id_reclamo)->get();
            $pol_art = PolizasMaritimo::where("id_poliza", $reclamos->id_poliza)->first();            
        }else if ($tipo_interes == 5) {
            $interes = ReclamosPersonas::where("id_reclamo", $id_reclamo)->get();
            $acreedor_hipotecario = '';
            $porcentaje_acreedor = '';
        }else if ($tipo_interes == 6) {
            $interes = ReclamosProyecto::where("id_reclamo", $id_reclamo)->get();
            $pol_art = PolizasProyecto::where("id_poliza", $reclamos->id_poliza)->first();            
        }else if ($tipo_interes == 7) {
            $interes = ReclamosUbicacion::where("id_reclamo", $id_reclamo)->get();
            $pol_art = PolizasUbicacion::where("id_poliza", $reclamos->id_poliza)->first();            
        }else if ($tipo_interes == 8) {
            $interes = ReclamosVehiculo::where("id_reclamo", $id_reclamo)->get();
            $pol_art = PolizasVehiculo::where("id_poliza", $reclamos->id_poliza)->first();
            $causa = SegCatalogo::where("tipo", "causa_reclamo")->where("id", $reclamos->causa)->select("etiqueta")->first();
            $accidente = ReclamosAccidentes::where("id_reclamo", $id_reclamo)->get();
            $idsa = array();
            foreach ($accidente as $ac) {  array_push($idsa, $ac['id_tipo_accidente']);  }
            $nombreaccidentes = SegCatalogo::where("tipo", "accidente_reclamo")->whereIn("id", $idsa)->get();         
        }

        if($tipo_interes == 2 || $tipo_interes == 4 || $tipo_interes == 6 || $tipo_interes == 7 || $tipo_interes == 8){
            if (isset($pol_art)) {
                if ($pol_art->acreedor == "otro") { 
                    $acreedor_hipotecario = isset($pol_art->acreedor_opcional) ? $pol_art->acreedor_opcional : '';
                }else{ 
                    $acreedor = Proveedores::where("id", $pol_art->acreedor)->first();
                    $acreedor_hipotecario = isset($acreedor->nombre) ? $acreedor->nombre : '';  
                }
                $porcentaje_acreedor = $pol_art->porcentaje_acreedor;
            }else{
                $acreedor_hipotecario = "";
                $porcentaje_acreedor = "";
            }            
        }

        if ($acreedor_hipotecario == null) { $acreedor_hipotecario = ""; }
        if ($porcentaje_acreedor == null) { $porcentaje_acreedor = ""; }

        $pol['idpoliza'] = isset($poliza->id) ? $poliza->id : '';
        $pol['numeropoliza'] = isset($poliza->numero) ? $poliza->numero : '';
        $pol['vigencia_desde'] = isset($poliza->inicio_vigencia) ? $poliza->inicio_vigencia : '';
        $pol['vigencia_hasta'] = isset($poliza->fin_vigencia) ? $poliza->fin_vigencia : '';
        $pol['nombre_cliente'] = isset($poliza->clientefk->nombre) ? $poliza->clientefk->nombre : '';
        $pol['id_cliente'] = isset($poliza->clientefk->id) ? $poliza->clientefk->id : '';
        $pol['id_aseguradora'] = isset($poliza->aseguradorafk->id) ? $poliza->aseguradorafk->id : '' ;
        $pol['nombre_aseguradora'] = isset($poliza->aseguradorafk->nombre) ? $poliza->aseguradorafk->nombre : '' ;
        $pol['acreedor_hipotecario'] = $acreedor_hipotecario;
        $pol['porcentaje_acreedor'] = $porcentaje_acreedor;
        $pol['tipo_interes'] = $tipo_interes;

        $reclamos->causa = isset($causa->etiqueta) ? $causa->etiqueta : "";
 
        $coberturas = ReclamosCoberturas::where("id_reclamo", $reclamos->id)->get(); 
        $deducciones = ReclamosDeduccion::where("id_reclamo", $reclamos->id)->get(); 

        $formulario = "documentoReclamos";
        $nombre = $reclamos->numero;

        $data = ['datos' => $reclamos, 'intereses' => $interes, 'poliza' => $pol , 'coberturas' => $coberturas, 'deducciones' => $deducciones, 'accidentes' => $nombreaccidentes];
        $dompdf = new Dompdf();
        $html = $this->load->view( $formulario, $data, true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($nombre, array("Attachment" => false));
        exit(0);
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
            'public/assets/js/modules/reclamos/formulario_comentario.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/modules/reclamos/routes.js',
            'public/assets/js/default/subir_documento_modulo.js',
                //'public/assets/js/default/grid.js',
        ));
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
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css'
        ));
    }

    public function obtener_politicas() {
        echo json_encode($this->politicas);
        exit;
    }

    public function obtener_politicas_general() {
        echo json_encode($this->politicas_general);
        exit;
    }

    function documentos_campos() {

        return array(
            array(
                "type" => "hidden",
                "name" => "cliente_id",
                "id" => "cliente_id",
                "class" => "form-control",
                "readonly" => "readonly",
        ));
    }

    public function agregar_detalle_salud() {

        $campos = $_POST['campos'];

        try {
            $unico = array();
            $id = $campos['id'] ;
            $unico['detalle_unico'] = $campos['detalle_unico'];
            $unico['tipo_salud'] = $campos['tipo_salud'];
            $unico['hospital'] = $campos['hospital'];
            $unico['especialidad_salud'] = $campos['especialidad_salud'];
            $unico['doctor'] = $campos['doctor'];
            $unico['detalle_salud'] = $campos['detalle_salud'];
            $unico['fecha_salud'] = $campos['fecha_salud'];
            $unico['monto_salud'] = $campos['monto_salud'];      
            $unico['id_int_pol'] = $campos['id_int_pol'];       
            if ($id == "0") {
                $detalle = ReclamosDetalleSalud::create($unico);
            }else{
                $detalle = ReclamosDetalleSalud::where("id", $id)->update($unico);
            }
            
        } catch (\Exception $e) {
            $detalle = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($detalle))->_display();
        exit;
    }

    public function ver_detalle_salud() {

        $campos = $_POST['campo'];

        try {
            $detalle = ReclamosDetalleSalud::where("id", $campos['id'])->first();
        } catch (\Exception $e) {
            $detalle = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($detalle))->_display();
        exit;
    }


    public function ocultotablasalud($data = array()) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/reclamos/tablasalud.js'
        ));

        $this->load->view('tablasalud');
    }

    public function ajax_listar_salud($grid = NULL) {


        $id_interes = $this->input->post('id_interes', true);
        $deducible = $this->input->post('deducible', true);


        $clause = array(
            "detalle_unico" => $this->input->post('detalle_unico', true),
            "id_interes" => $this->input->post('id_interes', true),
            "id_poliza" => $this->input->post('id_poliza', true),
            "vista" => $this->input->post('vista', true),

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
        $count = ReclamosDetalleSalud::listar_salud_provicional($clause, NULL, NULL, NULL, NULL, $id_interes)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = ReclamosDetalleSalud::listar_salud_provicional($clause, $sidx, $sord, $limit, $start, $id_interes);

            //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;
        $monto = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {

                $link_option = '<button class="vermodalSalud btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $opciones = '<button class="btn btn-block btn-outline btn-success verDetalleSalud" data-id="'.$row['id'].'">Ver Detalle</button>';

                $monto = $monto + $row['monto_salud'];
                $deducible = number_format($deducible, 2) - number_format($row['monto_salud'], 2);

                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    $row["detalle_certificado"],
                    $row["nombrePersona"] != "" ? $row["nombrePersona"] : $row["nombrePersonaP"] ,
                    $row["numero"],
                    $row["etiqueta"],
                    $row['hospital'],
                    $row['especialidad_salud'],
                    $row['doctor'],
                    $row['detalle_salud'],
                    $row['fecha_salud'],
                    '$ '.number_format($row['monto_salud'],2),
                    '$ '.number_format($deducible,2),
                    $link_option,
                    $opciones                   
                );
                $i++;
            }
            $response->rows[$i]["id"] = 0;
            $response->rows[$i]["cell"] = array(
                '<b>Total<b>',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '<b>$ '.number_format($monto, 2)."<b>",
                '',
                '',
                ''
            );
        }
        print_r(json_encode($response));
        exit;
    }

    public function actualizarclientes(){
        $cli = clienteModel::where("empresa_id", 25)->where("tipo_identificacion", "cedula")->get();

        $x = "";
        foreach ($cli as $value) {
            $iden = $value->identificacion;
            $v = explode("-", $iden);
            if (count($v)==4) {
                $d=$v[0];
                if ($v[0] == "01") {
                    $d="1";
                }else if ($v[0] == "02") {
                    $d="2";
                }else if ($v[0] == "03") {
                    $d="3";
                }else if ($v[0] == "04") {
                    $d="4";
                }else if ($v[0] == "05") {
                    $d="5";
                }else if ($v[0] == "06") {
                    $d="6";
                }else if ($v[0] == "07") {
                    $d="7";
                }else if ($v[0] == "08") {
                    $d="8";
                }else if ($v[0] == "09") {
                    $d="9";
                }
                $x = $d.'-'.$v[1].'-'.$v[2].'-'.$v[3];

                $arr = array();
                $arr['identificacion'] = $x;
                echo $x."<br>";
                $ccc = clienteModel::where("empresa_id", 25)->where("id", $value->id)->update($arr);
            }
            
        }
    }

}
