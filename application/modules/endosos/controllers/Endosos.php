<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
use Carbon\Carbon;
use Dompdf\Dompdf;
use League\Csv\Writer as Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\Ramos\Models\RamosUsuarios;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo;
use Flexio\Modulo\Endosos\Models\Endoso;
use Flexio\Modulo\Endosos\Models\EndososBitacora;
use Flexio\Modulo\Endosos\Models\EndosoCliente;
use Flexio\Modulo\Endosos\Models\EndosoVigencia;
use Flexio\Modulo\Endosos\Models\EndosoPrima;
use Flexio\Modulo\Endosos\Models\EndosoPoliza;
use Flexio\Modulo\Endosos\Models\EndosoParticipacion;
use Flexio\Modulo\Endosos\Models\EndosoCoberturas;
use Flexio\Modulo\Endosos\Models\EndosoDeducciones;
use Flexio\Modulo\Endosos\Models\EndosoAgentePrin;
use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Modulo\Polizas\Models\PolizasPrima;
use Flexio\Modulo\Polizas\Models\PolizasVigencia;
use Flexio\Modulo\Polizas\Models\PolizasCobertura;
use Flexio\Modulo\Polizas\Models\PolizasDeduccion;
use Flexio\Modulo\Polizas\Models\PolizasParticipacion;
use Flexio\Modulo\Polizas\Models\PolizasCliente;
use Flexio\Modulo\Polizas\Models\PolizasAcreedores;
use Flexio\Modulo\Polizas\Models\SegPolizasAgentePrin;
use Flexio\Modulo\Polizas\Models\PolizasBitacora;
use Flexio\Modulo\Planes\Models\Planes;
use Flexio\Modulo\Agentes\Models\Agentes as Agente;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\SegCatalogo\Repository\SegCatalogoRepository;
use Flexio\Modulo\Ramos\Repository\RamoRepository as RamoRepository;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable as centroModel;
use Flexio\Modulo\Documentos\Repository\DocumentosRepository as DocumentosRepository;
use Flexio\Modulo\SegInteresesAsegurados\Repository\SegInteresesAseguradosRepository as SegInteresesAseguradosRepository;



class Endosos extends CRM_Controller
{

    private $empresa_id;
    private $usuario_id;
    protected $ramoRepository;
    protected $SegCatalogoRepository;
    protected $DocumentosRepository;
     protected $SegInteresesAseguradosRepository;


    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        //$this->load->model('remesas/Remesas_orm');

        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $uuid_usuario = $this->session->userdata("huuid_usuario");
        $usuario = Usuarios::findByUuid($uuid_usuario);
        $this->usuario_id = $usuario->id;
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
        $this->ramoRepository = new RamoRepository();
        $this->SegCatalogoRepository = new SegCatalogoRepository();
        $this->DocumentosRepository = new DocumentosRepository();
        $this->load->module(array('documentos'));
        $this->SegInteresesAseguradosRepository = new SegInteresesAseguradosRepository();

    }

    public function ocultotabla(){
        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/endosos/tabla.js',
        ));

        $this->load->view('tabla');
    }


    public function listar(){

        if (is_null($this->session->flashdata('mensaje')) ) {
           $mensaje = []; 
        } else {
            $mensaje = $this->session->flashdata('mensaje');
        }

        $clause = array('empresa_id' => $this->empresa_id);

        $data = array();
        $data['mensaje'] = $mensaje;
        
        $data['aseguradoras'] = Aseguradoras::where(['empresa_id' => $this->empresa_id, 'estado' => 'Activo'])->get();
        $data['clientes'] = Cliente::where(['empresa_id' => $this->empresa_id, 'estado' => 'activo'])->get();
        $data['motivo_endoso'] = SegCatalogo::where('tipo', '=' ,'endoso_motivo_regular')->orwhere('tipo', '=' ,'endoso_motivo_cancelacion')->get();
        $data['menu_crear'] = $this->ramoRepository->listar_cuentas($clause);
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

        $this->_css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/endosos/plugins.js',
            'public/assets/js/modules/endosos/funciones.js'
        ));

        $this->assets->agregar_var_js(array(
            "vista" => 'listar',
            "flexio_mensaje" => collect($mensaje),
            "id_empresa" => $this->empresa_id,
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Endosos',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Endosos</b>', "activo" => true)
                ),
            "filtro" => false,
            "menu" => array()
            );

        $breadcrumb["menu"] = array(
            "url" => 'endosos/crear',
            "clase" => 'modalOpcionesCrear',
            "nombre" => "Crear"
            );
        
        $menuOpciones["#cambiarEstadosEndososLnk"]= "Cambiar estado";
        $menuOpciones["#exportarEndososBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Listado de Endosos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

    public function crear($id_poliza = NULL,$declarativa = NULL){

        $clause = array('empresa_id' => $this->empresa_id);

        $data = array();
        $data['subpanels'] = [];
        $data['data'] = array('valor_descripcion' => '');
        $clientes = Cliente::where(['empresa_id' => $this->empresa_id, 'estado' => 'activo'])
        ->orderBy('nombre','asc')
        ->select('id','nombre', 'identificacion','tipo_identificacion','detalle_identificacion')
        ->get();
        foreach ($clientes  as $key => $value) {
            if($value['tipo_identificacion'] == 'pasaporte' && $value['identificacion'] == ''){
                $clientes[$key]["identificacion"] = $value['detalle_identificacion']['pasaporte'];
            }elseif($value['tipo_identificacion'] == '' && $value['identificacion'] == ''){
                $clientes[$key]["identificacion"] = 'null';
            }
        }
        $motivos_endosos = $this->SegCatalogoRepository->listar_catalogo('endoso_motivo_regular','valor');
        $estadosEndosos = $this->SegCatalogoRepository->listar_catalogo('estado_endoso','orden');
        $polizas = Polizas::where(['empresa_id' => $this->empresa_id])->where('estado','<>','Expirada')->orderBy('numero','asc')->get(array('id','numero'));
        $datosRamos = $this->ramoRepository->listar_cuentas($clause);
        $ramosRoles = RolesUsuario::with(array('ramos'))->where(['usuario_id' => $this->usuario_id, 'empresa_id' => $this->empresa_id])->get();
        $ramosUsuario = RamosUsuarios::where(['id_usuario' => $this->usuario_id])->get();
        $rolesArray = array();
        $usuariosArray = array();
        $ramos = array();
        $i = 0;
        foreach ($ramosRoles AS $value) {
            foreach ($value->ramos AS $valuee) {
                $rolesArray[$i] = $valuee->id_ramo;
                $i++;
            }
        }
        $i = 0;
        foreach ($ramosUsuario AS $value) {
            $usuariosArray[$i] = $value['id_ramo'];
            $i++;
        }

        if(!empty($datosRamos)){
            $cont = 0;
            foreach ($datosRamos as $key => $value) {
                foreach ($datosRamos AS $menu) {
                    if ($value['id'] == $menu['padre_id']) {
                        $cont++;
                    }
                }
                if($cont == 0 && $value['padre_id'] != 0 && $value['estado'] == 1 && in_array($value['id'], $rolesArray) && in_array($value['id'], $usuariosArray) ){
                    array_push($ramos, array('id' => $value['id'], 'nombre' => $value['nombre']));
                }
                $cont = 0;
            }
        }
        if(!is_null($id_poliza) && !is_null($declarativa)){
            $tipo_endoso = "Regular";
            $id_motivo = SegCatalogo::where(['etiqueta' => 'Declaracion'])->select('id')->first();
            $id_motivo = $id_motivo->id; 
            $id_poliza = $id_poliza;
            $datosPolizas = Polizas::where(['id' => $id_poliza])->select('cliente','ramo_id')->first();
            $id_cliente = $datosPolizas->cliente;
            $id_ramo = $datosPolizas->ramo_id;

        }elseif(!is_null($id_poliza) && is_null($declarativa)){

            $tipo_endoso = "";
            $id_motivo = "";
            $id_poliza = $id_poliza;
            $datosPolizas = Polizas::where(['id' => $id_poliza])->select('cliente','ramo_id')->first();
            $id_cliente = $datosPolizas->cliente;
            $id_ramo = $datosPolizas->ramo_id;

        }else{
            $tipo_endoso = '';
            $id_motivo = '';
            $id_poliza = '';
            $id_cliente = '';
            $id_ramo = '';
        }

        $this->assets->agregar_var_js(array(
            'vista' => 'crear',
            'desde' => 'endosos',
            'poliza' => $polizas,
            'motivos_endosos' => $motivos_endosos,  
            'estadosEndosos' => $estadosEndosos,
            'tipo_endoso' => $tipo_endoso,
            'id_motivo' => $id_motivo,
            'id_poliza' => $id_poliza,
            'id_cliente' => $id_cliente,
            'id_ramo' => $id_ramo,
            'clientes' => $clientes,
            'ramos' => json_encode($ramos),



            "estado_solicitud" => '',
            "estado_pol" => '',
            "cliente" => '',
            "aseguradora" => '',
            "plan" => '',
            "coberturas" => '',
            "deducciones" => '',
            "comision" => '',
            "vigencia" => '',
            "prima" => '',
            "participacion" => '',
            "totalParticipacion" => '',
            "centroFacturacion" => '',
            "agtPrincipal" => '',
            "agtPrincipalporcentaje" => '',
            "id_centroContable" => '',
            "nombre_centroContable" => '',
            "cantidadPagos" => '',
            "frecuenciaPagos" => '',
            "sitioPago" => '',
            "metodoPago" => '',
            "centrosFacturacion" => '',
            "id_tipo_int_asegurado" => '',
            "tipo_ramo" => '',
            "ramo" => '',
            "nombre_ramo" => '', 
            "centrosContables" => '',
            "grupo" => '',
            "acreedores" => '',
            "categoria_poliza" => '',
            "id_tipo_poliza" => '',
            "validavida" => '',
            "poliza_declarativa" => '',
            "permiso_editar" => '',
            "pagador" => '' ,


        ));

        $this->assets->agregar_js(array(
            'public/assets/js/modules/endosos/crear.vue.js',
            'public/assets/js/modules/endosos/funciones.js',
            'public/assets/js/modules/polizas/crear.vue.js',
        ));

        $this->_css();
        $this->_js();
        $this->tablasIntereses();

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Endosos Crear',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => 'Endosos', "url" => "endosos/listar", "activo" => true),
                2 => array("nombre" => '<b>Crear</b>', "activo" => true)
                ),
            "filtro" => false,
            "menu" => array()
            );

        $breadcrumb["menu"] = array(
            "url" => '#',
            "clase" => 'modalOpcionesAccion',
            "nombre" => "Acción"
        );
        
        //$menuOpciones["#cambiarEstadosEndososLnk"]= "Cambiar estado";
        $menuOpciones["#exportarEndososBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Crear de Endosos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
        
    }

    public function editar($uuid = null){

        $this->_css();
        $this->_js();

        $datosEndosos = Endoso::findByUuid($uuid);
        $numero_endoso = $datosEndosos->endoso;

        $clause = array('empresa_id' => $this->empresa_id);
        $data = array();
        $data['subpanels'] = [];
        $data['id_ramo_endoso'] = $datosEndosos->id_ramo;
        $data['id_cliente_endoso'] = $datosEndosos->cliente_id;
        $data['data'] = array('valor_descripcion' => $datosEndosos->descripcion);
        $data['campos'] = array('uuid_endoso' => $uuid);
        $dataPoliza = Polizas::where(['id' => $datosEndosos->id_poliza])->first();
        $data['uuid_poliza'] = bin2hex($dataPoliza->uuid_polizas);

        if($datosEndosos->tipo == "Cancelación"){
            $tipo_motivo = 'endoso_motivo_cancelacion';
        }else{
            $tipo_motivo = 'endoso_motivo_regular';
        }

        $motivos_endosos = $this->SegCatalogoRepository->listar_catalogo($tipo_motivo,'valor');
        $estadosEndosos = $this->SegCatalogoRepository->listar_catalogo('estado_endoso','orden');

        $data['clientes'] = Cliente::where(['empresa_id' => $this->empresa_id, 'estado' => 'activo'])
        ->orderBy('nombre','asc')
        ->select('id','nombre', 'identificacion','tipo_identificacion','detalle_identificacion')
        ->get();
        foreach ($data['clientes']  as $key => $value) {
            if($value['tipo_identificacion'] == 'pasaporte' && $value['identificacion'] == ''){
                $data['clientes'][$key]["identificacion"] = $value['detalle_identificacion']['pasaporte'];
            }elseif($value['tipo_identificacion'] == '' && $value['identificacion'] == ''){
                $data['clientes'][$key]["identificacion"] = 'null';
            }
        }

        $polizas = Polizas::where(['empresa_id' => $this->empresa_id])->where('estado','<>','Expirada')->orderBy('numero','asc')->get(array('id','numero'));
        $data['menu_crear'] = $this->ramoRepository->listar_cuentas($clause);
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



        $estado = $this->SegCatalogoRepository->listar_catalogo('estado_p', 'orden');
        $estado = $estado->whereIn('key', array('polizas_pf', 'polizas_f'));
        $estado_pol = $dataPoliza->estado;
        $cliente = PolizasCliente::where(['id_poliza' => $dataPoliza->id])->first();
        if(count($cliente) == 0){
            $cliente = '';
        }
        $aseguradora = Aseguradoras::where(['id' => $dataPoliza->aseguradora_id])->get(array('id', 'nombre'));
        if (count($aseguradora) == 0) {
            $aseguradora = '';
        }
        $plan = Planes::where(['id' => $dataPoliza->plan_id])->get(array('nombre'));
        $coberturas = PolizasCobertura::where(['id_poliza' => $dataPoliza->id])->get();
        $deducciones = PolizasDeduccion::where(['id_poliza' => $dataPoliza->id])->get();
        $comision = $dataPoliza->comision;
        $vigencia = PolizasVigencia::where(['id_poliza' => $dataPoliza->id])->first();
        if(count($vigencia) == 0){
            $vigencia = '';
        }
        $prima = PolizasPrima::where(['id_poliza' => $dataPoliza->id])->first();
        $centroFacturacion = centroModel::where(['id' => $prima->centro_facturacion])->first();
        if ($centroFacturacion == '') {
            $centroFacturacion = '';
        }
        $participacion = PolizasParticipacion::where(['id_poliza' => $dataPoliza->id])->get();
        $totalParticipacion = PolizasParticipacion::where(['id_poliza' => $dataPoliza->id])->sum('porcentaje_participacion');
        if ($totalParticipacion == '') {
            $totalParticipacion = '';
        }

        $agenteprincipaltotal=Agente::where('principal',1)->where('id_empresa',$this->empresa_id)->count();
        if($agenteprincipaltotal>0){

            $agenteprincipal=Agente::where('id_empresa',$this->empresa_id)->where('principal',1)->first();
            $agenteprincipalnombre=$agenteprincipal->nombre;
            $totalparticipacion=PolizasParticipacion::where('id_poliza',$dataPoliza->id)->sum('porcentaje_participacion');
            $agtPrincipalporcentaje=number_format((100-$totalparticipacion),2);
        }else{

            $agenteprincipalnombre="";
            $agtPrincipalporcentaje=0;
        }

        if($dataPoliza->centros != null){
            $id_centroContable = $dataPoliza->centros->id;
            $nombre_centroContable = $dataPoliza->centros->nombre;
        }else{
            $id_centroContable = 0;
            $nombre_centroContable = '';
        }
        $cantidad_pagos =    $this->SegInteresesAseguradosRepository->listar_catalogo('cantidad_pagos', 'orden');
        $frecuencia_pagos = $this->SegInteresesAseguradosRepository->listar_catalogo('frecuencia_pagos', 'orden');
        $metodo_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('metodo_pago', 'orden');
        $sitio_pago =$this->SegInteresesAseguradosRepository->listar_catalogo('sitio_pago', 'orden');
        $centrosFacturacion = centroModel:: where("cliente_id",$dataPoliza->cliente)->where("empresa_id",$this->empresa_id)->get();
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
        $group = cliente::join('grp_grupo_clientes', 'grp_grupo_clientes.uuid_cliente', '=', 'cli_clientes.uuid_cliente')
        ->join('grp_grupo', 'grp_grupo.id', '=', 'grp_grupo_clientes.grupo_id')
        ->where(['cli_clientes.empresa_id' =>  $this->empresa_id, 'cli_clientes.id' => $datosEndosos->id_poliza])
        ->where('grp_grupo_clientes.deleted_at', '=', NULL)
        ->select('grp_grupo.nombre')
        ->get();
        $acreedores = PolizasAcreedores::where("id_poliza", $datosEndosos->id_poliza)->get();
        if (count($acreedores) == 0) {
            $acreedores = 'undefined';
        }
        

        $solicitudes_titulo = Ramos::find($dataPoliza->ramo_id);
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

        $catego = "nueva";


        $this->assets->agregar_var_js(array(
            'vista' => 'editar',
            'desde' => 'endosos',
            'poliza' => $polizas,
            'estadosEndosos' => $estadosEndosos,
            'motivos_endosos' => $motivos_endosos,  
            'id_poliza' => $datosEndosos->id_poliza,
            'tipo_endoso' => $datosEndosos->tipo,
            'id_motivo' => $datosEndosos->motivo,
            'modifica_prima' => $datosEndosos->modifica_prima,
            'fecha_efectividad' => $datosEndosos->fecha_efectividad !=  "0000-00-00" ? date('d-m-Y',strtotime($datosEndosos->fecha_efectividad)) : $datosEndosos->fecha_efectividad,
            'estado_endoso' => $datosEndosos->estado,
            'endoso_id' => $datosEndosos->id,
            'uuid_endoso' => bin2hex($datosEndosos->uuid_endoso),

        

            "estado_solicitud" => $estado,
            "estado_pol" => $estado_pol,
            "cliente" => $cliente,
            "aseguradora" => $aseguradora,
            "plan" => $plan,
            "coberturas" => $coberturas,
            "deducciones" => $deducciones,
            "comision" => $comision,
            "vigencia" => $vigencia,
            "prima" => $prima,
            "participacion" => $participacion,
            "totalParticipacion" => $totalParticipacion,
            "centroFacturacion" => $centroFacturacion,
            "agtPrincipal"=>$agenteprincipalnombre,
            "agtPrincipalporcentaje"=>$agtPrincipalporcentaje,
            "id_centroContable" => $id_centroContable,
            "nombre_centroContable" => $nombre_centroContable,
            "cantidadPagos" => $cantidad_pagos,
            "frecuenciaPagos" => $frecuencia_pagos,
            "sitioPago" => $sitio_pago,
            "metodoPago" => $metodo_pago,
            "centrosFacturacion" =>$centrosFacturacion,
            "id_tipo_int_asegurado" => $dataPoliza->id_tipo_int_asegurado,
            "tipo_ramo" => $dataPoliza->tipo_ramo,
            "ramo" => $dataPoliza->ramo,
            "nombre_ramo" => $dataPoliza->ramo, 
            "centrosContables" => $centroContable,
            "grupo" => $group,
            "acreedores" => $acreedores,
            "categoria_poliza" => $catego,
            "id_tipo_poliza" => $dataPoliza->tipo_ramo == "colectivo" ? 2 : 1,
            "validavida" => $validavida,
            "poliza_declarativa" => $dataPoliza->poliza_declarativa,
            "permiso_editar" => 1,

        ));

        $this->assets->agregar_js(array(
            'public/assets/js/modules/endosos/crear.vue.js',
            'public/assets/js/modules/endosos/funciones.js',
            'public/assets/js/modules/endosos/plugins.js',
            'public/assets/js/modules/endosos/formulario_comentario.js',
            'public/assets/js/modules/polizas/crear.vue.js',
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Endosos N°'.$numero_endoso,
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => 'Endosos', "url" => "endosos/listar", "activo" => true),
                    2 => array("nombre" => '<b>'.$numero_endoso.'</b>', "activo" => true)
                ),
            "filtro" => false,
            "menu" => array()
            );

        $breadcrumb["menu"] = array(
            "url" => '#',
            "clase" => 'modalOpcionesAccion',
            "nombre" => "Acción"
        );
        
        $menuOpciones["#imprimirEndososBtn"] = "Imprimir";
        //$menuOpciones["#exportarEndososBtn"] = "Exportar";
        $menuOpciones["#documentosEndososBtn"] = "Cargar documentos";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Editar Endosos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

        

    }

    public function ajax_get_polizas(){

        $clause['empresa_id'] = $this->empresa_id;
        $ramo_id = $this->input->post('id_ramo');
        $cliente = $this->input->post('id_cliente');
        $id_poliza = $this->input->post('id_poliza');

        $response = new stdClass();
        $response->polizas = array();
        $response->clientes = array();
        $response->ramos = array();
        $ramos = '';

        if($ramo_id != ''){
            $clause['ramo_id'] = $ramo_id;
        }
        if($cliente != ''){
            $clause['cliente'] = $cliente;
        }

        if($id_poliza != '' && $ramo_id == '' && $cliente == ''){
            $polizas = Polizas::where(['id' => $id_poliza])->get(array('id','numero','cliente','ramo_id'));
        }else{
            $polizas = Polizas::where($clause)->where('estado','<>','Expirada')->orderBy('numero','asc')->get(array('id','numero','cliente','ramo_id')); 
        }

        if(count($polizas) > 0  && $ramo_id != '' && $cliente == '' && $id_poliza == ''){

            foreach ($polizas  as $key => $value) {
                $id_cliente[$key] = $value['cliente'];
            }
            $clientes = Cliente::whereIn('id', $id_cliente)->where(['estado' => 'activo'])->orderBy('nombre','asc')->select('id','nombre', 'identificacion','tipo_identificacion','detalle_identificacion')->get(); 

        }elseif(count($polizas) > 0  && $ramo_id == '' && $cliente == '' && $id_poliza == ''){

            $clientes = Cliente::where(['empresa_id' => $this->empresa_id,'estado' => 'activo'])->orderBy('nombre','asc')->select('id','nombre', 'identificacion','tipo_identificacion','detalle_identificacion')->get();

        }

        if($cliente != '' && $ramo_id == '' && $id_poliza == ''){
            if(count($polizas) > 0){
                foreach ($polizas  as $key => $value) {
                    $id_ramos[$key] = $value['ramo_id'];
                }
                $ramos = Ramos::whereIn('id',$id_ramos)->get(array('id','nombre'));
            }
        }elseif($cliente == '' && $ramo_id == '' && $id_poliza == ''){
            $clause2['empresa_id'] = $this->empresa_id;
            $datosRamos = $this->ramoRepository->listar_cuentas($clause2);
            $ramosRoles = RolesUsuario::with(array('ramos'))->where(['usuario_id' => $this->usuario_id, 'empresa_id' => $this->empresa_id])->get();
            $ramosUsuario = RamosUsuarios::where(['id_usuario' => $this->usuario_id])->get();
            $rolesArray = array();
            $usuariosArray = array();
            $ramos = array();
            $i = 0;
            foreach ($ramosRoles AS $value) {
                foreach ($value->ramos AS $valuee) {
                    $rolesArray[$i] = $valuee->id_ramo;
                    $i++;
                }
            }
            $i = 0;
            foreach ($ramosUsuario AS $value) {
                $usuariosArray[$i] = $value['id_ramo'];
                $i++;
            }
            if(!empty($datosRamos)){
                $cont = 0;
                foreach ($datosRamos as $key => $value) {
                    foreach ($datosRamos AS $menu) {
                        if ($value['id'] == $menu['padre_id']) {
                            $cont++;
                        }
                    }
                    if($cont == 0 && $value['padre_id'] != 0 && $value['estado'] == 1 && in_array($value['id'], $rolesArray) && in_array($value['id'], $usuariosArray) ){
                        array_push($ramos, array('id' => $value['id'], 'nombre' => $value['nombre']));
                    }
                    $cont = 0;
                }
            }

        }
        if($cliente == '' && $ramo_id == '' && $id_poliza != ''){

            $clientes = Cliente::where(['id' => $polizas[0]['cliente']])->orderBy('nombre','asc')->select('id','nombre', 'identificacion','tipo_identificacion','detalle_identificacion')->get();
            $ramos = Ramos::where(['id' => $polizas[0]['ramo_id']])->get(array('id','nombre'));
        }   

        if(isset($clientes) && count($clientes) > 0){
            foreach ($clientes  as $key => $value) {
                if($value['tipo_identificacion'] == 'pasaporte' && $value['identificacion'] == ''){
                    $clientes[$key]["identificacion"] = $value['detalle_identificacion']['pasaporte'];
                }elseif($value['tipo_identificacion'] == '' && $value['identificacion'] == ''){
                    $clientes[$key]["identificacion"] = 'null';
                }
            }
        }else{
            $clientes = '';
        }

        $response->ramos = $ramos;
        $response->clientes = $clientes;
        $response->polizas = $polizas;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_get_prima(){

        $id_motivo = $this->input->post('id_motivo');

        $motivos = SegCatalogo::where(['id' => $id_motivo ])->first();
        if($motivos->key == "modifica_prima_si"){
            $modifica_prima = 'si'; 
        }elseif($motivos->key == "modifica_prima_no"){
            $modifica_prima = 'no';
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($modifica_prima))->_display();
        exit;
    }

    public function ajax_get_motivo(){

        if($this->input->post('motivo') == "Cancelación"){
            $tipo_motivo = "endoso_motivo_cancelacion";
        }elseif($this->input->post('motivo') == "Regular" || $this->input->post('motivo') == "Activación"){
            $tipo_motivo = "endoso_motivo_regular";
        }

        $motivos_endosos = $this->SegCatalogoRepository->listar_catalogo($tipo_motivo,'valor');
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($motivos_endosos))->_display();
        exit;
    }

    public function ocultoformulario($data = NULL){

        $this->assets->agregar_js(array(
            'public/assets/js/modules/endosos/plugins.js',
        ));

        $this->load->view('formulario',$data);
    }

    public function guardar(){

        if ($_POST) {

            $campo = $this->input->post('campos');

            Capsule::beginTransaction();
            try {

                if(empty($campo['uuid'])){

                    $detalle_unico = $campo['detalle_unico_endoso'];
                    unset($campo['detalle_unico_endoso']);

                    $codigo = Endoso::getLastCodigo(array('empresa_id' => $this->empresa_id));
                    $campo['endoso'] = $codigo;

                    $datosPolizas = Polizas::where(['id' => $campo['id_poliza']])->first();
                    if(empty($campo['id_ramo']) && empty($campo['cliente_id']) ){
                        $campo['cliente_id'] = $datosPolizas->cliente;
                        $campo['id_ramo'] = $datosPolizas->ramo_id;

                    }elseif(empty($campo['id_ramo'])){

                        $campo['id_ramo'] = $datosPolizas->ramo_id;
                    }elseif(empty($campo['cliente_id'])){
                        
                        $campo['cliente_id'] = $datosPolizas->cliente;
                    }
                    $campo['aseguradora_id'] = $datosPolizas->aseguradora_id;
                    $campo['empresa_id'] = $this->empresa_id;
                    $campo['fecha_creacion'] = date('Y-m-d');
                    $campo['usuario'] = $this->usuario_id;
                    if(!empty($campo['fecha_efectividad'])){
                        $campo['fecha_efectividad'] = date('Y-m-d', strtotime($campo['fecha_efectividad']));
                    }
                    
                    $endosos = Endoso::create($campo);

                    $modeloInstancia = Endoso::find($endosos->id);
                    $this->documentos->subir($modeloInstancia);

                    EndosoCoberturas::where('id_endoso',$detalle_unico)->update(['id_endoso' => $endosos->id]);
                    EndosoDeducciones::where('id_endoso',$detalle_unico)->update(['id_endoso' => $endosos->id]);
                    EndosoParticipacion::where('id_endoso',$detalle_unico)->update(['id_endoso' => $endosos->id]);
                    EndosoAgentePrin::where('id_endoso',$detalle_unico)->update(['id_endoso' => $endosos->id]);
                    EndosoPoliza::where('id_endoso',$detalle_unico)->update(['id_endoso' => $endosos->id]);
                    EndosoCliente::where('id_endoso',$detalle_unico)->update(['id_endoso' => $endosos->id]);
                    EndosoVigencia::where('id_endoso',$detalle_unico)->update(['id_endoso' => $endosos->id]);
                    EndosoPrima::where('id_endoso',$detalle_unico)->update(['id_endoso' => $endosos->id]);

                    $tipo = "Endosos_seguros";
                    $fecha_creado = date('Y-m-d H:i:s');
                    $comentario = "N° endoso: ".$endosos->endoso."<br>Tipo de endoso: ".$endosos->tipo."<br>Motivo: ".$endosos->motivos->valor;
                    $comment = ['comentario'=> $comentario ,'usuario_id'=>$this->usuario_id, 'comentable_id' =>$endosos->id_poliza, 'comentable_type'=>$tipo, 'created_at'=>$fecha_creado, 'empresa_id'=>$this->empresa_id ];
                    $Bitacora = new PolizasBitacora;
                    $Bitacora->create($comment);

                }else{

                    $datosPolizas = Polizas::where(['id' => $campo['id_poliza']])->first();
                    if(empty($campo['id_ramo']) && empty($campo['cliente_id']) ){
                        $campo['cliente_id'] = $datosPolizas->cliente;
                        $campo['id_ramo'] = $datosPolizas->ramo_id;

                    }elseif(empty($campo['id_ramo'])){

                        $campo['id_ramo'] = $datosPolizas->ramo_id;
                    }elseif(empty($campo['cliente_id'])){
                        
                        $campo['cliente_id'] = $datosPolizas->cliente;
                    }
                    if(!empty($campo['fecha_efectividad'])){
                        $campo['fecha_efectividad'] = date('Y-m-d', strtotime($campo['fecha_efectividad']));
                    }

                    $datosEndosos = Endoso::find($campo['id_endosos']);
                    if($datosEndosos->estado != $campo['estado']){
                        $tipo = "Endosos_seguros";
                        $fecha_creado = date('Y-m-d H:i:s');
                        $comentario = "N° endoso: ".$datosEndosos->endoso."<br>Estado anterior: ".$datosEndosos->estado."<br>Estado actual: ".$campo['estado']."<br>Fecha cambio: ".date('d/m/Y');
                        $comment = ['comentario'=> $comentario ,'usuario_id'=>$this->usuario_id, 'comentable_id' =>$datosEndosos->id_poliza, 'comentable_type'=>$tipo, 'created_at'=>$fecha_creado, 'empresa_id'=>$this->empresa_id ];
                        $Bitacora = new PolizasBitacora;
                        $Bitacora->create($comment);
                    }
                    $datosEndosos->update($campo);
                    $codigo =  $datosEndosos->endoso;

                }
                Capsule::commit();
            } catch (ValidationException $e) {
                log_message('error', $e);
                Capsule::rollback();
            }

            if(!is_null($endosos) || !is_null($datosEndosos)){
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Endoso ' . $codigo . '');
            } else {
                $mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            }
        }else {
            $mensaje = array('class' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su Endoso no fue procesado');
        }

        $this->session->set_flashdata('mensaje',$mensaje);
        redirect(base_url('endosos/listar'));

    }

    public function ajax_guardar_documentos(){

        $id_endoso = $this->input->post('id_endoso');

        $modeloInstancia = Endoso::find($id_endoso);
        $this->documentos->subir($modeloInstancia);

        if(!is_null($modeloInstancia)){
            $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado el documento correctamente');
            $this->session->set_flashdata('mensaje',$mensaje);
        }

        if($this->input->post('vista') == true){
            redirect(base_url('endosos/editar/'.bin2hex($modeloInstancia->uuid_endoso)));
        }else{
            redirect(base_url('endosos/listar/'));
        }
    }

    public function ajax_listar_endosos(){
        //Just Allow ajax request

        /*if(!$this->input->is_ajax_request()){
            return false;
        }*/

        $endoso = $this->input->post('endoso');
        $cliente = $this->input->post('cliente');
        $aseguradora = $this->input->post('aseguradora');
        $ramo = $this->input->post('ramo');
        $tipo_endoso = $this->input->post('tipo_endoso');
        $motivo_endoso = $this->input->post('motivo_endoso');
        $fecha_inicio = $this->input->post('fecha_inicio');
        $fecha_final = $this->input->post('fecha_final');
        $estado = $this->input->post('estado');

        $id_poliza = $this->input->post('id_poliza');
        $modulo = $this->input->post('modulo');

        if(!empty($endoso)){
           $clause['endoso'] = $endoso; 
        } 
        if(!empty($cliente)){
            $clause['cliente_id'] = $cliente;  
        } 
        if(!empty($aseguradora)){
            $clause['aseguradora_id'] = $aseguradora;  
        } 
        if(!empty($ramo)){
            $clause['id_ramo'] = $ramo;  
        }
        if(!empty($tipo_endoso)){
            $clause['tipo'] = $tipo_endoso;
        }
        if(!empty($motivo_endoso)){
            $clause['motivo'] = $motivo_endoso;  
        }
        if(!empty($fecha_inicio)){
            $clause['fecha_inicio'] = date('Y-m-d', strtotime($fecha_inicio));
        }
        if(!empty($fecha_final)){
            $clause['fecha_final'] = date('Y-m-d', strtotime($fecha_final));
        }
        if(!empty($estado)){
            $clause['estado'] = $estado;
        }
        if(!empty($id_poliza)){
            $clause['id_poliza'] = $id_poliza;
        }


        $clause['empresa_id'] = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Endoso::listar($clause,NULL,NULL,NULL,NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $endosos = Endoso::listar($clause,$sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if(!empty($endosos)){
            foreach ($endosos as  $row){

                $uuid_endosos = bin2hex($row['uuid_endoso']);
                $uuid_cliente = $row->cliente->uuid_cliente;
                $uuid_aseguradora = bin2hex($row->aseguradora->uuid_aseguradora);


                $estado_color = $row['estado'] == "En Trámite" ? 'background-color: #F8AD46' : ($row['estado'] == "Aprobado" ? 'background-color: #5cb85c' : ($row['estado'] == "Rechazado" ? 'background-color: #fc0d1b' : ($row['estado'] == "Cancelado" ? 'background-color: #000000' : 'background-color: #5bc0de')));

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row['id'].'" data-nombre="'.$row['endoso'].'"><i class="fa fa-cog"></i> <span class="hidden-sm hidden-xs">Opciones</span></button>';
                $hidden_options = "";
                $hidden_options = '<a href="'.base_url('endosos/editar/'.$uuid_endosos).'" data-id="'.$row['id'].'" class="btn btn-block btn-outline btn-success" >Ver detalle</a>';
                /*if($row['estado'] != 'Cancelado' && $row['estado'] != 'Aprobado' && $row['estado'] != 'Rechazado'){
                    $hidden_options.= '<a data-id="'.$row['id'].'" data-nombre="'.$row['endoso'].'" class="btn btn-block btn-outline btn-success cambiarEstados" >Cambiar Estado</a>';
                }*/
                if(empty($modulo)){
                    $hidden_options.= '<a data-id="'.$row['id'].'" class="btn btn-block btn-outline btn-success subirDocumentos" >Adjuntar documento</a>';
                }

                $modalstate = "";
                if($row['estado'] != "Pendiente"){
                    $modalstate .= '<a href="javascript:" data-id="'.$row['id'].'" data-estado="Pendiente" class="btn btn-block btn-outline cambiarEstadoBtn" style="color:white; background-color:#5bc0de" >Pendiente</a>';
                }elseif($row['estado'] != "En Trámite"){
                    $modalstate .= '<a href="javascript:" data-id="'.$row['id'].'" data-estado="En Trámite" class="btn btn-block btn-outline cambiarEstadoBtn" style="color:white; background-color:#F8AD46" >En Trámite</a>';    
                }
                //$modalstate .= '<a href="javascript:" data-id="'.$row['id'].'" data-estado="Aprobado" class="btn btn-block btn-outline cambiarEstadoBtn" style="color:white; background-color:#5cb85c" >Aprobado</a>';
                $modalstate .= '<a href="javascript:" data-id="'.$row['id'].'" data-estado="Rechazado" class="btn btn-block btn-outline cambiarEstadoBtn" style="color:white; background-color:#fc0d1b" >Rechazado</a>';
                $modalstate .= '<a href="javascript:" data-id="'.$row['id'].'" data-estado="Cancelado" class="btn btn-block btn-outline cambiarEstadoBtn" style="color:white; background-color:#000000" >Cancelado</a>';

                if(!empty($modulo) && $modulo == 'Polizas' ){

                    $redirectCliente = base_url('clientes/ver/'.$uuid_cliente);
                    $redirectAseguradora = base_url('aseguradoras/editar/'.$uuid_aseguradora);
                }else{

                    $redirectCliente = base_url('clientes/ver/'.$uuid_cliente.'?mod=endo');
                    $redirectAseguradora = base_url('aseguradoras/editar/'.$uuid_aseguradora.'/endosos');
                }
                  
                $response->rows[$i]["id"] = $row['id']; 
                $response->rows[$i]["cell"] = array(
                    'id' => $row['id'],
                    'endoso'=> '<a href="'.base_url('endosos/editar/'.$uuid_endosos).'" style="color:blue;">'.$row['endoso'].'</a>',
                    'cliente' => '<a href="'.$redirectCliente.'" style="color:blue;">'.$row->cliente->nombre.'</a>',
                    'aseguradora' => '<a href="'.$redirectAseguradora.'" style="color:blue;">'.$row->aseguradora->nombre.'</a>',
                    'ramo' => $row->ramos->nombre,
                    'poliza' => $row->polizas->numero,
                    'fecha' => $row['fecha_creacion'],
                    'tipo' => $row['tipo'],
                    'estado' => '<span style="color:white; '.$estado_color.'" class="btn btn-xs btn-block estadoEndosos" data-id="'.$row['id'].'" data-nombre="'.$row['endoso'].'" data-estado="'.$row['estado'].'" >'.$row["estado"].'</span>',
                    'link' => $link_option,
                    'options' => $hidden_options,
                    'modalstate' => $modalstate
                );
                $i++;
            }
        }
       echo json_encode($response);
       exit;
    }   
    
    public function ajax_cambiar_estado_endoso(){

        $FormRequest = new Flexio\Modulo\Endosos\Models\GuardarEndososEstado;
        try {
            $msg = $Agentes = $FormRequest->guardar();
        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }
    print json_encode($msg);
    exit;

    }

    public function exportar(){

        if(empty($_POST)){
            exit();
        }

        $ids = $this->input->post('ids');
        $id = explode(',' , $ids);

        if(empty($id)){
            return false;
        }

        $csv = array();
        $csvdata = array();

        $datosEndosos = Endoso::whereIn('id' , $id)->get();

        $i = 0;
        foreach ($datosEndosos AS $row) {
            $csvdata[$i]['numero'] = $row->endoso;
            $csvdata[$i]["cliente_id"] = utf8_decode(Util::verificar_valor($row->cliente->nombre));
            $csvdata[$i]["aseguradora_id"] = utf8_decode(Util::verificar_valor($row->aseguradora->nombre));
            $csvdata[$i]["ramo_id"] = utf8_decode(Util::verificar_valor($row->ramos->nombre));
            $csvdata[$i]["poliza_id"] =utf8_decode(Util::verificar_valor($row->polizas->numero));
            $csvdata[$i]["fecha_creacion"] = utf8_decode(Carbon::createFromFormat('Y-m-d', $row->fecha_creacion)->format('d/m/Y'));
            $csvdata[$i]["tipo"] = utf8_decode(Util::verificar_valor($row->tipo));
            $csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->estado));
            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $headers = [
            'No. Endoso',
            'Cliente',
            'Aseguradora',
            'Ramo/Riesgo',
            'No. Poliza',
            'Fecha creación',
            'tipo',
            'Estado',
        ];
        
        $decodingHeaders = array_map("utf8_decode", $headers);
        $csv->insertOne($decodingHeaders);
        $csv->insertAll($csvdata);
        $csv->output("Endosos -" . date('y-m-d') . ".csv");
        exit();

    }

    public function imprimirEndoso($id_endoso = NULL){

        $datosEndosos = Endoso::where(['id' => $id_endoso])->first();

        $nombre = $datosEndosos->endoso;
        $formulario = "formularioEndoso";

        $data = ['datos' => $datosEndosos, ];
        $dompdf = new Dompdf();
        $html = $this->load->view('pdf/' . $formulario, $data, true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($nombre, array("Attachment" => false));
        exit(0);
    }

    public function formularioModal($data = NULL){

        $this->load->view('formularioModalDocumento',$data);
    }

    public function formularioModalEditar($data = NULL) {

        $this->assets->agregar_var_js(array(
            "numero" => "",
            'data' => "",
        ));

        $this->load->view('formularioModalDocumentoEditar');
    }

    public function ajax_get_uuid_poliza(){
        $id_poliza = $this->input->post('id_poliza');
        $response = new stdClass();
        $response->uuid = '';
        $datosPoliza = Polizas::where(['id' =>$id_poliza ])->first();
        $response->uuid = bin2hex($datosPoliza->uuid_polizas);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit;
    }
    
    public function ocultoGetPoliza($uuid_poliza = NULL){

        $uuid_poliza = $this->input->post('uuid_poliza');

        $dataPoliza = Polizas::where(['uuid_polizas' => hex2bin($uuid_poliza)])->first();

        $estado = $this->SegCatalogoRepository->listar_catalogo('estado_p', 'orden');
        $estado = $estado->whereIn('key', array('polizas_pf', 'polizas_f'));
        $estado_pol = $dataPoliza->estado;
        $cliente = PolizasCliente::where(['id_poliza' => $dataPoliza->id])->first();
        if(count($cliente) == 0){
            $cliente = '';
        }
        $aseguradora = Aseguradoras::where(['id' => $dataPoliza->aseguradora_id])->get(array('id', 'nombre'));
        if (count($aseguradora) == 0) {
            $aseguradora = '';
        }
        $plan = Planes::where(['id' => $dataPoliza->plan_id])->get(array('nombre'));
        $coberturas = PolizasCobertura::where('id_poliza',$dataPoliza->id)->where('id_poliza_interes','')->get();
        $deducciones = PolizasDeduccion::where('id_poliza',$dataPoliza->id)->where('id_poliza_interes','')->get();
        $comision = $dataPoliza->comision;
        $vigencia = PolizasVigencia::where(['id_poliza' => $dataPoliza->id])->first();
        if(count($vigencia) == 0){
            $vigencia = '';
        }
        $prima = PolizasPrima::where(['id_poliza' => $dataPoliza->id])->first();
        $centroFacturacion = centroModel::where(['id' => $prima->centro_facturacion])->first();
        if ($centroFacturacion == '') {
            $centroFacturacion = '';
        }
        $participacion = PolizasParticipacion::where(['id_poliza' => $dataPoliza->id])->get();
        $totalParticipacion = PolizasParticipacion::where(['id_poliza' => $dataPoliza->id])->sum('porcentaje_participacion');
        if ($totalParticipacion == '') {
            $totalParticipacion = '';
        }

        $agenteprincipaltotal=Agente::where('principal',1)->where('id_empresa',$this->empresa_id)->count();
        if($agenteprincipaltotal>0){

            $agenteprincipal=Agente::where('id_empresa',$this->empresa_id)->where('principal',1)->first();
            $agenteprincipalnombre=$agenteprincipal->nombre;
            $totalparticipacion=PolizasParticipacion::where('id_poliza',$dataPoliza->id)->sum('porcentaje_participacion');
            $agtPrincipalporcentaje=number_format((100-$totalparticipacion),2);
        }else{

            $agenteprincipalnombre="";
            $agtPrincipalporcentaje=0;
        }

        if($dataPoliza->centros != null){
            $id_centroContable = $dataPoliza->centros->id;
            $nombre_centroContable = $dataPoliza->centros->nombre;
        }else{
            $id_centroContable = 0;
            $nombre_centroContable = '';
        }
        $cantidad_pagos =    $this->SegInteresesAseguradosRepository->listar_catalogo('cantidad_pagos', 'orden');
        $frecuencia_pagos = $this->SegInteresesAseguradosRepository->listar_catalogo('frecuencia_pagos', 'orden');
        $metodo_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('metodo_pago', 'orden');
        $sitio_pago =$this->SegInteresesAseguradosRepository->listar_catalogo('sitio_pago', 'orden');
        $centrosFacturacion = centroModel:: where("cliente_id",$dataPoliza->cliente)->where("empresa_id",$this->empresa_id)->get();
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
        $group = cliente::join('grp_grupo_clientes', 'grp_grupo_clientes.uuid_cliente', '=', 'cli_clientes.uuid_cliente')
        ->join('grp_grupo', 'grp_grupo.id', '=', 'grp_grupo_clientes.grupo_id')
        ->where(['cli_clientes.empresa_id' =>  $this->empresa_id, 'cli_clientes.id' => $dataPoliza->cliente])
        ->where('grp_grupo_clientes.deleted_at', '=', NULL)
        ->select('grp_grupo.nombre')
        ->get();
        $acreedores = PolizasAcreedores::where("id_poliza", $dataPoliza->id)->get();
        if (count($acreedores) == 0) {
            $acreedores = 'undefined';
        }
        

        $solicitudes_titulo = Ramos::find($dataPoliza->ramo_id);
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

        $pagador = $this->SegInteresesAseguradosRepository->listar_catalogo('pagador_seguros', 'orden');
        if($dataPoliza->id_tipo_int_asegurado !=5){
            unset($pagador[1]);
        }
        $agentes = Agente::join('agt_agentes_ramos', 'agt_agentes.id', '=', 'agt_agentes_ramos.id_agente')->where('id_ramo', '=', $dataPoliza->ramo_id)->orderBy("nombre")->get(array('agt_agentes.id', 'nombre'));

        $catego = "nueva";

        if($dataPoliza->id_tipo_int_asegurado == 1){
            $tabla = "articulo";
        }elseif($dataPoliza->id_tipo_int_asegurado == 2){
            $tabla = "carga";
        }elseif($dataPoliza->id_tipo_int_asegurado == 3){
            $tabla = "casco_aereo";
        }elseif($dataPoliza->id_tipo_int_asegurado == 4){
            $tabla = "casco_maritimo";
        }elseif($dataPoliza->id_tipo_int_asegurado == 5){
            $tabla = "persona";
        }elseif($dataPoliza->id_tipo_int_asegurado == 6){
            $tabla = "proyecto_actividad";
        }elseif($dataPoliza->id_tipo_int_asegurado == 7){
            $tabla = "ubicacion";
        }elseif($dataPoliza->id_tipo_int_asegurado == 8){
            $tabla = "vehiculo";
        }

        

        $response = new stdClass();
        $response->id_poliza = $dataPoliza->id;
        $response->tabla = $tabla;
        $response->estado_solicitud = $estado;
        $response->estado_pol = $estado_pol;
        $response->cliente = $cliente;
        $response->aseguradora = $aseguradora;
        $response->plan = $plan;
        $response->coberturas = $coberturas;
        $response->deducciones = $deducciones;
        $response->comision = $comision;
        $response->vigencia = $vigencia;
        $response->prima = $prima;
        $response->participacion = $participacion;
        $response->totalParticipacion = $totalParticipacion;
        $response->centroFacturacion = $centroFacturacion;
        $response->agtPrincipal =$agenteprincipalnombre;
        $response->agtPrincipalporcentaje = $agtPrincipalporcentaje;
        $response->id_centroContable = $id_centroContable;
        $response->nombre_centroContable = $nombre_centroContable;
        $response->cantidadPagos = $cantidad_pagos;
        $response->frecuenciaPagos = $frecuencia_pagos;
        $response->sitioPago = $sitio_pago;
        $response->metodoPago = $metodo_pago;
        $response->centrosFacturacion = $centrosFacturacion;
        $response->id_tipo_int_asegurado = $dataPoliza->id_tipo_int_asegurado;
        $response->tipo_ramo = $dataPoliza->tipo_ramo;
        $response->ramo = $dataPoliza->ramo;
        $response->nombre_ramo = $dataPoliza->ramo; 
        $response->centrosContables = $centroContable;
        $response->grupo = $group;
        $response->acreedores = $acreedores;
        $response->categoria_poliza = $catego;
        $response->id_tipo_poliza = $dataPoliza->tipo_ramo == "colectivo" ? 2 : 1;
        $response->validavida = $validavida;
        $response->poliza_declarativa = $dataPoliza->poliza_declarativa;
        $response->pagador = $pagador;
        $response->agentes = $agentes;
        $response->permiso_editar = 1;

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit;
    }

    public function ocultotablaPrueba(){

        $this->load->view('formularioPoliza');
    }

    public function comentaformulario($data){

        $uuid = $data["uuid_endoso"];
        $endoso = Endoso::findByUuid($uuid);

        $data = array();
        $Bitacora = new Flexio\Modulo\Endosos\Models\EndososBitacora;
        $data["Bitacora"] = $Bitacora;
        $data["nid_endoso"] = $endoso->id;
        $data["historial"] = $Bitacora->where(array("comentable_id" => $endoso->id, "comentable_type" => "Comentario"))->with(array('usuario'))->orderBy("created_at", "desc")->get(array("comentario", "created_at", "usuario_id"))->toArray();

        $this->load->view('comentarios', $data);

    }

    function ajax_guardar_comentario() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        try {
            $Bitacora = new Flexio\Modulo\Endosos\Models\EndososBitacora;

            $tipo = "Comentario";
            $id_endoso = $this->input->post('nid_endoso');
            $comentario = $this->input->post('comentario');
            $usuario = $this->usuario_id;
            $fecha_creado = date('Y-m-d H:i:s');

            $comment = ['comentario' => $comentario, 'usuario_id' => $usuario, 'comentable_id' => $id_endoso, 'comentable_type' => $tipo, 'created_at' => $fecha_creado, 'empresa_id' => $this->empresa_id];

            $bus = Endoso::where('id',$id_endoso);
            if ($bus->count() != 0) {
                $msg = $Bitacora->create($comment);
                exit;
            }
        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        die(json_encode($msg));
    }

    function ajax_carga_comentarios_poliza() {
        $html = '';
        try {
            $id_endoso = $_POST["id_endoso"];

            $Bitacora = new EndososBitacora();
            $historial = $Bitacora->where(array("comentable_id" => $id_endoso, "comentable_type" => "Comentario"))->with(array('usuario'))->orderBy("created_at", "desc")->get(array("comentario", "created_at", "usuario_id"))->toArray();
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
                      ' . $Bitacora->getCuantoTiempo($item["created_at"]) . '
                      <br>
                      <small>' . $item["created_at"] . '</small>
                      <div><small>' . $item["usuario"]["nombre"] . " " . $item["usuario"]["apellido"] . " " . $Bitacora->getHora($item["created_at"]) . '</small></div>
                  </span>
              </div>
          </div>';
        }
            $data = array();
            $data["nid_endoso"] = $id_endoso;
        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
            die("error " . $e->getMessage());
        }
        die($html);
    }

    public function guardarPoliza(){
        $inf = array();
        $inf["msg"] ='error';
        //$error = false;
        try {

            $detalleUnico = $this->input->post('detalleUnico');
            $id_poliza = $this->input->post('id_poliza');

            $polizaData = Polizas::where('id',$id_poliza)->first();
            $datosPoliza['id_endoso'] = $detalleUnico;
            $datosPoliza['id_aseguradora'] = $polizaData->aseguradora_id;
            $datosPoliza['id_plan'] = $polizaData->plan_id;
            $datosPoliza['comision'] = $this->input->post('comision');
            $datosPoliza['centro_contable'] = $polizaData->centro_contable;
            $datosPoliza['estado'] = $polizaData->estado;
            EndosoPoliza::create($datosPoliza);

            $coberturas = json_decode($this->input->post('planesCoberturas'),true);
            if($coberturas != NULL){
                
                for ($i = 0; $i < count($coberturas['coberturas']['nombre']); $i++) {
                    $solCoberturas = [
                        'cobertura' => $coberturas['coberturas']["nombre"][$i],
                        'valor_cobertura' => $coberturas['coberturas']["valor"][$i],
                        'id_endoso' => $detalleUnico
                    ];
                    EndosoCoberturas::create($solCoberturas);
                }
                
                
                for ($i=0;$i<count($coberturas['deducibles']['nombre']);$i++) {
                    $solDeducion = [
                        'deduccion' => $coberturas['deducibles']['nombre'][$i],
                        'valor_deduccion' => $coberturas['deducibles']['valor'][$i],
                        'id_endoso' => $detalleUnico
                    ];
                    EndosoDeducciones::create($solDeducion);
                }
            }else{

                $coberturas = PolizasCobertura::where('id_poliza',$id_poliza)->where('id_poliza_interes',0)->get();
                $deducciones = PolizasDeduccion::where('id_poliza',$id_poliza)->where('id_poliza_interes',0)->get();

                foreach ($coberturas as $key => $value) {
                    $solCoberturas = [
                        'cobertura' => $value["cobertura"],
                        'valor_cobertura' => $value["valor_cobertura"],
                        'id_endoso' => $detalleUnico
                    ];
                    EndosoCoberturas::create($solCoberturas);
                }
                foreach ($deducciones as $key => $value) {
                    $solDeducion = [
                        'deduccion' => $value['deduccion'],
                        'valor_deduccion' => $value['valor_deduccion'],
                        'id_endoso' => $detalleUnico
                    ];
                    EndosoDeducciones::create($solDeducion);
                }
            }   
            
            $participacion = $this->input->post('participacion');
            if(!empty($participacion) && $participacion != '' ){
                for ($i=0; $i <count($participacion['nombre']) ; $i++) { 
                    $solParticipacion = [
                        'id_endoso' => $detalleUnico,
                        'agente' => $participacion['nombre'][$i], 
                        'porcentaje_participacion' => $participacion['valor'][$i]
                    ];
                    EndosoParticipacion::create($solParticipacion);    
                }
            }

            $agentePrin = SegPolizasAgentePrin::where('poliza_id',$id_poliza)->first();
            $AgentePrin['id_endoso'] = $detalleUnico;
            $AgentePrin['agente_id'] = $agentePrin->agente_id;
            $AgentePrin['comision'] = $this->input->post('porcAgentePrincipal');
            EndosoAgentePrin::create($AgentePrin);
            
            $Cliente['id_endoso'] = $detalleUnico;
            $Cliente['nombre_cliente'] = $this->input->post('clienteNombre');
            $Cliente['identificacion'] = $this->input->post('clienteIdentificacion');
            $Cliente['n_identificacion'] = $this->input->post('clienteNoIdentificacion');
            $Cliente['grupo'] = $this->input->post('clienteGrupo');
            $Cliente['telefono'] = $this->input->post('clienteTelefono');
            $Cliente['correo_electronico'] = $this->input->post('clienteCorreo');
            $Cliente['direccion'] = $this->input->post('clienteDireccion');
            $Cliente['exonerado_impuesto'] = $this->input->post('clienteExoneradoImp');
            EndosoCliente::create($Cliente);

            $vigencia['id_endoso'] = $detalleUnico;
            $vigencia['vigencia_desde'] = $this->input->post('vigenciaDesde');
            $vigencia['vigencia_hasta'] = $this->input->post('vigenciaHasta');
            $vigencia['suma_asegurada'] = $this->input->post('vigenciaSuma') != '' ? $this->input->post('vigenciaSuma') : '0.00';
            $vigencia['tipo_pagador'] = $this->input->post('vigenciaPagador');
            $vigencia['pagador'] = $this->input->post('vigenciaNombrePagador');
            if($this->input->post('vigenciaDeclarativa') == 'true'){
                $vigencia['poliza_declarativa'] = 'si';
            }else{
              $vigencia['poliza_declarativa'] = 'no';
            }
            EndosoVigencia::create($vigencia);

            $prima['id_endoso'] = $detalleUnico;
            $prima['prima_anual'] = $this->input->post('primaAnual');
            $prima['impuesto'] = $this->input->post('primaImpuesto');
            $prima['otros'] = $this->input->post('primaOtros');
            $prima['descuentos'] = $this->input->post('primaDescuentos');
            $prima['total'] = $this->input->post('primaTotal');
            $prima['frecuencia_pago'] = $this->input->post('pagosFrecuencia');
            $prima['metodo_pago'] = $this->input->post('pagosMetodo');
            $prima['fecha_primer_pago'] = $this->input->post('pagosPrimerPago');
            $prima['cantidad_pagos'] = $this->input->post('pagosCantidad');
            $prima['sitio_pago'] = $this->input->post('pagosSitio');
            $prima['centro_facturacion'] = $this->input->post('pagosCentroFac');
            $prima['direccion_pago'] = $this->input->post('pagosDireccion');
            EndosoPrima::create($prima);

            $inf["msg"] ='ok';

        } catch (\Exception $e) {
            $msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
            print  $inf["msg"] = 'error'. __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n";
        }
        print json_encode($inf);
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
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css'
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
            'public/assets/js/modules/endosos/routes.js',
        ));
    }


    public function tablasIntereses() {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/intereses_asegurados/tablaarticulo.js',
            'public/assets/js/modules/intereses_asegurados/tablacarga.js',
            'public/assets/js/modules/intereses_asegurados/tablaaereo.js',
            'public/assets/js/modules/intereses_asegurados/tablamaritimo.js',
            'public/assets/js/modules/intereses_asegurados/tablapersonas.js',
            'public/assets/js/modules/intereses_asegurados/tablaproyecto.js',
            'public/assets/js/modules/intereses_asegurados/tablaubicacion.js',
            'public/assets/js/modules/intereses_asegurados/tablavehiculo.js',
        ));
    
    }


}