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
use Flexio\Modulo\Cliente\Repository\ClienteRepository as clienteRepository;
use Flexio\Modulo\Solicitudes\Repository\SolicitudesRepository as solicitudesRepository;
use Flexio\Modulo\Cliente\Models\Cliente as clienteModel;
use Flexio\Modulo\Contabilidad\Models\Impuestos as impuestosModel;
use Flexio\Modulo\Solicitudes\Models\Solicitudes as solicitudesModel;
use Flexio\Modulo\Solicitudes\Models\SegSolicitudesAgentePrin as SegSolicitudesAgentePrin;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_catModel;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_detalles as InteresesAsegurados_detalles;
use Flexio\Modulo\Solicitudes\Models\SolicitudesVigencia as solicitudesVigenciaModel;
use Flexio\Modulo\Solicitudes\Models\SolicitudesPrima as solicitudesPrimaModel;
use Flexio\Modulo\Solicitudes\Models\SolicitudesIntereses as solicitudesIntereses;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados as InteresesAsegurados;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable as centroModel;
use Flexio\Modulo\CentroFacturable\Repository\CentroFacturableRepository as centroRepository;
use Flexio\Modulo\Ramos\Repository\RamoRepository as RamoRepository;
use Flexio\Modulo\Ramos\Models\Ramos as Ramos;
use Flexio\Modulo\Polizas\Models\Polizas as Polizas;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Models\CatalogoTipoPoliza;
use Flexio\Modulo\Usuarios\Models\Usuarios;
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
use Flexio\Modulo\Solicitudes\Models\SolicitudesAcreedores;
use Flexio\Modulo\Solicitudes\Models\SolicitudesCoberturas;
use Flexio\Modulo\Solicitudes\Models\SolicitudesDeduccion;
use Flexio\Modulo\Solicitudes\Models\SolicitudesParticipacion as Participacion;
use Flexio\Modulo\Solicitudes\Models\SolicitudesDocumentacion as solicitudesDocumentosModel;
use Flexio\Modulo\Documentos\Repository\DocumentosRepository as DocumentosRepository;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository as PoliticasRepository;
use Dompdf\Dompdf;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;
use Flexio\Modulo\Ramos\Models\RamosUsuarios;
use Flexio\Modulo\Solicitudes\Models\SolicitudesBitacora as bitacoraModel;
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
use Flexio\Modulo\Catalogos\Models\RamosDocumentos as RamosDocumentos;
use Flexio\Modulo\Documentos\Models\Documentos as Documentos;
use Flexio\Modulo\Usuarios\Models\CentrosUsuario;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAseguradosCoberturasDetalle as IndCoverage;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAseguradosDeducibleDetalle as IndDeductible;

class Solicitudes extends CRM_Controller {

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
    protected $solicitudesRepository;
    protected $centroRepository;
    protected $ramoRepository;
    protected $coberturaModel;
    protected $AseguradorasRepository;
    protected $deduciblesModel;
    protected $solicitudesCoberturas;
    protected $solicitudesDeduccion;
    private $Participacion;
    private $solicitudesDocumentosModel;
    protected $politicas;
    protected $politicas_general;
    protected $PoliticasRepository;
    private $bitacoraModel;
    private $uuid_soli;
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
    private $Documentos;
    private $SegSolicitudesAgentePrin;

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
        $this->solicitudesRepository = new solicitudesRepository();
        $this->centroRepository = new centroRepository();
        $this->clienteModel = new clienteModel();
        $this->planesModel = new PlanesRepository();
        $this->impuestosModel = new impuestosModel();
        $this->solicitudesModel = new solicitudesModel();
        $this->InteresesAsegurados_catModel = new InteresesAsegurados_catModel();
        $this->solicitudesVigenciaModel = new solicitudesVigenciaModel();
        $this->solicitudesPrimaModel = new solicitudesPrimaModel();
        $this->centroModel = new centroModel();
        $this->ramoRepository = new RamoRepository();
        $this->SegCatalogoRepository = new SegCatalogoRepository();
        $this->SegInteresesAseguradosRepository = new SegInteresesAseguradosRepository();
        $this->CatalogoTPoliza = new CatalogoTPoliza();
        $this->AseguradorasRepository = new AseguradorasRepository();
        $this->deduciblesModel = new Deducibles();
        $this->coberturaModel = new coberturaModel();
        $this->solicitudesDeduccion = new SolicitudesDeduccion();
        $this->solicitudesCoberturas = new SolicitudesCoberturas();
        $this->Participacion = new Participacion();
        $this->solicitudesDocumentosModel = new solicitudesDocumentosModel();
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
        $this->Documentos = new Documentos();
        $this->SegSolicitudesAgentePrin=new SegSolicitudesAgentePrin();

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

    public function listar() {

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
            ));

        $data = array();

        $this->_Css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/solicitudes/listar.js'
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
            "titulo" => '<i class="fa fa-archive"></i> Solicitudes',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Solicitudes</b>', "activo" => true)
                ),
            "filtro" => false,
            "menu" => array()
            );

        if ($this->auth->has_permission('acceso', 'solicitudes/crear')) {
            $breadcrumb["menu"] = array(
                "url" => 'javascript:',
                "clase" => 'modalOpcionesCrear',
                "nombre" => "Crear"
                );
            $menuOpciones["#cambiarEstadoSolicitudesLnk"] = "Cambiar estado";
            $menuOpciones["#imprimirCartaSolicitudesLnk"] = "Imprimir carta";
            $menuOpciones["#exportarSolicitudesLnk"] = "Exportar";
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


        $this->template->agregar_titulo_header('Listado de Solicitudes');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function editar($uuid_solicitudes = null) {

        $this->uuid_soli = $uuid_solicitudes;

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
            ));

        if (!$this->auth->has_permission('acceso', 'solicitudes/ver/(:any)') && !$this->auth->has_permission('acceso', 'solicitudes/editar/(:any)')) {
            // No, tiene permiso, redireccionarlo.
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ingresar a editar', 'titulo' => 'Solicitudes ');

            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url('solicitudes/listar'));
        } else {
            $acceso = 1;
        }

        if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
            $ceditar = 1;
        } else {
            $ceditar = 0;
        }


        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/solicitudes/formulario.js',
            'public/assets/js/modules/solicitudes/crear.vue.js',
            'public/assets/js/modules/solicitudes/component.vue.js',
            'public/assets/js/modules/solicitudes/plugins.js'
            ));

        $solicitudes = $this->solicitudesRepository->verSolicitudes(hex2bin(strtolower($uuid_solicitudes)));
        $clientes = $this->clienteModel->where(['id' => $solicitudes->cliente_id])->first();

        $aseguradoras = $this->solicitudesRepository->verAseguradas($solicitudes->aseguradora_id);
        //var_dump($aseguradoras->uuid_aseguradora);
        $vigencias = $this->solicitudesRepository->verVigencia($solicitudes->id);
        if (count($aseguradoras) == 0) {
            $aseguradoras = 'undefined';
        }
        if (count($vigencias) == 0) {
            $vigencias = 'undefined';
        }
        $prima = $this->solicitudesRepository->verPrima($solicitudes->id);
        if (count($prima) == 0) {
            $prima = 'undefined';
        }
        $acreedores = $this->solicitudesRepository->verAcreedores($solicitudes->id);
        if (count($acreedores) == 0) {
            $acreedores = 'undefined';
        }
        $participacion = $this->solicitudesRepository->verParticipacion($solicitudes->id);
        if (count($participacion) == 0) {
            $participacion = 'undefined';
        }
        $planes = Planes::where('id', $solicitudes->plan_id)->get(array('id', 'nombre', 'id_aseguradora', 'id_ramo', 'desc_comision', 'id_impuesto', 'prima_neta')); //

        $catalogo_clientes = $this->SegInteresesAseguradosRepository->listar_catalogo('Identificacion', 'orden');
        $pagador = $this->SegInteresesAseguradosRepository->listar_catalogo('pagador_seguros', 'orden');
        $cantidad_pagos = $this->SegInteresesAseguradosRepository->listar_catalogo('cantidad_pagos', 'orden');
        $frecuencia_pagos = $this->SegInteresesAseguradosRepository->listar_catalogo('frecuencia_pagos', 'orden');
        $metodo_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('metodo_pago', 'orden');
        $sitio_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('sitio_pago', 'orden');
//        $agentes = Agentes::orderBy("nombre")->get(array('id', 'nombre'));
        $agentes = Agentes::join('agt_agentes_ramos', 'agt_agentes.id', '=', 'agt_agentes_ramos.id_agente')
        ->where('id_ramo', '=', $solicitudes->ramo_id)
        ->orderBy("nombre")->get(array('agt_agentes.id', 'nombre'));
        $estado = $this->SegCatalogoRepository->listar_catalogo('estado_s', 'orden');

        $solicitudes_titulo = Ramos::find($solicitudes->ramo_id);
        $tipo_poliza = $solicitudes_titulo['id_tipo_poliza'];
        $tipo_interes_asegurado = $solicitudes_titulo['id_tipo_int_asegurado'];

        $tipo_solicitud=Ramos::find($solicitudes->ramo_id)->first();
        $indcolec = $tipo_solicitud->id_tipo_poliza;

        $usersList = Usuarios::join("seg_ramos_usuarios", "seg_ramos_usuarios.id_usuario", "=", "usuarios.id")->where(array("usuarios.estado" => 'Activo', "seg_ramos_usuarios.id_ramo" => $solicitudes->ramo_id))->get();
        if ($usersList->count() == 0) {
            $usersList = 0;
        }

        if ($this->auth->has_permission('acceso', 'solicitudes/editar asignación')) {
            $editar_asignado = 1;
        } else {
            $editar_asignado = 0;
        }

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

        $agenteprincipaltotal=Agentes::where('principal',1)->
        where('id_empresa',$this->empresa_id)->first()->count();

        if($agenteprincipaltotal>0)
        {
           $agenteprincipal=Agentes::where('id_empresa',$this->empresa_id)->where('principal',1)->first();
           $agenteprincipalnombre=$agenteprincipal->nombre;

           $totalparticipacion=$this->Participacion->where('id_solicitud',$solicitudes->id)->sum('porcentaje_participacion');
           $agtPrincipalporcentaje=number_format((100-$totalparticipacion),2);
       }
       else
       {
           $agenteprincipalnombre="";
           $agtPrincipalporcentaje=0;
       }

    $solicitudes_titulo = Ramos::find($solicitudes->ramo_id);
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


     $data = array();
     $this->assets->agregar_var_js(array(
        "vista" => 'editar',
        "agtPrincipal"=>$agenteprincipalnombre,
        "agtPrincipalporcentaje"=>$agtPrincipalporcentaje,
        "acceso" => $acceso,
        "solicitud_id" => $solicitudes->id,
        "ramo_id" => $solicitudes->ramo_id,
        "catalogo_clientes" => $catalogo_clientes,
        "pagador" => $pagador,
        "cantidad_pagos" => $cantidad_pagos,
        "frecuencia_pagos" => $frecuencia_pagos,
        "metodo_pago" => $metodo_pago,
        "sitio_pago" => $sitio_pago,
        "agentes" => $agentes,
        "ramo" => 0,
        "id_tipo_poliza" => $tipo_poliza,
        "codigo_ramo" => 0,
        "nombre_padre" => 0,
        "estado_solicitud" => $estado,
        "ramoscadena" => 0,
        "permiso_editar" => $ceditar,
        "editar" => $ceditar,
        "cliente" => $clientes,
        "asegurada" => $aseguradoras,
        "plan" => $planes,
        "vigencia" => $vigencias,
        "prima" => $prima,
        "estado" => $solicitudes->estado,
        "participacion" => $participacion,
        "acreedores" => $acreedores,
        "observaciones" => $solicitudes->observaciones,
        "desde" => "solicitudes",
        "indcolec"=>$indcolec,
        "id_tipo_int_asegurado" => $tipo_interes_asegurado,
        "uuid_solicitudes" => bin2hex($solicitudes->uuid_solicitudes),
        "comision" => $solicitudes->comision,
        "selInteres" => '',
        "grupogbd" => $solicitudes->grupo == "" ? '' : $solicitudes->grupo,
        "direcciongbd" => $solicitudes->direccion == "" ? '' : $solicitudes->direccion,
            //***************
        "usuario_id" => $solicitudes->usuario_id,
        "usersList" => $usersList,
        "editar_asignado" => $editar_asignado,
        "numero_soliciud" => $solicitudes->numero,
        "centros_contables" => $centroContable,
        "id_centro_contable" => $solicitudes->centro_contable,
        "validavida" => $validavida,
        "contacre" => count($acreedores)
        ));

     $titulo = $solicitudes->numero;

     $breadcrumb = array(
        "titulo" => '<i class="fa fa-archive"></i> Solicitud: N°. ' . $titulo,
        "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
            1 => array("nombre" => '<a href="' . base_url() . 'solicitudes/listar">Solicitudes</a>', "activo" => false),
            2 => array("nombre" => '<b>' . $titulo . '</b>', "activo" => true),
            ),
        "filtro" => false,
        "menu" => array(
            'url' => 'javascipt:',
            'nombre' => "Acción",
            "opciones" => array(
                "solicitudes/bitacora/" . strtoupper(bin2hex($uuid_solicitudes)) => "Bitacora",
                )
            ),
        "historial" => true,
        );
     if ($this->auth->has_permission('acceso', 'solicitudes/crear')) {
        $breadcrumb["menu"] = array(
            "url" => 'javascript:',
            "clase" => 'modalOpcionesCrear',
            "nombre" => "Acción"
            );
        $menuOpciones["#imprimirSolicitudesLnk"] = "Imprimir";
        $menuOpciones["#exportarSolicitudesLnk"] = "Exportar";
        $menuOpciones["#subirDocumento"] = "Subir Documento";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;
    }

    $data['mensaje'] = $mensaje;
    $data['id_ramo'] = $solicitudes->ramo_id;
    $this->template->agregar_titulo_header('Solicitudes: Editar');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
    $this->assets->agregar_js(array(
        'public/assets/js/modules/solicitudes/tabla.js'
        ));

    $this->load->view('tabla');
}

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
    if ($modulo == "Clientes") {

        $cliente = $this->clienteRepository->findByUuid($uuid);
        $idCliente = $cliente->id;
        if ($idCliente) {
            $clause['cliente_id'] = $idCliente;
        }
    } else if ($modulo == "Aseguradoras") {

        //$ase = $this->AseguradorasRepository->verAseguradora(hex2bin($uuid));
        $ase = $this->AseguradorasRepository->verAseguradora(hex2bin($uuid));
        $id_aseguradora = $ase->id;
        if ($id_aseguradora) {
            $clause['aseguradora_id'] = $id_aseguradora;
        }
    } else if ($modulo == "Intereses Asegurados") {
        $intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($uuid)));
        $solicitudes_detalle = InteresesAsegurados_detalles::where('id_intereses', $intereses_asegurados->id)
        ->get(array('id_solicitudes'))->toArray();
            //var_dump($solicitudes_detalle);
        $clause['interes_asegurado'] = $solicitudes_detalle;
    } else if ($modulo == "Agentes") {
        $Angentes = Agentes::findByUuid($uuid);
        $clause['agentes_id'] = $Angentes->id;
    }
        //**************************************************
        // clause modulo clientes detalle tab solicitudes
        //**************************************************
    $numero = $this->input->post('numero', true);
    $cliente = $this->input->post('cliente', true);
    $aseguradora = $this->input->post('aseguradora', true);
    $ramo = $this->input->post('ramo', true);
    $ramo_id = $this->input->post('ramo_id', true);
    $tipo = $this->input->post('tipo', true);
    $inicio_creacion = $this->input->post('inicio_creacion', true);
    $fin_creacion = $this->input->post('fin_creacion', true);
    $usuario = $this->input->post('usuario', true);
    $estado = $this->input->post('estado', true);
    $cliente_id = $this->input->post('cliente_id', true);
    $aseguradora_id = $this->input->post('aseguradora_id', true);
    $tipo_poliza = $this->input->post('id_tipo_poliza', true);

    if (!empty($numero)) {
        $clause["numero"] = array('LIKE', "%$numero%");
    }
    if (!empty($cliente)) {
        $clause["cliente_id"] = $cliente;
    }
    if (!empty($cliente_id)) {
        $clause["cliente_id"] = $cliente_id;
    }
    if (!empty($aseguradora)) {
        $clause["aseguradora_id"] = $aseguradora;
    }
    if (!empty($aseguradora_id)) {
        $clause["aseguradora_id"] = $aseguradora_id;
    }
    if (!empty($ramo)) {
        $clause["ramo"] = $ramo;
    }
    if (!empty($ramo_id)) {
        $clause["ramo_id"] = $ramo_id;
    }
    if (!empty($tipo_poliza)) {
        $clause["id_tipo_poliza"] = $tipo_poliza;
    }
    if (!empty($tipo)) {
        $clause["id_tipo_poliza"] = $tipo;
    }
    if (!empty($inicio_creacion)) {
        $fecha_inicio = date("Y-m-d", strtotime($inicio_creacion));
        $clause["inicio_creacion"] = $fecha_inicio;
    }
    if (!empty($fin_creacion)) {
        $fecha_fin = date("Y-m-d", strtotime($fin_creacion));
        $clause["fin_creacion"] = $fecha_fin;
    }
    if (!empty($usuario)) {
        $clause["usuario_id"] = $usuario;
    }
    if (!empty($estado)) {
        $clause["estado"] = $estado;
    }

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    $count = $this->solicitudesRepository->listar_solicitudes($clause, NULL, NULL, NULL, NULL)->count();

    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    $rows = $this->solicitudesRepository->listar_solicitudes($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
    $response = new stdClass();
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $response->result = array();
    $i = 0;

    if (!empty($rows)) {
        foreach ($rows AS $i => $row) {
            $uuid_solicitudes = bin2hex($row->uuid_solicitudes);
            $uuid_cliente = $row->cliente->uuid_cliente;
            $uuid_aseguradora = bin2hex($row->aseguradora->uuid_aseguradora);
            $now = Carbon::now();
            $url = base_url("solicitudes/editar/$uuid_solicitudes");
            $urlbitacora = base_url("solicitudes/bitacora/$uuid_solicitudes");

                //$hidden_options = ""; 
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                //$hidden_options .= '<a href="'. base_url('colaboradores/ver/'. $uuid_colaborador) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';

            $hidden_options = '<a href="' . $url . '?reg=age&val='.strtoupper($uuid).'" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarSolicitud" >Ver Solicitud</a>';
                //$hidden_options .= '<button data-id="' . $row['id'] . '" id="cambio_estado_solicitud" class="btn btn-block btn-outline btn-success " data-type="" data-estado="' . $row->estado . '" >Cambio de Estado</button>';
            $hidden_options .= $row->estado == "Anulada" ? '' : ($row->estado == "Aprobada" ? '' : ($row->estado == "Rechazada" ? '' : '<a href="javascript:" data-id="' . $row['id'] . '" data-solicitud="' . $row->numero . '" data-cliente="' . $row->cliente->nombre . '" class="btn btn-block btn-outline btn-success anular_solicitud" data-type="' . $row['id'] . '" >Anular</a>' ));
            $hidden_options .= '<a href="' . $urlbitacora . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success bitacora_solicitud" data-type="' . $row['id'] . '" >Bitácora</a>';
            $hidden_options .= '<a href="" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success subir_archivos_solicitudes" data-type="' . $row['id'] . '" >Subir Archivos</a>';

            $estado_color = $row->estado == "En Trámite" ? 'background-color: #F8AD46' : ($row->estado == "Aprobada" ? 'background-color: #5cb85c' : ($row->estado == "Rechazada" ? 'background-color: #fc0d1b' : ($row->estado == "Anulada" ? 'background-color: #000000' : 'background-color: #5bc0de')));

            $politicas_general = $this->politicas_general;
            $politicas = $this->politicas;
            $validar_politicas = $this->politicasgenerales;
            $validar_politicas2 = $this->politicasgenerales2;
            if ($politicas_general > 0) {
//                    if (in_array(21, $politicas) || in_array(22, $politicas)) {

                if (in_array(21, $politicas) && $validar_politicas == 21) {
                    $modalstate = $row->estado == "En Trámite" ?
                    '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: black;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>' : ($row->estado == "Aprobada" ?
                        '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000" class="btn btn-block btn-outline anular_solicitud">Anulada</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>' : ($row->estado == "Rechazada" ?
                            '<button data-id="alert" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>' :
                            ($row->estado == "Anulada" ?
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' :
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En Trámite" style="color:white; background-color: #F8AD46" class="btn btn-block btn-outline massive">En Trámite</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000" class="btn btn-block btn-outline anular_solicitud">Anulada</a>'
                                )));
                } else if (in_array(22, $politicas) && $validar_politicas == 22) {
                    $modalstate = $row->estado == "En Trámite" ?
                    '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>'
                    . '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: black;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>' : ($row->estado == "Aprobada" ?
                        '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>
                        <a href="javascript:" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000" class="btn btn-block btn-outline anular_solicitud">Anulada</a>
                        <a href="javascript:" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>' : ($row->estado == "Rechazada" ?
                            '<button data-id="alert" data-estado-anterior="' . $row->estado . '" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>' : ($row->estado == "Anulada" ?
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' :
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En Trámite" style="color:white; background-color: #F8AD46" class="btn btn-block btn-outline massive">En Trámite</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000" class="btn btn-block btn-outline anular_solicitud">Anulada</a>'
                                )));
                } else if (in_array(21, $politicas) && in_array(22, $politicas) && $validar_politicas == 21 && $validar_politicas == 22) {
                    $modalstate = $row->estado == "En Trámite" ?
                    '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: black;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>' : ($row->estado == "Aprobada" ?
                        '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000" class="btn btn-block btn-outline anular_solicitud">Anulada</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>' : ($row->estado == "Rechazada" ?
                            '<button data-id="alert" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>' :
                            ($row->estado == "Anulada" ?
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' :
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En Trámite" style="color:white; background-color: #F8AD46" class="btn btn-block btn-outline massive">En Trámite</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000" class="btn btn-block btn-outline anular_solicitud">Anulada</a>'
                                )));
                } else if ($validar_politicas == 21 && $validar_politicas2 == 22) {
                    $modalstate = $row->estado == "En Trámite" ?
                    '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>' : ($row->estado == "Aprobada" ?
                        '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>' : ($row->estado == "Rechazada" ?
                            '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' : ($row->estado == "Anulada" ?
                                '<button data-id="alert" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>' :
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En Trámite" style="color:white; background-color: #F8AD46;" class="btn btn-block btn-outline massive">En Trámite</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c;" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>'
                                )));
                } else if ($validar_politicas == 21) {
                    $modalstate = $row->estado == "En Trámite" ?
                    '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>' : ($row->estado == "Aprobada" ?
                        '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>' : ($row->estado == "Rechazada" ?
                            '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' : ($row->estado == "Anulada" ?
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' :
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En Trámite" style="color:white; background-color: #F8AD46;" class="btn btn-block btn-outline massive">En Trámite</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c;" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>'
                                )));
                } else if ($validar_politicas == 22) {
                    $modalstate = $row->estado == "En Trámite" ?
                    '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c;" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>' : ($row->estado == "Aprobada" ?
                        '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>
                        <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>' : ($row->estado == "Rechazada" ?
                            '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' : ($row->estado == "Anulada" ?
                                '<button data-id="alert" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>' :
                                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En Trámite" style="color:white; background-color: #F8AD46;" class="btn btn-block btn-outline massive">En Trámite</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c;" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>'
                                )));
                }
            } else {
                $modalstate = $row->estado == "En Trámite" ?
                '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c;" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>
                <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red;" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>' : ($row->estado == "Aprobada" ?
                    '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Rechazada" style="color:white; background-color: red" class="btn btn-block btn-outline rechazar_solicitud">Rechazada</a>
                    <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>' : ($row->estado == "Rechazada" ?
                        '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' : ($row->estado == "Anulada" ?
                            '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Pendiente" style="color:white; background-color: #5bc0de;" class="btn btn-block btn-outline massive">Pendiente</a>' :
                            '<a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="En Trámite" style="color:white; background-color: #F8AD46;" class="btn btn-block btn-outline massive">En Trámite</a>
                            <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Aprobada" style="color:white; background-color: #5cb85c;" class="btn btn-block btn-outline  aprobar_solicitud">Aprobada</a>
                            <a href="javascript:" data-estado-anterior="' . $row->estado . '" data-id="' . $row['id'] . '" data-estado="Anulada" style="color:white; background-color: #000000;" class="btn btn-block btn-outline anular_solicitud">Anulada</a>'
                            )));
            }

            $modalanular = '<div class="row"><div class="col-md-6 form-group"><label>Solicitud</label><br><input type="text" name="nsolicitud" class="form-control" value="' . $row->numero . '" disabled=""></div><div class="col-md-6 form-group"><label>Cliente</label><br><input type="text" class="form-control" name="ncliente" value="' . $row->cliente->nombre . '" disabled=""></div></div><div class="row"><div class="col-md-12 form-group"><label>Razón</label><br><textarea name="motivoanula" id="motivoanula" class="form-control"></textarea></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><input type="hidden" name="id_solicitud" class="form-control" value="' . $row->id . '" disabled=""><button class="btn btn-success massive" id="guardaranular" data-estado-anterior="' . $row->estado . '" data-estado="Anulada">Guardar</button></div></div>';

            $modalaprobar = '<div class="row"><div class="col-md-12 form-group"><label>Número de Póliza</label><br><input type="text" name="npoliza" id="npoliza" class="form-control" ></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><button class="btn btn-success massive" id="guardaraprobar" data-estado-anterior="' . $row->estado . '" data-solicitud="' . $row->numero . '" data-id="' . $row->id . '" data-estado="Aprobada">Aprobar</button></div></div>';

            $modalrehazar = '<div class="row"><div class="col-md-6 form-group"><label>Solicitud</label><br><input type="text" name="nsolicitud" class="form-control" value="' . $row->numero . '" disabled=""></div><div class="col-md-6 form-group"><label>Cliente</label><br><input type="text" class="form-control" name="ncliente" value="' . $row->cliente->nombre . '" disabled=""></div></div><div class="row"><div class="col-md-12 form-group"><label>Razón</label><br><textarea name="motivorechazar" id="motivorechazar" class="form-control"></textarea></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><input type="hidden" name="id_solicitud" class="form-control" value="' . $row->id . '" disabled=""><button class="btn btn-success massive" id="guardarrechazar" data-estado-anterior="' . $row->estado . '" data-estado="Rechazada">Guardar</button></div></div>';

            $response->rows[$i]["id"] = $row->id;
            $response->rows[$i]["cell"] = array(
                $row->id,
                '<a href="' . base_url('solicitudes/editar/' . $uuid_solicitudes) . '" style="color:blue;">' . Util::verificar_valor($row->numero) . '</a>',
                '<a href="' . base_url('clientes/ver/' . $uuid_cliente) . '" style="color:blue;">' . Util::verificar_valor($row->cliente->nombre) . '</a>',
                '<a href="' . base_url('aseguradoras/editar/' . $uuid_aseguradora) . '" style="color:blue;">' . Util::verificar_valor($row->aseguradora->nombre) . '</a>',
                Util::verificar_valor($row->ramorelacion->nombre),
                Util::verificar_valor($row->tipo->nombre),
                    //($row->created_at->diff($now)->days < 1) ? '1' : $row->created_at->diff($now)->days,
                ($modulo == "Clientes") ? ( ($row->created_at != "") ? Carbon::createFromFormat('Y-m-d  H:i:s', $row->created_at)->format('d/m/Y') : "" ) : ( $row->dias_transcurridos == '' ? ($row->created_at->diff($now)->days < 1) ? '1' : $row->created_at->diff($now)->days : $row->dias_transcurridos ),
                    //$row->created_at != "" ? Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d/m/Y') : "",
                ($modulo == "Clientes") ? ($row->dias_transcurridos == '' ? (($row->created_at->diff($now)->days < 1) ? '1' : $row->created_at->diff($now)->days) : $row->dias_transcurridos ) : ($row->created_at != "" ? Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d/m/Y') : ""),
                Util::verificar_valor($row->usuario->nombre . " " . $row->usuario->apellido),
                !empty($row->estado) ? '<span style="color:white; ' . $estado_color . '" class="btn btn-xs btn-block estadoSolicitudes" data-id="' . $row['id'] . '" data-solicitudEstado="' . $row->estado . '">' . $row->estado . '</span>' : "",
                $link_option,
                $hidden_options,
                $modalstate,
                $modalanular,
                $modalaprobar,
                $modalrehazar
                );
            $i++;
        }
    }
    echo json_encode($response);
    exit;
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
    $uuid = $this->uuid_soli;
//      $uuid = $campo['uuid'];
    $bitacora = $this->solicitudesRepository->verSolicitudes(hex2bin(strtolower($uuid)));


    $data = array();
    $fechas = $this->bitacoraModel;
    $data["Fecha"] = $fechas;
    $data["n_solicitud"] = $bitacora->id;
    $data["historial"] = bitacoraModel::join('usuarios', 'seg_solicitudes_bitacora.usuario_id', '=', 'usuarios.id')
    ->where('comentable_id', $bitacora->id)
    ->where('comentable_type', 'Comentario')
    ->select('seg_solicitudes_bitacora.comentario', "seg_solicitudes_bitacora.comentable_type", 'seg_solicitudes_bitacora.created_at', "usuarios.nombre", "usuarios.apellido")
    ->orderBy("created_at", "desc")->get()->toArray();

    $this->load->view('comentarios', $data);
}

function ajax_carga_comentarios() {
    $html = '';
    try {
        $id_solicitud = $_POST["n_solicitud"];

        $uuid = $this->uuid_soli;
        $fechas = $this->bitacoraModel;
//      $uuid = $campo['uuid'];
        $Bitacora = $this->solicitudesRepository->verSolicitudes(hex2bin(strtolower($uuid)));
        $historial = bitacoraModel::join('usuarios', 'seg_solicitudes_bitacora.usuario_id', '=', 'usuarios.id')
        ->where('comentable_id', $id_solicitud)
        ->where('comentable_type', 'Comentario')
        ->select('seg_solicitudes_bitacora.comentario', "seg_solicitudes_bitacora.comentable_type", 'seg_solicitudes_bitacora.created_at', "usuarios.nombre", "usuarios.apellido")
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

    $data["n_solicitud"] = $id_solicitud;
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
        $id_solicitud = $this->input->post('n_solicitud');
        $comentario = $this->input->post('comentario');
        $usuario = $this->session->userdata['id_usuario'];
        $id_empresa = $this->empresa_id;


        $comment = ['comentario' => $comentario, 'usuario_id' => $usuario, 'comentable_id' => $id_solicitud, 'comentable_type' => $tipo, 'empresa_id' => $id_empresa];

        $bus = SolicitudesModel::find($id_solicitud);
        if ($bus->count() != 0) {
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
        'public/assets/js/modules/solicitudes/bitacora.js'
        ));
    $fechas = $this->bitacoraModel;
    $data["Fecha"] = $fechas;
    $bitacora = $this->solicitudesRepository->verSolicitudes(hex2bin(strtolower($uuid_solicitudes)));
    $historial = bitacoraModel::join('usuarios', 'seg_solicitudes_bitacora.usuario_id', '=', 'usuarios.id')
    ->where('comentable_id', $bitacora->id)
    ->select('seg_solicitudes_bitacora.comentario', "seg_solicitudes_bitacora.comentable_type", 'seg_solicitudes_bitacora.created_at', "usuarios.nombre", "usuarios.apellido")
    ->orderBy("created_at", "desc")
    ->get()->toArray();
    $numero = $bitacora->numero;
    $breadcrumb = array(
        "titulo" => '<i class="fa fa-archive"></i> Historial: Solicitud N° ' . $numero,
        "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#", "activo" => true),
            1 => array("nombre" => 'Solicitudes', "url" => "solicitudes/listar", "activo" => true),
            3 => array("nombre" => $numero, "url" => "solicitudes/editar/" . $uuid_solicitudes, "activo" => false),
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

public function ajax_cambiar_estado_solicitudes() {

    $FormRequest = new Flexio\Modulo\Solicitudes\Models\GuardarSolicitudesEstado;

    try {
        $msg = $Agentes = $FormRequest->guardar();
    } catch (\Exception $e) {
        $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
    }

    print json_encode($msg);
    exit;
}

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
        if (isset($campos['solicitud'])) {
            $solicitud = $campos['solicitud'];
        } else {
            $solicitud = "";
        }
        $usuario = $this->usuario_id;

        $now = Carbon::now();
        $datosSolicitud = $this->solicitudesModel->where(['numero' => $solicitud])->first();

        if ($estado == "Aprobada") {

            $solicitudes = $this->solicitudesModel->join('seg_solicitudes_vigencia', 'seg_solicitudes.id', '=', 'seg_solicitudes_vigencia.id_solicitudes')
            ->join('seg_solicitudes_prima', 'seg_solicitudes.id', '=', 'seg_solicitudes_prima.id_solicitudes')
            ->where('seg_solicitudes.numero', $solicitud)
            ->select('seg_solicitudes.id AS id', 'seg_solicitudes.cliente_id AS cliente', "seg_solicitudes.ramo as ramo", 'seg_solicitudes.ramo_id as ramo_id', 'seg_solicitudes.usuario_id AS usuario', 'seg_solicitudes.plan_id AS plan_id', 'seg_solicitudes.comision AS comision', 'seg_solicitudes.porcentaje_sobre_comision AS sobre_comision', 'seg_solicitudes.impuesto AS impuesto', 'seg_solicitudes.numero AS numero', 'seg_solicitudes.centro_contable AS centro_contable', "seg_solicitudes_vigencia.vigencia_desde AS desde", "seg_solicitudes_vigencia.vigencia_hasta AS hasta", "seg_solicitudes_vigencia.poliza_declarativa AS poliza_declarativa", "seg_solicitudes_prima.frecuencia_pago AS frecuencia", "seg_solicitudes.aseguradora_id AS aseguradora_id")->first()->toArray();

            $ramo = Ramos::where(['id' => $solicitudes['ramo_id']])->first();
            if ($ramo->id_tipo_poliza == 1) {
                $tipo_ramo = "individual";
            } elseif ($ramo->id_tipo_poliza == 2) {
                $tipo_ramo = "colectivo";
            }

            $comentario = "No. de Solicitud: ".$solicitudes['numero']."<br> No. de Poliza: " . $motivo . "";

            $descuentoPlan = Planes::where(['id' => $solicitudes['plan_id']])->first();

            $sol = ['numero' => $motivo,
            'creado_por' => $usuario,
            'empresa_id' => $this->empresa_id,
            'cliente' => $solicitudes['cliente'],
            'ramo_id' => $solicitudes['ramo_id'],
            'ramo' => $solicitudes['ramo'],
            'tipo_ramo' => $tipo_ramo,
            'id_tipo_int_asegurado' => $ramo->id_tipo_int_asegurado,
            'usuario' => $solicitudes['usuario'],
            'estado' => 'Por Facturar',
            'inicio_vigencia' => $solicitudes['desde'],
            'fin_vigencia' => $solicitudes['hasta'],
            'frecuencia_facturacion' => $solicitudes['frecuencia'],
            'ultima_factura' => $solicitudes['hasta'],
            'categoria' => '44',
            'solicitud' => $solicitud,
            'aseguradora_id' => $solicitudes['aseguradora_id'],
            'plan_id' => $solicitudes['plan_id'],
            'comision' => $solicitudes['comision'],
            'poliza_declarativa' => $solicitudes['poliza_declarativa'],
            'porcentaje_sobre_comision' => $solicitudes['sobre_comision'],
            'impuesto' => $solicitudes['impuesto'],
            'desc_comision' => $descuentoPlan->desc_comision,
            'centro_contable' => $solicitudes['centro_contable']
            ];

            $datos['dias_transcurridos'] = ($datosSolicitud->created_at->diff($now)->days < 1) ? '1' : $datosSolicitud->created_at->diff($now)->days;
            $this->solicitudesModel->where(['numero' => $solicitud])->update($datos);

                //print_r($sol);


            $poliza1 = new Flexio\Modulo\Polizas\Models\Polizas;
            $p = $poliza1->create($sol);

            $Bitacora = new Flexio\Modulo\Polizas\Models\PolizasBitacora;
            $fecha_creacion = date('Y-m-d h:i:s');
            $comentario2 = "Solicitud # " .$solicitudes['numero']. "<br>";
            $comment2 = ['comentario' => $comentario2, 'usuario_id' => $this->session->userdata['id_usuario'], 'comentable_id' => $p->id, 'comentable_type' => 'Creacion Poliza', 'created_at' =>  $fecha_creacion, 'empresa_id' => $this->empresa_id];
            $Bitacora->create($comment2);

            $poliza2 = new Flexio\Modulo\Polizas\Models\PolizasPrima;
            $prima = $this->solicitudesRepository->verPrima($solicitudes['id']);
            $solprima = [
            'id_poliza' => $p->id,
            'prima_anual' => $prima->prima_anual,
            'impuesto' => $prima->impuesto,
            'otros' => $prima->otros,
            'descuentos' => $prima->descuentos,
            'total' => $prima->total,
            'frecuencia_pago' => $prima->frecuencia_pago,
            'metodo_pago' => $prima->metodo_pago,
            'fecha_primer_pago' => $prima->fecha_primer_pago,
            'cantidad_pagos' => $prima->cantidad_pagos,
            'sitio_pago' => $prima->sitio_pago,
            'centro_facturacion' => $prima->centro_facturacion,
            'direccion_pago' => $prima->direccion_pago
            ];
            $p2 = $poliza2->create($solprima);

            $poliza3 = new Flexio\Modulo\Polizas\Models\PolizasVigencia;
            $vigencia = $this->solicitudesRepository->verVigencia($solicitudes['id']);
            $solvigencia = [
            'id_poliza' => $p->id,
            'vigencia_desde' => $vigencia->vigencia_desde,
            'vigencia_hasta' => $vigencia->vigencia_hasta,
            'suma_asegurada' => $vigencia->suma_asegurada,
            'tipo_pagador' => $vigencia->tipo_pagador,
            'pagador' => $vigencia->pagador,
                        //'poliza_declarativa' => $vigencia->poliza_declarativa
            ];
            $p3 = $poliza3->create($solvigencia);

            $poliza4 = new Flexio\Modulo\Polizas\Models\PolizasCobertura;
            $coberturas = $this->solicitudesCoberturas->where(['id_solicitud' => $solicitudes['id']])->get();
            foreach ($coberturas AS $value) {
                $solCoberturas = [
                'cobertura' => $value->cobertura,
                'valor_cobertura' => $value->valor_cobertura,
                'id_poliza' => $p->id
                ];
                $p4 = $poliza4->create($solCoberturas);
            }

            $poliza5 = new Flexio\Modulo\Polizas\Models\PolizasDeduccion;
            $deducion = $this->solicitudesDeduccion->where(['id_solicitud' => $solicitudes['id']])->get();
            foreach ($deducion AS $value) {
                $solDeducion = [
                'deduccion' => $value->deduccion,
                'valor_deduccion' => $value->valor_deduccion,
                'id_poliza' => $p->id
                ];
                $p5 = $poliza5->create($solDeducion);
            }

            $poliza6 = new Flexio\Modulo\Polizas\Models\PolizasParticipacion;
            $participacion = $this->solicitudesRepository->verParticipacion($solicitudes['id']);
            foreach ($participacion AS $value) {
                $agentes = Agentes::where(['id' => $value->agente])->first();
                $solParticipacion = [
                'id_poliza' => $p->id,
                'agente' => $agentes->nombre,
                ];
                $solParticipacion['porcentaje_participacion'] = $value->porcentaje_participacion == '' ? 0 : $value->porcentaje_participacion;
                $p6 = $poliza6->create($solParticipacion);
            }

            $poliza7 = new Flexio\Modulo\Polizas\Models\PolizasCliente;
            $clause['cli_clientes.empresa_id'] = $this->empresa_id;
            $clause['cli_clientes.id'] = $solicitudes['cliente'];

            $cliente = $this->clienteModel->join('cli_clientes_telefonos', 'cli_clientes_telefonos.cliente_id', '=', 'cli_clientes.id')
            ->join('cli_clientes_correos', 'cli_clientes_correos.cliente_id', '=', 'cli_clientes.id')
            ->join('cli_centros_facturacion', 'cli_centros_facturacion.cliente_id', '=', 'cli_clientes.id')
            ->where($clause)
            ->select('cli_clientes.*', 'cli_clientes_telefonos.telefono', 'cli_centros_facturacion.direccion', 'cli_clientes_correos.correo')
            ->first();
            $group = $this->clienteModel->join('grp_grupo_clientes', 'grp_grupo_clientes.uuid_cliente', '=', 'cli_clientes.uuid_cliente')
            ->join('grp_grupo', 'grp_grupo.id', '=', 'grp_grupo_clientes.grupo_id')
            ->where($clause)
            ->where('grp_grupo_clientes.deleted_at', '=', NULL)
            ->select('grp_grupo.nombre')
            ->first();

            if (!count($cliente)) {
                $cliente = clienteModel::where(['id' => $solicitudes['cliente']])->first();
            }

            $solCliente = [
            'id_poliza' => $p->id,
            'nombre_cliente' => $cliente->nombre,
            'identificacion' => $cliente->tipo_identificacion,
            'n_identificacion' => $cliente->identificacion,
            'grupo' => $datosSolicitud->grupo,
            'telefono' => $cliente->telefono,
            'correo_electronico' => $cliente->correo,
            'direccion' => $cliente->direccion,
            'exonerado_impuesto' => $cliente->exonerado_impuesto
            ];

            $p7 = $poliza7->create($solCliente);

            $id_intereses = InteresesAsegurados_detalles::where(['id_solicitudes' => $solicitudes['id']])->get();
            foreach ($id_intereses as $value) {
                $interes_id = InteresesAsegurados::where(['id' => $value->id_intereses])->first();
                if ($ramo->id_tipo_int_asegurado == 1) {
                    $datosArticulo = ArticuloModel::where(['id' => $interes_id->interesestable_id])->first()->toArray();
                    unset($datosArticulo["id"]);
                    unset($datosArticulo["updated_at"]);
                    unset($datosArticulo["created_at"]);
                    $datosArticulo["id_poliza"] = $p->id;
                    $datosArticulo["numero"] = $interes_id->numero;
                    $datosArticulo["detalle_certificado"] = $value->detalle_certificado;
                    $datosArticulo["detalle_suma_asegurada"] = $value->detalle_suma_asegurada;
                    $datosArticulo["detalle_prima"] = $value->detalle_prima;
                    $datosArticulo["detalle_deducible"] = $value->detalle_deducible;
                    $datosArticulo["estado"] = $interes_id->estado;
                    $datosArticulo["fecha_inclusion"] = $value->fecha_inclusion;
                    PolizasArticulo::create($datosArticulo);
                } elseif ($ramo->id_tipo_int_asegurado == 2) {
                    $datoscarga = CargaModel::where(['id' => $interes_id->interesestable_id])->first()->toArray();
                    unset($datoscarga["id"]);
                    unset($datoscarga["updated_at"]);
                    unset($datoscarga["created_at"]);
                    unset($datoscarga["estado"]);
                    $datoscarga["id_poliza"] = $p->id;
                    $datoscarga["detalle_certificado"] = $value->detalle_certificado;
                    $datoscarga["detalle_suma_asegurada"] = $value->detalle_suma_asegurada;
                    $datoscarga["detalle_prima"] = $value->detalle_prima;
                    $datoscarga["detalle_deducible"] = $value->detalle_deducible;
                    $datoscarga["estado"] = $interes_id->estado;
                    $datoscarga["fecha_inclusion"] = $value->fecha_inclusion;
                    PolizasCarga::create($datoscarga);
                } elseif ($ramo->id_tipo_int_asegurado == 3) {
                    $datosAereo = AereoModel::where(['id' => $interes_id->interesestable_id])->first()->toArray();
                    unset($datosAereo["id"]);
                    unset($datosAereo["updated_at"]);
                    unset($datosAereo["created_at"]);
                    $datosAereo["id_poliza"] = $p->id;
                    $datosAereo["detalle_certificado"] = $value->detalle_certificado;
                    $datosAereo["detalle_suma_asegurada"] = $value->detalle_suma_asegurada;
                    $datosAereo["detalle_prima"] = $value->detalle_prima;
                    $datosAereo["detalle_deducible"] = $value->detalle_deducible;
                    $datosAereo["estado"] = $interes_id->estado;
                    $datosAereo["fecha_inclusion"] = $value->fecha_inclusion;
                    PolizasAereo::create($datosAereo);
                } elseif ($ramo->id_tipo_int_asegurado == 4) {
                    $datosMaritimo = MaritimoModel::where(['id' => $interes_id->interesestable_id])->first()->toArray();
                    unset($datosMaritimo["id"]);
                    unset($datosMaritimo["updated_at"]);
                    unset($datosMaritimo["created_at"]);
                    $datosMaritimo["id_poliza"] = $p->id;
                    $datosMaritimo["detalle_certificado"] = $value->detalle_certificado;
                    $datosMaritimo["detalle_suma_asegurada"] = $value->detalle_suma_asegurada;
                    $datosMaritimo["detalle_prima"] = $value->detalle_prima;
                    $datosMaritimo["detalle_deducible"] = $value->detalle_deducible;
                    $datosMaritimo["estado"] = $interes_id->estado;
                    $datosMaritimo["fecha_inclusion"] = $value->fecha_inclusion;
                    PolizasMaritimo::create($datosMaritimo);
                } elseif ($ramo->id_tipo_int_asegurado == 5) {
                    $datosPersonas = PersonasModel::where(['id' => $interes_id->interesestable_id])->first()->toArray();
                    unset($datosPersonas["id"]);
                    unset($datosPersonas["updated_at"]);
                    unset($datosPersonas["created_at"]);
                    $datosPersonas["id_interes"] = $value->id_intereses;
                    $datosPersonas["id_poliza"] = $p->id;
                    $datosPersonas["detalle_relacion"] = $value->detalle_relacion;
                    $datosPersonas["detalle_int_asociado"] = $value->detalle_int_asociado;
                    $datosPersonas["detalle_certificado"] = $value->detalle_certificado;
                    $datosPersonas["detalle_beneficio"] = $value->detalle_beneficio;
                    $datosPersonas["detalle_monto"] = $value->detalle_monto;
                    $datosPersonas["detalle_prima"] = $value->detalle_prima;
                    $datosPersonas["estado"] = $interes_id->estado;
                    $datosPersonas["fecha_inclusion"] = $value->fecha_inclusion;
                    $datosPersonas["detalle_participacion"] = $value->detalle_participacion;
                    $datosPersonas["detalle_suma_asegurada"] = $value->detalle_suma_asegurada;
                    $datosPersonas["tipo_relacion"] = $value->tipo_relacion;
                    PolizasPersonas::create($datosPersonas);
                } elseif ($ramo->id_tipo_int_asegurado == 6) {
                    $datosProyecto = ProyectoModel::where(['id' => $interes_id->interesestable_id])->first()->toArray();
                    unset($datosProyecto["id"]);
                    unset($datosProyecto["updated_at"]);
                    unset($datosProyecto["created_at"]);
                    unset($datosProyecto["estado"]);
                    $datosProyecto["id_poliza"] = $p->id;
                    $datosProyecto["detalle_certificado"] = $value->detalle_certificado;
                    $datosProyecto["detalle_suma_asegurada"] = $value->detalle_suma_asegurada;
                    $datosProyecto["detalle_prima"] = $value->detalle_prima;
                    $datosProyecto["detalle_deducible"] = $value->detalle_deducible;
                    $datosProyecto["estado"] = $interes_id->estado;
                    $datosProyecto["fecha_inclusion"] = $value->fecha_inclusion;
                    PolizasProyecto::create($datosProyecto);
                } elseif ($ramo->id_tipo_int_asegurado == 7) {
                    $datosUbicacion = UbicacionModel::where(['id' => $interes_id->interesestable_id])->first()->toArray();
                    unset($datosUbicacion["id"]);
                    unset($datosUbicacion["updated_at"]);
                    unset($datosUbicacion["created_at"]);
                    unset($datosUbicacion["estado"]);
                    $datosUbicacion["id_poliza"] = $p->id;
                    $datosUbicacion["detalle_certificado"] = $value->detalle_certificado;
                    $datosUbicacion["detalle_suma_asegurada"] = $value->detalle_suma_asegurada;
                    $datosUbicacion["detalle_prima"] = $value->detalle_prima;
                    $datosUbicacion["detalle_deducible"] = $value->detalle_deducible;
                    $datosUbicacion["estado"] = $interes_id->estado;
                    $datosUbicacion["fecha_inclusion"] = $value->fecha_inclusion;
                    PolizasUbicacion::create($datosUbicacion);
                } elseif ($ramo->id_tipo_int_asegurado == 8) {
                    $datosVehiculo = VehiculoModel::where(['id' => $interes_id->interesestable_id])->first()->toArray();
                    unset($datosVehiculo["id"]);
                    unset($datosVehiculo["updated_at"]);
                    unset($datosVehiculo["created_at"]);
                    $datosVehiculo["id_poliza"] = $p->id;
                    $datosVehiculo["numero"] = $interes_id->numero;
                    $datosVehiculo["detalle_certificado"] = $value->detalle_certificado;
                    $datosVehiculo["detalle_suma_asegurada"] = $value->detalle_suma_asegurada;
                    $datosVehiculo["detalle_prima"] = $value->detalle_prima;
                    $datosVehiculo["detalle_deducible"] = $value->detalle_deducible;
                    $datosVehiculo["estado"] = $interes_id->estado;
                    $datosVehiculo["fecha_inclusion"] = $value->fecha_inclusion;
                    PolizasVehiculo::create($datosVehiculo);
                }
            }

            $sel_uuid = $poliza1->where("id", $p->id)->first()->toArray();
            $inf["msg"] = "Ok";
            $inf["id"] = $p->id;
            $inf["uuid"] = strtoupper(bin2hex($sel_uuid["uuid_polizas"]));
        } elseif ($estado == "Anulada") {
            $datos['dias_transcurridos'] = ($datosSolicitud->created_at->diff($now)->days < 1) ? '1' : $datosSolicitud->created_at->diff($now)->days;
            $this->solicitudesModel->where(['numero' => $solicitud])->update($datos);
            $comentario = "Estado Actual: " . $estado . "<br>Estado Anterior: " . $estado_anterior . "<br>Motivo: " . $motivo . "<br>";
        } elseif ($estado == "Rechazada") {

            $datosSolicitud = $this->solicitudesModel->where(['numero' => $solicitud])->first();
            $datos['dias_transcurridos'] = ($datosSolicitud->created_at->diff($now)->days < 1) ? '1' : $datosSolicitud->created_at->diff($now)->days;
            $this->solicitudesModel->where(['numero' => $solicitud])->update($datos);
            $comentario = "Estado Actual: " . $estado . "<br>Estado Anterior: " . $estado_anterior . "<br>Motivo: " . $motivo . "<br>";
        } else {
            $comentario = "Estado Actual: " . $estado . "<br>Estado Anterior: " . $estado_anterior;
        }
        $fieldset["comentario"] = $comentario;
        $fieldset["comentable_type"] = $tipo;
        $fieldset["comentable_id"] = $id_comentario;
        $fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
        $fieldset["empresa_id"] = $this->empresa_id;
        
        $interesaseg = $this->bitacoraModel->create($fieldset);

//            $campo = ['comentario' => $comentario, 'comentable_type' => $tipo, 'usuario_id' => $usuario, 'empresa_id' => $this->empresa_id, 'comentable_id'=>$id_comentario ];
//            $Solicitud = $Bitacora->create($campo);
            //$msg = $Agentes = $FormRequest->guardar();
    } catch (\Exception $e) {
        $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        $inf["msg"] = "Err";
    }
    die(json_encode($inf));
    exit;
}

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

public function prueba() {
    $x = $_POST['campo'];
    print_r($x);
}

public function crear($id_ramo = null, $id_interes = null) {

    if (!is_null($this->session->flashdata('mensaje'))) {
        $mensaje = $this->session->flashdata('mensaje');
    } else {
        $mensaje = [];
    }

    if (!$this->auth->has_permission('acceso', 'solicitudes/crear')) {
            // No, tiene permiso, redireccionarlo.
        $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ingresar a crear', 'titulo' => 'Solicitudes ');

        $this->session->set_flashdata('mensaje', $mensaje);

        redirect(base_url('solicitudes/listar'));
    } else {
        $acceso = 1;
    }

    if (empty($id_ramo)) {
        $solicitudes_id = !empty($_POST['solicitud_id']) ? $_POST['solicitud_id'] : '';
    } else {
        $solicitudes_id = $id_ramo;
    }

    $solicitudes_titulo = Ramos::find($solicitudes_id);
    $titulo = $solicitudes_titulo->nombre;
    $ramo = $solicitudes_titulo->nombre;
    $tipo_poliza = $solicitudes_titulo->id_tipo_poliza;
    $codigo_ramo = $solicitudes_titulo->codigo_ramo;
    $id_ramo = $solicitudes_titulo->id;
    $idpadre = $solicitudes_titulo->padre_id;
    $tipo_interes_asegurado = $solicitudes_titulo->id_tipo_int_asegurado;

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

    if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
        $acceso = 0;
        $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
    }

    $this->_Css();
    $this->_js();
    $this->assets->agregar_js(array(
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/modules/solicitudes/formulario.js',
        'public/assets/js/modules/solicitudes/crear.vue.js',
        'public/assets/js/modules/solicitudes/component.vue.js',
        'public/assets/js/modules/solicitudes/plugins.js',
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js'
        ));
        //Catalogos
    $catalogo_clientes = $this->SegInteresesAseguradosRepository->listar_catalogo('Identificacion', 'orden');
    $pagador = $this->SegInteresesAseguradosRepository->listar_catalogo('pagador_seguros', 'orden');
    $cantidad_pagos = $this->SegInteresesAseguradosRepository->listar_catalogo('cantidad_pagos', 'orden');
    $frecuencia_pagos = $this->SegInteresesAseguradosRepository->listar_catalogo('frecuencia_pagos', 'orden');
    $metodo_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('metodo_pago', 'orden');
    $sitio_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('sitio_pago', 'orden');
        //$centro_facturacion = Catalogos_orm::where('identificador', 'like', 'centro_facturacion')->orderBy("orden")->get(array('valor', 'etiqueta'));    
    $agentes = Agentes::join('agt_agentes_ramos', 'agt_agentes.id', '=', 'agt_agentes_ramos.id_agente')
    ->where('id_ramo', '=', $solicitudes_id)
    ->orderBy("nombre")->get(array('agt_agentes.id', 'nombre'));
    $estado = $this->SegCatalogoRepository->listar_catalogo('estado_s', 'orden');

    if (!empty($id_interes)) {
        $selInteres = $id_interes;
    } else {
        $selInteres = '';
    }

    if ($this->auth->has_permission('acceso', 'solicitudes/editar asignación')) {
        $usersList = Usuarios::join("seg_ramos_usuarios", "seg_ramos_usuarios.id_usuario", "=", "usuarios.id")->where(array("usuarios.estado" => 'Activo', "seg_ramos_usuarios.id_ramo" => $solicitudes_id))->get();
        if ($usersList->count() == 0) {
            $usersList = 0;
        }
    } else {
        $usersList = Usuarios::join("seg_ramos_usuarios", "seg_ramos_usuarios.id_usuario", "=", "usuarios.id")->where(array("usuarios.estado" => 'Activo', "seg_ramos_usuarios.id_ramo" => $solicitudes_id, "usuarios.id" => $this->usuario_id))->get();
        if ($usersList->count() == 0) {
            $usersList = 0;
        }
    }


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

    if(count($centroContable) == 1){
        $id_centro_contable = $centroContable[0]->id;
    }else{
        $id_centro_contable = 0;
    }

    $agenteprincipaltotal=Agentes::where('principal',1)->
    where('id_empresa',$this->empresa_id)->count();

    if($agenteprincipaltotal>0)
    {
     $agenteprincipal=Agentes::where('id_empresa',$this->empresa_id)->where('principal',1)->first();
     $agenteprincipalnombre=$agenteprincipal->nombre;
     $agtPrincipalporcentaje=100;
 }
 else
 {
     $agenteprincipalnombre="";
     $agtPrincipalporcentaje=0;
 }


 $data = array();
 $this->assets->agregar_var_js(array(
    "vista" => 'crear',
    "agtPrincipal"=>$agenteprincipalnombre,
    "agtPrincipalporcentaje"=>$agtPrincipalporcentaje,
    "acceso" => $acceso,
    "ramo_id" => $solicitudes_id,
    "catalogo_clientes" => $catalogo_clientes,
    "pagador" => $pagador,
    "cantidad_pagos" => $cantidad_pagos,
    "frecuencia_pagos" => $frecuencia_pagos,
    "metodo_pago" => $metodo_pago,
    "sitio_pago" => $sitio_pago,
    "agentes" => $agentes,
    "ramo" => $ramo,
    "id_tipo_poliza" => $tipo_poliza,
    "codigo_ramo" => $codigo_ramo,
    "nombre_padre" => $nombrepadre,
    "estado_solicitud" => $estado,
    "ramoscadena" => $ramocadena,
    "id_tipo_int_asegurado" => $tipo_interes_asegurado,
    "documentacionesgbd" => "",
            //********************************************************************
    "cliente" => "undefined",
    "editar" => "undefined",
    "asegurada" => "undefined",
    "plan" => "undefined",
    "vigencia" => "undefined",
    "prima" => "undefined",
    "estado" => "undefined",
    "participacion" => "undefined",
    "acreedores" => "undefined",
    "observaciones" => "undefined",
    "uuid_solicitudes" => "undefined",
    "comision" => "undefined",
    "permisos_editar" => $this->auth->has_permission('acceso', 'solicitudes/editar') == true ? 1 : 0,
            //********************************************************************
    "desde" => "solicitudes",
    "indcolec"=>$indcolec,
    "grupogbd" => "",
    "direcciongbd" => "",
    "documentaciones" => "",
    "documentacion_editar" => "",
    "selInteres" => $selInteres,
            //***************
    "usuario_id" => $this->usuario_id,
    "usersList" => $usersList,
    "editar_asignado" => 1,
    "centros_contables" => $centroContable,
    "id_centro_contable" => $id_centro_contable,
    "validavida" => $validavida
    ));



 $breadcrumb = array(
    "titulo" => '<i class="fa fa-archive"></i> Solicitudes: Crear / ' . $titulo,
    "ruta" => array(
        0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
        1 => array("nombre" => '<a href="' . base_url() . 'solicitudes/listar">Solicitudes</a>', "activo" => false),
        2 => array("nombre" => '<b>Crear</b>', "activo" => true)
        ),
    "filtro" => false,
    "menu" => array()
    );
 $data['mensaje'] = $mensaje;
 $data['id_ramo'] = $solicitudes_id;

 $this->template->agregar_titulo_header('Solicitudes: Crear');
 $this->template->agregar_breadcrumb($breadcrumb);
 $this->template->agregar_contenido($data);
 $this->template->visualizar();
}

function ajax_get_asociados() {

    $unico = $_POST['unico'];
    $tablaTipo = $_POST['tablaTipo'];

        //var_dump($tablaTipo);

    if($tablaTipo == 'vida' || $tablaTipo == 'salud' || $tablaTipo == 'accidentes'){
        $inter = InteresesAsegurados_detalles::join("int_intereses_asegurados", "int_intereses_asegurados.id", "=", "int_intereses_asegurados_detalles.id_intereses")
        ->join("int_personas", 'int_personas.id', '=', 'int_intereses_asegurados.interesestable_id')
        ->where('detalle_unico', $unico)
        ->where('detalle_relacion', '<>','Dependiente')
        ->where('detalle_relacion', '<>','Beneficiario')
        ->where('int_intereses_asegurados.empresa_id', $this->empresa_id)
        ->select('int_personas.id', 'int_personas.nombrePersona', 'int_intereses_asegurados_detalles.detalle_certificado')
        ->get();
    }else{

        $inter = InteresesAsegurados_detalles::join("int_intereses_asegurados", "int_intereses_asegurados.id", "=", "int_intereses_asegurados_detalles.id_intereses")
        ->join("int_personas", 'int_personas.id', '=', 'int_intereses_asegurados.interesestable_id')
        ->where('detalle_unico', $unico)
        ->where('int_intereses_asegurados.empresa_id', $this->empresa_id)
        ->select('int_personas.id', 'int_personas.nombrePersona', 'int_intereses_asegurados_detalles.detalle_certificado')
        ->get(); 
    }

    $response = new stdClass();
    $response->inter = array();
    $response->inter = $inter->toArray();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($response))->_display();

    exit;
}

function ajax_get_tipointereses() {

    $interes = $_POST['interes'];
    $unico = $_POST['unico'];
    $personVar = $this->input->post("tablaTipo");
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
    $select = $tbl .'.*';
    $inter = InteresesAsegurados::join('' . $tbl . '', '' . $tbl . '.id', '=', 'int_intereses_asegurados.interesestable_id')->where('int_intereses_asegurados.interesestable_type', $tipo)
    ->where('int_intereses_asegurados.estado', 'Activo')
    ->where('int_intereses_asegurados.empresa_id', $this->empresa_id)
    ->where('int_intereses_asegurados.deleted', 0)
    ->select('int_intereses_asegurados.id as mainId','int_intereses_asegurados.numero',''.$select.'')
    ->get();
    $tableIntereses=[];
    if(!isset($personVar)){             
        $intable =InteresesAsegurados_detalles::where('detalle_unico',$unico)
        ->select("id_intereses")
        ->get();
        

        foreach ($intable as $key => $value) {
         $tableIntereses[] = $value->id_intereses;   
     }
 }
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
        $v = $value->no_orden .'-'.$value->nombre_proyecto." (" . $value->numero . ")";
    } else if ($interes == "ubicacion") {
        $v = $value->nombre . " - " . $value->direccion . " (" . $value->numero . ")";
    } else if ($interes == "vehiculo") {
        $v = $value->motor . " (" . $value->numero . ")";
    }
    array_push($response->inter, array(
        "id" => $value->id,
        "numero" => $v,
        "disabled"=>in_array( $value->mainId,$tableIntereses)));
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

function ajax_get_clientes() {

    $clause['empresa_id'] = $this->empresa_id;

    if ($_POST['tipo_cliente'] == 'juridico') {
        $clause['tipo_identificacion'] = 'ruc';
    } else {
        $clause['tipo_identificacion'] = 'cedula';
    }

    $clientes = $this->clienteRepository->getClientesPorTipo($clause)
    ->select('id', 'nombre', 'identificacion','tipo_identificacion','detalle_identificacion')
    ->get()
    ->toArray();
    foreach ($clientes  as $key => $value) {
        if($value['tipo_identificacion'] == 'pasaporte' && $value['identificacion'] == ''){
            $clientes[$key]["identificacion"] = $value['detalle_identificacion']['pasaporte'];
        }
    }
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($clientes))->_display();

    exit;
}

function ajax_get_centro_facturable() {

    $clause['empresa_id'] = $this->empresa_id;
    $clause['cliente_id'] = $_POST['cliente_id'];
    $centro_facturacion = $this->centroModel->where($clause)->get()->toArray();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($centro_facturacion))->_display();

    exit;
}

function ajax_get_direccion() {

    $clause['empresa_id'] = $this->empresa_id;
    $clause['id'] = $_POST['centro_id'];
    $direccion = $this->centroModel->where($clause)->get()->toArray();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($direccion))->_display();

    exit;
}

function ajax_get_cliente() {

    $clause['cli_clientes.empresa_id'] = $this->empresa_id;
    $clause['cli_clientes.id'] = $_POST['cliente_id'];
    $clienteUuid = $this->clienteModel->where($clause)->select('uuid_cliente')->first();
    $uuid = hex2bin(strtolower($clienteUuid->uuid_cliente));

    $cliente = $this->clienteModel->join('cli_clientes_telefonos', 'cli_clientes_telefonos.cliente_id', '=', 'cli_clientes.id')
    ->join('cli_clientes_correos', 'cli_clientes_correos.cliente_id', '=', 'cli_clientes.id')
    ->join('cli_centros_facturacion', 'cli_centros_facturacion.cliente_id', '=', 'cli_clientes.id')
    ->where($clause)
    ->select('cli_clientes.*', 'cli_clientes_telefonos.telefono', 'cli_centros_facturacion.direccion', 'cli_clientes_correos.correo')
    ->first();
    $group = $this->clienteModel->join('grp_grupo_clientes', 'grp_grupo_clientes.uuid_cliente', '=', 'cli_clientes.uuid_cliente')
    ->join('grp_grupo', 'grp_grupo.id', '=', 'grp_grupo_clientes.grupo_id')
    ->where($clause)
    ->where('grp_grupo_clientes.deleted_at', '=', NULL)
    ->select('grp_grupo.nombre')
    ->get();
    $group2 = $this->clienteModel->join('grp_grupo_clientes', 'grp_grupo_clientes.uuid_cliente', '=', 'cli_clientes.uuid_cliente')
    ->join('grp_grupo', 'grp_grupo.id', '=', 'grp_grupo_clientes.grupo_id')
    ->where($clause)
    ->where('grp_grupo_clientes.deleted_at', '=', NULL)
    ->select('grp_grupo.nombre')
    ->first();
    $direccion2 = centroModel::where('cliente_id', $_POST['cliente_id'])
    ->where('empresa_id', $this->empresa_id)
    ->first();
    $direccion = centroModel::where('cliente_id', $_POST['cliente_id'])
    ->where('empresa_id', $this->empresa_id)
    ->get();
    if (!count($cliente)) {
        $cliente = $this->clienteModel->where($clause)->first();
    }
    $cliente['group'] = $group;
    $cliente['group2'] = $group2;
    $cliente['direccion'] = $direccion;
    $cliente['direccion2'] = $direccion2;
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($cliente))->_display();

    exit;
}

function ajax_get_pagador() {

    $clause['empresa_id'] = $this->empresa_id;

    $pagador_ = $this->SegInteresesAseguradosRepository->listar_catalogo('pagador_seguros', 'orden');
    $pagador = $pagador_->toArray();
    if ($_POST['tipo_cliente'] != 5) {
        unset($pagador[1]);
    }

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($pagador))->_display();

    exit;
}

function ajax_get_planes() {

    $ramo = Ramos::where('codigo_ramo', '=', $_POST['codigoRamo'])
    ->where('empresa_id', $this->empresa_id)
    ->select('id', 'padre_id')
    ->get()
    ->first();
    $clause['id_ramo'] = $ramo->id;
    $planes = $this->planesModel->getPlanes($clause)->get();
    if (!count($planes)) {
        $clause['id_ramo'] = $ramo->padre_id;
        $planes = $this->planesModel->getPlanes($clause)->get();
    }
    $response = new stdClass();
    foreach ($planes as $key => $value) {
        $response->planes = array(
            "id" => $value->id,
            "nombre" => $value->nombre,
            "primaNeta" => $value->prima_neta
            );
    }
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($response))->_display();
    exit;
}

function ajax_get_comision() {
    $idPlan = $_POST['id_planes'];
    $inicio = 1;
    $comisiones = Planes::join('seg_planes_comisiones', 'seg_planes.id', '=', 'seg_planes_comisiones.id_planes')
    ->join('contab_impuestos', 'seg_planes.id_impuesto', '=', 'contab_impuestos.id')
    ->where('seg_planes.id', $idPlan)
                //->where("seg_planes_comisiones.inicio", $inicio)
    ->select('seg_planes_comisiones.comision AS plancomision', 'seg_planes_comisiones.sobre_comision AS sobre_comision',"contab_impuestos.*")
    ->get();
        /* $comisiones = PlanesComisiones::where($clause)->get()->toArray();
          $impuesto_plan = Planes::where('id', $clause['id_planes'])->get()->toArray();
          $clause2['id'] = $impuesto_plan[0]['id_impuesto'];
          $clause2['empresa_id'] = $this->empresa_id;
          $impuesto = $this->impuestosModel->where($clause2)->get(array('id', 'nombre', 'impuesto'))->toArray(); */
          $response = new stdClass();
          foreach ($comisiones as $key => $value) {

            $response->planes = array(
                "impuesto" => array(
                    'id' => $value->id,
                    'nombre' => $value->nombre,
                    'impuesto' => $value->impuesto),
                'comisiones' => array(
                    'comision' => $value->plancomision
                    ),
                'sobre_comisiones' => array(
                    'sobre_comision' => $value->sobre_comision
                    )
                );
        }


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();

        exit;
    }

    function ajax_get_coberturas() {
        $clause['id_planes'] = $_POST['plan_id'];
        $coberturas = $this->coberturaModel->where($clause)->get()->toArray();
        $deducion = $this->deduciblesModel->where($clause)->get()->toArray();
        $response = new stdClass();
        $response->coberturas = $coberturas;
        $response->deducion = $deducion;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    function ajax_get_coberturas_editar() {
        $clause['id_solicitud'] = $_POST['id_solicitud'];
        $coberturas = $this->solicitudesCoberturas->where($clause)->get()->toArray();
        $deducion = $this->solicitudesDeduccion->where($clause)->get()->toArray();
        $response = new stdClass();
        $response->coberturas = $coberturas;
        $response->deducion = $deducion;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    function ajax_get_prima() {
        $idPlan = $_POST['id_planes'];
        $inicio = 1;
        $comisiones = Planes::where('seg_planes.id', $idPlan)->get();
        $response = new stdClass();
        foreach ($comisiones as $key => $value) {

            $response->planes = array(
                "prima" => $value->prima_neta
                );
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();

        exit;
    }

    function ajax_get_porcentaje() {
        $clause['agt_agentes_ramos.id_ramo'] = $_POST['ident_ramo'];
        $clause['agt_agentes.id'] = $_POST['agente_id'];
        $agentes = Agentes::join('agt_agentes_ramos', 'agt_agentes.id', '=', 'agt_agentes_ramos.id_agente')
        ->where($clause)
        ->get()->toArray();
//        $agentes = Agentes::where($clause)->get()->toArray();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($agentes))->_display();
        exit;
    }

    function ocultoformulario($id_ramo = NULL) {
        $provincias = $this->SegCatalogoRepository->listar_catalogo('provincias', 'orden');
        $letras = $this->SegCatalogoRepository->listar_catalogo('letras', 'orden');
        $clause = array(
            'seg_aseguradoras.empresa_id' => $this->empresa_id,
            'seg_aseguradoras.estado' => 'Activo',
            'seg_planes.id_ramo' => $id_ramo
            );

        $aseguradoras = Aseguradoras::select("seg_aseguradoras.id", "seg_aseguradoras.nombre")->join("seg_planes", "seg_planes.id_aseguradora", "=", "seg_aseguradoras.id")->where($clause)->groupBy("seg_aseguradoras.id", "seg_aseguradoras.nombre")->get();

        if ($aseguradoras->count() == 0) {
            $info_ramo = Ramos::find($id_ramo);
            $id_padre = $info_ramo['padre_id'];

            $clause["seg_planes.id_ramo"] = $id_padre;

            $aseguradoras = Aseguradoras::select("seg_aseguradoras.id", "seg_aseguradoras.nombre")->join("seg_planes", "seg_planes.id_aseguradora", "=", "seg_aseguradoras.id")->where($clause)->groupBy("seg_aseguradoras.id", "seg_aseguradoras.nombre")->get();
        }
        $cont_nivel1 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get());

        $ramopadre = Ramos::where('id', $id_ramo)
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
            ->where('modulo', "!=", "reclamo")
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
                ->where('modulo', "!=", "reclamo")
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
            ->where('modulo', "!=", "reclamo")
            ->get());
        if ($cont_nivel1 > 0) {
            $documentaciones = $this->RamosDocumentos->where(['id_ramo' => $id_ramo])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get();
        } else if ($cont_nivel2 > 0) {
            $documentaciones = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get();
        } else if ($cont_nivel2 > 0 && $cont_nivel3 > 0) {
            $documentaciones = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre2])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get();
        } else if ($cont_nivel4 > 0) {
            $documentaciones = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre3])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get();
        } else {
            $documentaciones = 0;
        }
        if (isset($this->uuid_soli) && ($this->uuid_soli != "")) {
            $solicitudes = $this->solicitudesRepository->verSolicitudes(hex2bin(strtolower($this->uuid_soli)));
            $documentacionesgbd = $this->solicitudesDocumentosModel->where(['id_solicitud' => $solicitudes->id])->select('valor')->get();
        } else {
            $documentacionesgbd = "";
        }


        $this->assets->agregar_var_js(array(
            "provincias" => $provincias,
            "letras" => $letras,
            "aseguradoras" => $aseguradoras,
            "documentaciones" => $documentaciones != "" ? $documentaciones : "",
            "documentacionesgbd" => $documentacionesgbd != "" ? $documentacionesgbd : ""
            ));

        $this->load->view('formulario');
    }

    function guardar() {
        if ($_POST) {
            //print_r($_POST["campo"]);
            $reg = !empty($_POST['reg']) ? $_POST['reg'] : '' ;
            unset($_POST["campo"]["guardar"]);
            unset($_POST["reg"]);
            $campo = Util::set_fieldset("campo");
            $campovigencia = Util::set_fieldset("campovigencia");
            //$campoacreedores = Util::set_fieldset("campoacreedores");
            $campoprima = Util::set_fieldset("campoprima");
            $campoPlanesCoberturas = Util::set_fieldset("campoPlanesCoberturas");
            $campoparticipacion = Util::set_fieldset("campoparticipacion");
            $campodocumentacion = Util::set_fieldset("campodocumentacion");
            $Bitacora = new Flexio\Modulo\Solicitudes\Models\SolicitudesBitacora;
            Capsule::beginTransaction();
            try {
                if (empty($campo['uuid'])) {
                    //Crear en Solicitudes
                    $campo["uuid_solicitudes"] = Capsule::raw("ORDER_UUID(uuid())");
                    $clause['empresa_id'] = $this->empresa_id;
                    $total = $this->solicitudesRepository->listar($clause);
                    $year = Carbon::now()->format('y');
                    $codigo = Util::generar_codigo($_POST['codigo_ramo'] . "-" . $year, count($total) + 1);
                    $campo["numero"] = $codigo;
                    $campo["usuario_id"] = $this->session->userdata['id_usuario'];
                    $campo["empresa_id"] = $this->empresa_id;
                    $date = Carbon::now();
                    $date = $date->format('Y-m-d');
                    $campo['fecha_creacion'] = $date;
                    $solicitudes = $this->solicitudesModel->create($campo);

                    //Create Coverage and dedutibles
                    if (!empty($campoPlanesCoberturas["planesCoberturasDeducibles"])) {
                        $decodeJSON = json_decode($campoPlanesCoberturas["planesCoberturasDeducibles"], TRUE);
                        for ($i = 0; $i < count($decodeJSON['coberturas']["nombre"]); $i++) {
                            $cobertura["cobertura"] = $decodeJSON['coberturas']["nombre"][$i];
                            $cobertura["valor_cobertura"] = $decodeJSON['coberturas']["valor"][$i];
                            $cobertura["id_solicitud"] = $solicitudes->id;
                            $this->solicitudesCoberturas->create($cobertura);
                        }

                        for ($i = 0; $i < count($decodeJSON['deducibles']['nombre']); $i++) {

                            $deduccion["deduccion"] = $decodeJSON['deducibles']["nombre"][$i];
                            $deduccion["valor_deduccion"] = $decodeJSON['deducibles']["valor"][$i];
                            $deduccion["id_solicitud"] = $solicitudes->id;
                            $this->solicitudesDeduccion->create($deduccion);
                        }
                    }
                    //Crear en Solicitudes Vigencia
                    if ($campovigencia['pagadornombre'] != "") {
                        $campovigencia['pagador'] = $campovigencia['pagadornombre'];
                    } else {
                        $campovigencia['pagador'] = $campovigencia['selpagadornombre'];
                    }
                    if (isset($campovigencia['poliza_declarativa'])) {
                        if ($campovigencia['poliza_declarativa'] == "on") {
                            $campovigencia['poliza_declarativa'] = "si";
                        } else {
                            $campovigencia['poliza_declarativa'] = "no";
                        }
                    } else {
                        $campovigencia['poliza_declarativa'] = "no";
                    }

                    $desde = $campovigencia['vigencia_desde'];
                    $hasta = $campovigencia['vigencia_hasta'];
                    $campovigencia['vigencia_desde'] = date('Y-m-d', strtotime($desde));
                    $campovigencia['vigencia_hasta'] = date('Y-m-d', strtotime($hasta));
                    $campovigencia['id_solicitudes'] = $solicitudes->id;
                    $solicitudesvigencia = $this->solicitudesVigenciaModel->create($campovigencia);
                    //Crear en Solicitudes Prima
                    $primerpago = $this->input->post('fecha_primer_pago'); //$campoprima['fecha_primer_pago'];
                    $campoprima['fecha_primer_pago'] = date('Y-m-d', strtotime($primerpago));
                    $campoprima['id_solicitudes'] = $solicitudes->id;
                    $solicitudesprima = $this->solicitudesPrimaModel->create($campoprima);
                    //guardar tabla distribucion
                    $cantidad = $campoparticipacion['cantidad'];

                    $int_ase = array();
                    $int_ase['id_solicitudes'] = $solicitudes->id;
                    $int_ase['fecha_inclusion'] = $campovigencia['vigencia_desde'];
                    $det = InteresesAsegurados_detalles::where('detalle_unico', $_POST['detalleunico'])->update($int_ase);

                    //Crear Acreedores
                    $fieldsetacre = array();
                    $campoacreedores = $this->input->post('campoacreedores');
                    if($campoacreedores!=NULL){
                        $porcentaje_cesion = $this->input->post('campoacreedores_por');
                        $monto_cesion = $this->input->post('campoacreedores_mon'); 
                        $fecha_ini = $this->input->post('campoacreedores_ini'); 
                        $fecha_fin = $this->input->post('campoacreedores_fin');                    
                        foreach ($campoacreedores as $key => $value) {
                            $fieldsetacre['acreedor'] = $value;
                            $fieldsetacre["id_solicitud"] = $solicitudes->id;
                            $fieldsetacre["porcentaje_cesion"] = $porcentaje_cesion[$key];
                            $fieldsetacre["monto_cesion"] = $monto_cesion[$key];
                            $fieldsetacre["fecha_inicio"] = $fecha_ini[$key];
                            $fieldsetacre["fecha_fin"] = $fecha_fin[$key];
                            if ($value != "") {
                                SolicitudesAcreedores::create($fieldsetacre);    
                            }                                                       
                        }
                    }

                    /* $det = InteresesAsegurados_detalles::where('detalle_unico', $_POST['detalleunico'])->get()->toArray();

                      foreach ($det as $deta) {
                      unset($deta['detalle_unico']);
                      unset($deta['id']);
                      $deta['id_solicitudes'] = $solicitudes->id;

                      $num = solicitudesIntereses::where('id_solicitudes', $deta['id_solicitudes'])->where('id_intereses', $deta['id_intereses'])->count();
                      if ($num > 0) {
                      $solint = solicitudesIntereses::where('id_solicitudes', $deta['id_solicitudes'])->where('id_intereses', $deta['id_intereses'])->update($deta);
                      } else {
                      $solint = solicitudesIntereses::create($deta);
                      }
                  } */


                  $arreglo_agentes = explode(",", $campoparticipacion['id_agente']);
                  $arreglo_porcentajes = explode(",", $campoparticipacion['porcentajes']);

                    $totalparotrosagentes=0;
                    for ($i = 0; $i <= $cantidad; $i++) {
                        if ($arreglo_agentes[$i] != '') {
                            $campoparticipacion['id_solicitud'] = $solicitudes->id;
                            $campoparticipacion['agente'] = $arreglo_agentes[$i];
                            $campoparticipacion['porcentaje_participacion'] = $arreglo_porcentajes[$i];
                            $solicitudesparticipacion = $this->Participacion->create($campoparticipacion);

                            $totalparotrosagentes+=$arreglo_porcentajes[$i];
                        }
                    }
                    //guardar tabla documentacion
                    if (isset($campodocumentacion['opcion']) && $campodocumentacion['opcion'] != "") {
                        $arreglo_documentacion = explode(",", $campodocumentacion['opcion']);
                        $cantidad_doc = $campodocumentacion['cantidad_check'];

                        for ($h = 0; $h <= count($arreglo_documentacion); $h++) {
                            if (isset($arreglo_documentacion[$h]) && $arreglo_documentacion[$h] != '') {
                                $campodocumentacion['id_solicitud'] = $solicitudes->id;
                                $campodocumentacion['valor'] = $arreglo_documentacion[$h];
                                $this->solicitudesDocumentosModel->create($campodocumentacion);
                            }
                        }
                    }

//                    $campodocumentacion['id_solicitud'] = $solicitudes->id;
//                    $solicituddocumentos = $this->solicitudesDocumentosModel->create($campodocumentacion);

                    //
                    $impuestoPlanes = Planes::where(['seg_planes.id' => $solicitudes->plan_id])->join('contab_impuestos','contab_impuestos.id','=','seg_planes.id_impuesto')->select('contab_impuestos.impuesto AS impuesto')->first();
                    $sobreComision = PlanesComisiones::where(['id_planes' => $solicitudes->plan_id])->first();
                    $this->solicitudesModel->find($solicitudes->id)->update(['impuesto_sobre_comision' => $sobreComision->sobre_comision, 'impuesto' => $impuestoPlanes->impuesto ]);



                    //Subir documentos
                    if (!empty($_FILES['file'])) {
                        $id_solicitud = $solicitudes->id;
                        $modeloInstancia = $this->solicitudesModel->find($id_solicitud);
                        $this->documentos->subir($modeloInstancia);
                    }

                    
                    $fieldset["comentario"] = "Creación de solicitud<br>Estado: ".$solicitudes->estado;
                    $fieldset["comentable_type"] = "Creacion";
                    $fieldset['created_at'] = date('Y-m-d H:i:s');
                    $fieldset["comentable_id"] = $solicitudes->id;
                    $fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
                    $fieldset["empresa_id"] = $this->empresa_id;
                    
                    $interesase = $this->bitacoraModel->create($fieldset);

                    $IInteres = array();
                    $IInteres['comentable_id'] = $solicitudes->id;
                    //$IInteres['created_at'] = date('Y-m-d H:i:s');
                    $IInteres["comentable_type"] = "Creacion_interes_solicitudes";
                    $bitacora = $this->bitacoraModel->where('comentable_id', $_POST['detalleunico'])->update($IInteres);
                    
                    $agenteprincipaltotal=Agentes::where('principal',1)->
                    where('id_empresa',$this->empresa_id)->count();

                    if($agenteprincipaltotal>0)
                    {
                         $agenteprincipal=Agentes::where('id_empresa',$this->empresa_id)->where('principal',1)->first();
                         $datossegprincipal['agente_id']=$agenteprincipal->id;
                         $datossegprincipal['solicitud_id']=$solicitudes->id;
                         $datossegprincipal['created_at']=date('Y-m-d H:i:s');
                         $datossegprincipal['updated_at']=date('Y-m-d H:i:s');
                         $datossegprincipal['comision']=number_format((100-$totalparotrosagentes),2);
                         
                         $poragenteprincipal=$this->SegSolicitudesAgentePrin->create($datossegprincipal);
                     }
                 
                } else {

                    //Actualizar
                    $id_solicitud = $campo["id_solicitud"];

                    if (!empty($campoPlanesCoberturas["planesCoberturasDeducibles"])) {

                        $this->solicitudesCoberturas->where(['id_solicitud' => $id_solicitud])->delete();
                        $this->solicitudesDeduccion->where(['id_solicitud' => $id_solicitud])->delete();
                        $decodeJSON = json_decode($campoPlanesCoberturas["planesCoberturasDeducibles"], TRUE);
                        for ($i = 0; $i < count($decodeJSON['coberturas']["nombre"]); $i++) {
                            $cobertura["cobertura"] = $decodeJSON['coberturas']["nombre"][$i];
                            $cobertura["valor_cobertura"] = $decodeJSON['coberturas']["valor"][$i];
                            $cobertura["id_solicitud"] = $id_solicitud;
                            $this->solicitudesCoberturas->create($cobertura);
                        }

                        for ($i = 0; $i < count($decodeJSON['deducibles']['nombre']); $i++) {

                            $deduccion["deduccion"] = $decodeJSON['deducibles']["nombre"][$i];
                            $deduccion["valor_deduccion"] = $decodeJSON['deducibles']["valor"][$i];
                            $deduccion["id_solicitud"] = $id_solicitud;
                            $this->solicitudesDeduccion->create($deduccion);
                        }
                    }

                    if ($campovigencia["pagadornombre"] != "") {
                        if ($campovigencia["tipo_pagador"] == "asegurado") {
                            $campovigencia["pagador"] = $campovigencia["selpagadornombre"];
                        } else {
                            $campovigencia["pagador"] = $campovigencia["pagadornombre"];
                        }
                    } else {
                        $campovigencia["pagador"] = $campovigencia["selpagadornombre"];
                    }
                    if (isset($campovigencia["poliza_declarativa"])) {
                        if ($campovigencia["poliza_declarativa"] == "on") {
                            $campovigencia["poliza_declarativa"] = "si";
                        } else {
                            $campovigencia["poliza_declarativa"] = "no";
                        }
                    } else {
                        $campovigencia["poliza_declarativa"] = "no";
                    }
                    $desde = $campovigencia["vigencia_desde"];
                    $hasta = $campovigencia["vigencia_hasta"];
                    $campovigencia["vigencia_desde"] = date('Y-m-d', strtotime($desde));
                    $campovigencia["vigencia_hasta"] = date('Y-m-d', strtotime($hasta));

                    $int_ase = array();
                    $int_ase['id_solicitudes'] = $id_solicitud;
                    $int_ase['fecha_inclusion'] = $campovigencia['vigencia_desde'];
                    $det = InteresesAsegurados_detalles::where('detalle_unico', $_POST['detalleunico'])->update($int_ase);

                    unset($campovigencia["pagadornombre"]);
                    unset($campovigencia["selpagadornombre"]);
                    $solicitudesvigencia = $this->solicitudesVigenciaModel->where(['id_solicitudes' => $id_solicitud])->update($campovigencia);

                    //Crear Acreedores
                    $fieldsetacre = array();
                    $campoacreedores = $this->input->post('campoacreedores');
                    SolicitudesAcreedores::where("id_solicitud", $id_solicitud)->delete();
                    if($campoacreedores!=NULL){                        
                        $porcentaje_cesion = $this->input->post('campoacreedores_por');
                        $monto_cesion = $this->input->post('campoacreedores_mon'); 
                        $fecha_ini = $this->input->post('campoacreedores_ini'); 
                        $fecha_fin = $this->input->post('campoacreedores_fin');                    
                        foreach ($campoacreedores as $key => $value) {
                            $fieldsetacre['acreedor'] = $value;
                            $fieldsetacre["id_solicitud"] = $id_solicitud;
                            $fieldsetacre["porcentaje_cesion"] = $porcentaje_cesion[$key];
                            $fieldsetacre["monto_cesion"] = $monto_cesion[$key];
                            $fieldsetacre["fecha_inicio"] = $fecha_ini[$key];
                            $fieldsetacre["fecha_fin"] = $fecha_fin[$key];
                            if ($value != "") {
                                SolicitudesAcreedores::create($fieldsetacre);    
                            }                                                       
                        }
                    }

                    $primerpago = $this->input->post('fecha_primer_pago'); //$campoprima['fecha_primer_pago'];

                    $campoprima['fecha_primer_pago'] = date('Y-m-d', strtotime($primerpago));
                    $solicitudesprima = $this->solicitudesPrimaModel->where(['id_solicitudes' => $id_solicitud])->update($campoprima);

                    $arreglo_agentes = explode(",", $campoparticipacion['id_agente']);
                    $arreglo_porcentajes = explode(",", $campoparticipacion['porcentajes']);

                    unset($campoparticipacion['id_agente']);
                    unset($campoparticipacion['porcentajes']);
                    unset($campoparticipacion['cantidad']);
                    unset($campoparticipacion['total']);

                    /* $this->Participacion->where(['id_solicitud' => $id_solicitud])->delete(); */
                    
                    $totalparotrosagentes=0;
                    for ($i = 0; $i < count($arreglo_agentes); $i++) {

                        if ($arreglo_agentes[$i] != '') {

                            $participacion = $this->Participacion->where(['agente' => $arreglo_agentes[$i], 'id_solicitud' => $id_solicitud])->first();

                            if ($participacion != '') {

                                $campoparticipacion['porcentaje_participacion'] = $arreglo_porcentajes[$i];
                                $participacion->update($campoparticipacion);
                            } else {

                                $campoparticipacion['id_solicitud'] = $id_solicitud;
                                $campoparticipacion['agente'] = $arreglo_agentes[$i];
                                $campoparticipacion['porcentaje_participacion'] = $arreglo_porcentajes[$i];
                                $solicitudesparticipacion = $this->Participacion->create($campoparticipacion);
                            }
                        }
                        $totalparotrosagentes+=$arreglo_porcentajes[$i];
                    }
                    
                    $agenteprincipaltotal=Agentes::where('principal',1)->
                    where('id_empresa',$this->empresa_id)->count();

                    if($agenteprincipaltotal>0)
                    {
                         $comisionagenteprincipal=$this->SegSolicitudesAgentePrin->where('solicitud_id',$id_solicitud)->count();
                         
                         $agenteprincipal=Agentes::where('id_empresa',$this->empresa_id)->where('principal',1)->first();
                         
                         if($comisionagenteprincipal>0)
                         {
                             $datossegprincipal['agente_id']=$agenteprincipal->id;
                             $datossegprincipal['updated_at']=date('Y-m-d H:i:s');
                             $datossegprincipal['comision']=number_format((100-$totalparotrosagentes),2);
                             
                             $poragenteprincipal=$this->SegSolicitudesAgentePrin->where('solicitud_id',$id_solicitud)->update($datossegprincipal);
                         }
                         else
                         {
                             $datossegprincipal['agente_id']=$agenteprincipal->id;
                             $datossegprincipal['solicitud_id']=$id_solicitud;
                             $datossegprincipal['created_at']=date('Y-m-d H:i:s');
                             $datossegprincipal['updated_at']=date('Y-m-d H:i:s');
                             $datossegprincipal['comision']=number_format((100-$totalparotrosagentes),2);
                             
                             $poragenteprincipal=$this->SegSolicitudesAgentePrin->create($datossegprincipal);
                         }
                     }
                     
                    $participacion = $this->Participacion->where(['id_solicitud' => $id_solicitud])->get();
                    foreach ($participacion as $key => $value) {
                        if (!in_array($value->agente, $arreglo_agentes)) {
                            $participacion->find($value->id)->delete();
                        }
                    }
                    if (isset($campodocumentacion['opcion']) && $campodocumentacion['opcion'] != "") {
                        $this->solicitudesDocumentosModel->where(['id_solicitud' => $id_solicitud])->delete();

                        $arreglo_documentacion = explode(",", $campodocumentacion['opcion']);
                        $cantidad_doc = $campodocumentacion['cantidad_check'];

                        for ($h = 0; $h <= count($arreglo_documentacion); $h++) {
                            if ($arreglo_documentacion[$h] != '') {

//                                $documentacion = count($this->solicitudesDocumentosModel->where('id_solicitud', "=", $id_solicitud)->where('valor', "=", "" . $arreglo_documentacion[$h] . "")->get()->toArray());
//                                if ($documentacion == 0) {
                                $campodocumentacion['id_solicitud'] = $id_solicitud;
                                $campodocumentacion['valor'] = "" . $arreglo_documentacion[$h] . "";
                                $this->solicitudesDocumentosModel->create($campodocumentacion);
//                                }
                            }
                        }
                    }

                    $solicitudes2 = $this->solicitudesModel->where(['id' => $id_solicitud])->first();
                    $codigo = $solicitudes2->numero;

                    unset($campo['uuid']);
                    unset($campo['id_solicitud']);
                    $solicitudes = $this->solicitudesModel->where(['id' => $id_solicitud])->update($campo);

                    $now = Carbon::now();
                    $datosSolicitud = $this->solicitudesModel->where(['id' => $id_solicitud])->first();
                    if ($campo['estado'] == 'Aprobada' || $campo['estado'] == 'Anulada' || $campo['estado'] == 'Rechazada') {
                        $datos['dias_transcurridos'] = ($datosSolicitud->created_at->diff($now)->days < 1) ? '1' : $datosSolicitud->created_at->diff($now)->days;
                        $this->solicitudesModel->where(['id' => $id_solicitud])->update($datos);
                    }
                }
                Capsule::commit();
            } catch (ValidationException $e) {
                log_message('error', $e);
                Capsule::rollback();
            }
            
            if (!is_null($solicitudes) || (!is_null($solicitudesvigencia) || !is_null($solicitudesprima) || !is_null($solicitudesparticipacion) || !is_null($solicitudes) )) {

                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Solicitud ' . $codigo . '');
            } else {
                $mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            }
        } else {
            $mensaje = array('class' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
        }
        
        if(empty($campo['uuid']))
        {
            $soli=SolicitudesModel::find($solicitudes->id);
            //$fieldsetupdate["created_at"]=date('Y-m-d H:i:s');
            $fieldsetupdate["comentario"] = "Creación de solicitud<br>Estado: ".$soli->estado;
            $interesase = $this->bitacoraModel->where('comentable_type','Creacion')
            ->where('comentable_id',$soli->id)->update($fieldsetupdate);
        }
        
        $this->session->set_flashdata('mensaje', $mensaje);
        if (!empty($url) && $reg == "age" ) 
            redirect(base_url('agentes/ver/'.$_POST['val']));
        else
            redirect(base_url('solicitudes/listar'));   
        
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
        if ($this->auth->has_permission('acceso', 'solicitudes/editar participación') == true) {
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

        if (isset($_POST['solicitud_id'])) {
            $id_ramo_sol = $_POST['solicitud_id'];
        } else {
            $id_ramo_sol = -1;
        }

        $cont_nivel1 = count($this->RamosDocumentos->where(['id_ramo' => $id_ramo_sol])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
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
            ->where('modulo', "!=", "reclamo")
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
            ->where('modulo', "!=", "reclamo")
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
            ->where('modulo', "!=", "reclamo")
            ->get());

        if ($cont_nivel1 > 0) {
            $documentacion = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_sol])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get();
        } else if ($cont_nivel2 > 0) {
            $documentacion = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get();
        } else if ($cont_nivel3 > 0) {
            $documentacion = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre2])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get();
        } else if ($cont_nivel4 > 0) {
            $documentacion = $this->RamosDocumentos->where(['id_ramo' => $id_ramo_padre3])
            ->where('estado', "=", "Activo")
            ->where('modulo', "!=", "reclamo")
            ->get();
        } else {
            $documentacion = array();
        }

        $data["campos"] = array(
            "campos" => array(
                "tipos_intereses_asegurados" => $this->InteresesAsegurados_catModel->get(),
                "politicas" => $this->politicas,
                "politicas_general" => $this->politicas_general,
                "participacion" => $editarParticipacion,
                "documentacion" => $documentacion
                ),
            );
        $this->load->view('formularioIntereses', $data);
    }

    function formulariovigencia($data = array()) {
        /* $clause = array('empresa_id' => $this->empresa_id);        
          $this->assets->agregar_var_js(array(
          ));
         */
          $this->load->view('formulariovigencia', $data);
      }

      function formularioprima($data = array()) {
        /* $clause = array('empresa_id' => $this->empresa_id);        
          $this->assets->agregar_var_js(array(
          ));
         */
          $this->load->view('formularioprima', $data);
      }

      function formularioparticipacion($data = array()) {

        $this->load->view('formularioparticipacion', $data);
    }

    function formulariodocumentos($data = array()) {

        $this->load->view('formulariodocumentos', $data);
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
        $csvdata = array();

        $clause['id'] = $id;

        $contactos = $this->solicitudesRepository->listar_solicitudes($clause, NULL, NULL, NULL, NULL);
        if (empty($contactos)) {
            return false;
        }
        $i = 0;
        $now = Carbon::now();
        foreach ($contactos AS $row) {
            $csvdata[$i]['numero'] = $row->numero;
            $csvdata[$i]["cliente_id"] = utf8_decode(Util::verificar_valor($row->cliente->nombre));
            $csvdata[$i]["aseguradora_id"] = utf8_decode(Util::verificar_valor($row->aseguradora->nombre));
            $csvdata[$i]["ramo"] = utf8_decode(Util::verificar_valor($row->ramo));
            $csvdata[$i]["id_tipo_poliza"] = utf8_decode(Util::verificar_valor($row->tipo->nombre));
            //$csvdata[$i]["Dias_transcurridos"] = utf8_decode(($row->created_at->diff($now)->days < 1) ? '1' : $row->created_at->diffForHumans($now));
            $csvdata[$i]["Dias_transcurridos"] = utf8_decode(($row->created_at->diff($now)->days < 1) ? '1' : $row->created_at->diff($now)->days);
            $csvdata[$i]["created_at"] = utf8_decode(Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d/m/Y'));
            $csvdata[$i]["usuario_id"] = utf8_decode(Util::verificar_valor($row->usuario->nombre . " " . $row->usuario->apellido));
            $csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->estado));
            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $headers = [
        'No.  Solicitud',
        'Cliente',
        'Aseguradora',
        'Ramo',
        'Tipo',
        'Días transcurridos',
        'Fecha creación',
        'usuario',
        'Estado',
        ];
        $decodingHeaders = array_map("utf8_decode", $headers);
        $csv->insertOne($decodingHeaders);
        $csv->insertAll($csvdata);
        $csv->output("Solicitudes-" . date('y-m-d') . ".csv");
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

    public function tablatabsolicitudes($data = array()) {
        //If ajax request

        $this->assets->agregar_js(array(
            'public/assets/js/modules/solicitudes/tablatab.js',
            'public/assets/js/modules/solicitudes/routes.js'
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

        if ($this->input->post('id_solicitud', true)) {

            $id_solicitud = $this->input->post('id_solicitud', true);
            $modeloInstancia = $this->solicitudesModel->find($id_solicitud);
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

        redirect(base_url('solicitudes/editar/' . bin2hex($modeloInstancia->uuid_solicitudes) . '#divplan'));
    }

    public function formularioModalEditar($data = NULL) {

        $this->assets->agregar_var_js(array(
            "numero" => "",
            'data' => "",
            ));

        $this->load->view('formularioModalDocumentoEditar');
    }

    public function imprimirSolicitud($id_solicitud = null) {

        if ($id_solicitud == null) {
            return false;
        }

        $solicitudes = $this->solicitudesModel->where(['id' => $id_solicitud])->first();
        $clientes = $this->clienteModel->where(['cli_clientes.id' => $solicitudes->cliente_id])
        ->join('cli_centros_facturacion', 'cli_centros_facturacion.cliente_id', '=', 'cli_clientes.id')
        ->join('cli_clientes_correos', 'cli_clientes_correos.cliente_id', '=', 'cli_clientes.id')
        ->join('cli_clientes_telefonos', 'cli_clientes_telefonos.cliente_id', '=', 'cli_clientes.id')
        ->first();


        $group = $this->clienteModel->where(['cli_clientes.id' => $solicitudes->cliente_id])->where('grp_grupo_clientes.deleted_at', '=', NULL)
        ->join('grp_grupo_clientes', 'grp_grupo_clientes.uuid_cliente', '=', 'cli_clientes.uuid_cliente')
        ->join('grp_grupo', 'grp_grupo.id', '=', 'grp_grupo_clientes.grupo_id')
        ->select('grp_grupo.nombre')
        ->first();
        if ($group != NULL) {
            $group = $group->nombre;
        } else {
            $group = false;
        }

        $provincias = $this->SegCatalogoRepository->listar_catalogo('provincias', 'orden');
        $centro_facturacion = $this->centroModel->where(['id' => $solicitudes->prima->centro_facturacion])->first();
        $i = 0;
        foreach ($solicitudes->participacion as $key => $value) {
            $agentes[$i] = Agentes::where(['id' => $value->agente])->first();
            $i++;
        }

        $nombre = $solicitudes->numero;
        $formulario = "formularioSolicitud";

        $data = ['datos' => $solicitudes, 'cliente' => $clientes, 'provincias' => $provincias, 'facturacion' => $centro_facturacion, 'agentes' => $agentes, 'grupo' => $group];
        $dompdf = new Dompdf();
        $html = $this->load->view('pdf/' . $formulario, $data, true);
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
            'public/assets/js/modules/solicitudes/formulario_comentario.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/modules/solicitudes/routes.js',
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

    function saveIndividualCoverage(){

        $deductibles = $this->input->post("deductibles");
        $coverage    = $this->input->post("coverage");
        $unicDetail  = $this->input->post("unicDetail");
        $interesId   = $this->input->post("interesId");
        $solicitud   = $this->input->post("solicitud");
        $msg = "";
        $clause["detalle_unico"] = $unicDetail;
        $clause["id_interes"] = $interesId;
        try {
         if(count($deductibles)){
            if(isset($solicitud)&& is_numeric($solicitud)){
                $clause['id_solicitud'] = $solicitud;
                unset($clause["detalle_unico"]);
            }
            IndDeductible::where($clause)
            ->delete();
            for ($i=0; $i<count($deductibles["deducibles"]['nombre']);$i++) {

                $value = $deductibles["deducibles"];
                $indDeductible = new IndDeductible();
                $indDeductible->detalle_unico = $unicDetail;
                $indDeductible->nombre = $value['nombre'][$i];
                $indDeductible->deducible_monetario  = $value['valor'][$i];
                $indDeductible->id_interes = $interesId;
                if(isset($solicitud)&& is_numeric($solicitud)){
                    $indDeductible->id_solicitud = $solicitud; 
                }
                $indDeductible->save();
            }
        }

        if(count($coverage)){
            IndCoverage::where($clause)
            ->delete();
            for ($i=0;$i<count($coverage["coberturas"]['nombre']);$i++) {
                $value = $coverage["coberturas"];
                $indCoverage= new IndCoverage();
                $indCoverage->detalle_unico = $unicDetail;
                $indCoverage->nombre = $value['nombre'][$i];
                $indCoverage->cobertura_monetario = $value['valor'][$i];
                $indCoverage->id_interes=$interesId;
                if(isset($solicitud)&& is_numeric($solicitud)){
                    $indCoverage->id_solicitud = $solicitud; 
                }
                $indCoverage->save();
            } 
        } 
        $msg = "success";
    } catch (Exception $e) {
        $msg = $e->getMessage();
    }

    print $msg;
    exit;
}

function ajax_get_invidualCoverage() {

    $clause['detalle_unico'] = $this->input->post('detalle_unico');
    $clause['id_interes'] = $this->input->post("id_interes");
    $clause2["id_planes"] = $this->input->post("planId");
    $solicitud = $this->input->post("solicitud");
    if(isset($solicitud)&& is_numeric($solicitud)){
        $clause['id_solicitud'] = $solicitud;
        unset($clause['detalle_unico']);
    }
    $coberturas = IndCoverage::where($clause)
    ->orderBy("created_at",'asc')
    ->get()->toArray();
    $deducion = IndDeductible::where($clause)
    ->orderBy("created_at",'asc')
    ->get()
    ->toArray();
    if(!count($coberturas) &&!count($deducion)){
      $coberturas = $this->coberturaModel->where($clause2)->get()->toArray();
      $deducion = $this->deduciblesModel->where($clause2)->get()->toArray();  
  }


  $response = new stdClass();
  $response->coberturas = $coberturas;
  $response->deducion = $deducion;
  $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
  ->set_output(json_encode($response))->_display();
  exit;
}

}
