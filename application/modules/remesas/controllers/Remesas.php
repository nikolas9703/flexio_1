<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 02:30 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Remesas\Models\Remesa as Remesa;
use Flexio\Modulo\Remesas\Models\Remesas_cobros;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Repository\RamoRepository as RamoRepository;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;
use Flexio\Modulo\Ramos\Models\RamosUsuarios;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros as cobros;
use Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroRepository as facturaRepository;
use Flexio\Modulo\Planes\Repository\PlanesRepository as PlanesRepository;
use Flexio\Modulo\Bancos\Models\Bancos as bancos;
use Flexio\Modulo\Cobros_seguros\Models\CobroFactura as CobroFactura;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaRemesaSaliente;
use Flexio\Modulo\Pagos\Models\Pagos;
use Flexio\Modulo\Pagos\Models\PagosMetodos;
use Flexio\Modulo\Pagos\Models\PagosPagables;

class Remesas extends CRM_Controller
{

    private $id_empresa;
    private $empresa_id;
    private $id_usuario;
    private $empresaObj;
    protected $ramoRepository;
    protected $FacturaSeguro;
    protected $Planes;


    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('file', 'string', 'util'));
        //$this->load->model('remesas/Remesas_orm');


        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_empresa = $this->empresaObj->id;


        $uuid_usuario = $this->session->userdata("huuid_usuario");
        $usuario = Usuarios::findByUuid($uuid_usuario);
        $this->usuario_id = $usuario->id;
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
        $this->ramoRepository = new RamoRepository();
        $this->FacturaSeguro = new facturaRepository();
        $this->Planes = new PlanesRepository();

    }

    function crearsubpanel()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/remesas/remesas.js',
            ));

        $this->template->vista_parcial(array(
            'remesas',
            //'crear',
            ));
    }

    public function ocultotabla($id_cliente = NULL)
    {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/remesas/tabla.js',
            ));

        if (!empty($id_cliente)) {

            // Agregra variables PHP como variables JS
            $this->assets->agregar_var_js(array(
                "id_cliente" => $id_cliente
                ));

        }

        $this->load->view('tabla');
    }

    public function listar(){

        /*if(!$this->auth->has_permission('acceso', 'remesas/crear')){

            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para listar', 'titulo' => 'Remesas ');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url(''));
        }*/

        if (is_null($this->session->flashdata('mensaje')) ) {
           $mensaje = []; 
        } else {
            $mensaje = $this->session->flashdata('mensaje');
        }

        
        $this->_css();
        $this->_js();

        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));

        $data = array();
        $data['mensaje'] = $mensaje;

        $data['userData'] = $this->session->userdata('empresa_id');
        $data['usuarios'] = Usuarios::join('usuarios_has_roles', 'usuario_id', '=', 'usuarios.id')
        ->where('usuarios_has_roles.empresa_id', '=', $this->empresa_id)
        ->where('usuarios.estado', '=', 'Activo')
        ->select('usuarios.id', 'nombre','apellido')
        ->groupBy('usuarios.id')
        ->get();

        $data['aseguradoras'] = Aseguradoras::where('empresa_id',$this->empresa_id)
        ->where('estado','Activo')->get();
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Remesas Salientes',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Remesas Salientes</b>', "activo" => true)
                ),
            "filtro" => false,
            "menu" => array()
            );

        $breadcrumb["menu"] = array(
            "url" => 'remesas/crear',
            "clase" => 'modalOpcionesCrear',
            "nombre" => "Crear"
            );
        //$menuOpciones["#cambiarEstadoSolicitudesLnk"] = "Cambiar estado";
        //$menuOpciones["#imprimirCartaSolicitudesLnk"] = "Imprimir carta";
        
        //$menuOpciones["#cambiarEstadosBtn"]= "Cambiar estado";
        $menuOpciones["#exportarRemesasBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Listado de Remesas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

    public function ajax_listar_remesas(){
        //Just Allow ajax request

        if(!$this->input->is_ajax_request()){
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $remesa = $this->input->post('remesa');
        $aseguradora = $this->input->post('aseguradora');
        $usuario = $this->input->post('usuario');
        $estado = $this->input->post('estado');
        $recibo = $this->input->post('recibo')=='0'? "ZERO": $this->input->post('recibo');
        $poliza = $this->input->post('poliza');
        //fix count
        
        
        $clause= array('empresa_id' => $this->empresa_id);
        if(!empty($remesa)) $clause['remesa'] = $remesa;
        if(!empty($aseguradora)) $clause['aseguradora'] = $aseguradora;
        if(!empty($usuario))$clause['usuario'] = $usuario;
        if(!empty($estado))$clause['estado'] = $estado;
        if(!empty($recibo))$clause['recibo'] = $recibo;
        if(!empty($poliza))$clause['poliza'] = $poliza;
        $count = Remesa::listar($clause,NULL,NULL,NULL,NULL)->count();
        
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $cuentas = Remesa::listar($clause,$sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->record  = $count;
        $i=0;
        $estados = array("#f8ac59"=>"En Proceso","black"=>"Anulado",/*"#5cb85c"=>"Pagada",*/"#5bc0de"=>"Por pagar");
        
        if(!empty($cuentas)){
            foreach ($cuentas as  $row){
                $labelClass="";
                $id= $row['id'];
                $estado="";
                foreach ($estados as $key => $value) {
                    $option = $value;
                    if($row['estado'] != $option ){
                        $estado .='<button class="btn btn-block  modal-std" data-id="'.$id.'" data-estado="'.$option.'" data-estado-anterior="'.$row['estado'].'" style="color:white;background-color:'.$key.'">'.$option.'</button>'; 
                    }else{
                        $updateState = $row['estado'] == "Pagada" ? "" : "updateState";
                        $labelClass='<button class="btn btn-xs btn-block '.$updateState.'" data-id="'.$id.'" data-estado="'.$option.'" style="color:white;background-color:'.$key.'">'.$option.'</button>';
                        if($row['estado'] == "Anulado"){
                            //$estado ='<button class="btn btn-block  modal-std" data-id="'.$id.'" data-estado="En Proceso" data-estado-anterior="'.$row['estado'].'" style="color:white;background-color:#f8ac59">En Proceso</button>';
                            break;
                        }
                    }
                } 
               $hidden_options = "";
               $payDay = new Carbon($row['created_at']);
               $fechaRemesa = $row['fecha'] == "" ? $payDay->format('Y-m-d') : $row['fecha'];
               $uuid_poliza = bin2hex($row['uuid_polizas']);
               $urlPolicy = base_url("polizas/editar/$uuid_poliza");
               $uuid_remesa = bin2hex($row['uuid_remesa']);
               $url = base_url("remesas/editar/$uuid_remesa");
               $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-sm hidden-xs">Opciones</span></button>';
               $hidden_options = '<a href="' . $url . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarRemesa" >Ver detalle</a>';
               $hidden_options.= '<a data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success descargarRemesa" >Descargar</a>';

               $level = substr_count($row['nombre'],".");
               $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                'id' => $row['id'],
                'remesa'=> '<a href="'.$url.'">'.$row['remesa'].'</a>',
                'fecha' => $fechaRemesa,
                'recibos_remesados' => "<a href='".$url."'>".$row['recibos_remesados']."</a>",//$row['cantidadRecibos']
                'poliza' => '<a href="'.$urlPolicy.'">'.$row['numero'].'</a>',
                'usuario' => $row['fullname'],
                'estado' =>  $labelClass,   
                'monto'  => $row['monto'],
                'aseguradora_id'=>$row['nombre'],
     //                    'opciones' =>$link_option,
                'options' => $hidden_options,
                'link' => $link_option,
                    "level" => isset($row["level"]) ? $row["level"] : "0", //level
                    'parent' => $row["padre_id"]==0? "NULL": (string)$row["padre_id"], //parent
                    //'isLeaf' =>(Ramos_orm::is_parent($row['id']) == true)? false: true, //isLeaf
                    'expanded' =>  false, //expended
                    'loaded' => true, //loaded
                    'updatedStated' => $estado,
                    'recibo' => $recibo 
                    ) );
                $i++;
            }
        }
    echo json_encode($response);
    exit;
    }

public function crear(){

    if (!$this->auth->has_permission('acceso', 'remesas/crear')) {
        // No, tiene permiso, redireccionarlo.
        $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para crear', 'titulo' => 'Remesas ');
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('remesas/listar'));
    } else {
        $mensaje = [];
    }


    $this->assets->agregar_js(array(
        'public/assets/js/modules/remesas/plugins.js',
        'public/assets/js/default/toast.controller.js'
        ));

    $this->_css();
    $this->_js();

    $data = array();
    $data['vista'] = "crear";

    $data['aseguradoras'] = Aseguradoras::where(['empresa_id' =>$this->id_empresa])->get();
    $clause = array('empresa_id' => $this->id_empresa);
    $data['menu_crear'] = $this->ramoRepository->listar_cuentas($clause);

    $ramosRoles = RolesUsuario::with(array('ramos'))->where(['usuario_id' => $this->usuario_id, 'empresa_id' => $this->id_empresa])->get();
    $ramosUsuario = RamosUsuarios::where(['id_usuario' => $this->usuario_id])->get();

    $data['rolesArray'] = array();
    $data['usuariosArray'] = array();
    $data['mensaje'] = $mensaje;

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

    $this->assets->agregar_var_js(array( 
        "vista" => 'crear',
        "flexio_mensaje" => collect($mensaje),
        "ver" => 0,
    ));

    $breadcrumb = array(
        "titulo" => '<i class="fa fa-archive"></i> Remesas Salientes: crear',
        "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
            1 => array("nombre" => '<b>Remesas Salientes</b>', "url" => "remesas/listar", "activo" => true),
            2 => array("nombre" => '<b>Crear</b>', "activo" => true)
            ),
        "filtro" => false,
        "menu" => array()
        );

    $breadcrumb["menu"] = array(
        "url" => '',
        "clase" => 'modalOpcionesCrear',
        "nombre" => "Crear"
    );

        //$menuOpciones["#cambiarEstadoSolicitudesLnk"] = "Cambiar estado";
        //$menuOpciones["#imprimirCartaSolicitudesLnk"] = "Imprimir carta";
    $menuOpciones["#eliminarRemesaBtn"] = "Eliminar";
    $menuOpciones["#exportarSolicitudesLnk"] = "Exportar";
    $breadcrumb["menu"]["opciones"] = $menuOpciones;


    $this->template->agregar_titulo_header('Crer Remesa saliente');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
}

public function tabla_remesas(){

    $this->assets->agregar_js(array(
        'public/assets/js/modules/remesas/crear.vue.js'
        ));


    $this->load->view('tabla_remesas');
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

    $clause['id'] = $id;
    $clause['empresa_id'] = $this->empresa_id;
    $contactos = Remesa::listar($clause, NULL, NULL, NULL, NULL);
    if (empty($contactos)) {
        return false;
    }
    $i = 0;
    foreach ($contactos AS $row) {
        $csvdata[$i]['remesa'] = $row->remesa;
        $csvdata[$i]['rango_fechas'] = "desde: ".$row->fecha_desde ." hasta: " .$row->fecha_hasta;
        $csvdata[$i]['recibos_remesados'] = $row->cantidadRecibos;
        $csvdata[$i]['nombre'] = $row->nombre;
        $csvdata[$i]['monto'] = $row->monto;
        $csvdata[$i]['fecha'] = $row->fecha;
        $ramos = explode(',', $row->ramos_id);
        $ramosString = "";
        foreach ($ramos as $key => $value) {
            # code...
             $query=Ramos:: where('id',$value)
             ->select('nombre')
             ->first();
             if(count($query))
             $ramosString .= $query->nombre."-";
        }
        $csvdata[$i]['ramo'] = $ramosString;
        $csvdata[$i]['fullname'] = $row->fullname;
        $csvdata[$i]['estado'] =   $row->estado;
        $i++;
    }
        //we create the CSV into memory
    $csv = Writer::createFromFileObject(new SplTempFileObject());
    $headers = [
    'No.  remesa',
    'Rango de Fecha',
    'Recibos remesados',
    'Aseguradora',
    'Monto',
    'Fecha',
    'Ramos',
    'Usuario',
    'Estado',
    ];
    $decodingHeaders = array_map("utf8_decode", $headers);
    $csv->insertOne($decodingHeaders);
    $csv->insertAll($csvdata);
    $csv->output("RemesaSalientes-" . date('y-m-d') . ".csv");
    exit();
}


public function ajax_get_remesa_saliente() {

    $vista = $_POST['vista'];
    $id_asegurado = $_POST['id_aseguradora'];
    $fecha_inicial = date('Y-m-d', strtotime($_POST['fecha_inicio'])); 
    $fecha_final = date('Y-m-d', strtotime($_POST['fecha_final']) );
    $id_ramos = $_POST['id_ramos'];
    $id_remesa = $_POST['id_remesa'];
    $cobros_eliminar = $_POST['cobros_eliminar'];
    $actualiza = $_POST['actualiza'];


    $response = new stdClass();
    $response->inter = array();
    $response->idCobros = array();
    $response->valor_cobro = array();
    $response->guardar = 0;
    $response->remesa = array();
    $datos = 0;
    $total_pago = 0;
    $datosRemesas = 0;

    if($vista == "crear"){
        $remesaDatos = Remesa::where(['aseguradora_id' => $id_asegurado, 'estado' => 'En proceso'])->first();
        if(count($remesaDatos) > 0){
            $response->guardar = 1;
            $response->remesa[0] = array('numero_remesa' => $remesaDatos->remesa, 'uuid_remesa' => bin2hex($remesaDatos->uuid_remesa));    
        }
    }
    if($vista == "editar"){
        $remesaDatos = Remesa::where(['id' => $id_remesa, ])->first();
        $estado = $remesaDatos->estado;
        $id_cobros = Remesas_cobros::where(['id_remesa' => $id_remesa])->select('id_cobro')->get(array('id_cobro'));
    }
    if($response->guardar == 0){
        foreach($id_ramos as $key => $info){

            if( $vista == "crear"  ){
                if($actualiza == 3){
                    $cobros = CobroFactura::whereRaw("DATE(cob_cobro_facturas.created_at) between '".$fecha_inicial."' AND '".$fecha_final."' AND cob_cobros.estado = 'aplicado' AND cob_cobros.num_remesa = '' AND cob_cobro_facturas.id_ramo = ".$info."")->whereNotIn('cob_cobros.id',$cobros_eliminar)->join("cob_cobros", "cob_cobros.id", "=", "cob_cobro_facturas.cobro_id")->get();
                }else{
                    $cobros = CobroFactura::whereRaw("DATE(cob_cobro_facturas.created_at) between '".$fecha_inicial."' AND '".$fecha_final."' AND cob_cobros.estado = 'aplicado' AND cob_cobros.num_remesa = '' AND cob_cobro_facturas.id_ramo = ".$info."")->join("cob_cobros", "cob_cobros.id", "=", "cob_cobro_facturas.cobro_id")->get();   
                }
            }elseif($vista == "editar" ){
                if($estado  == "En Proceso" && $actualiza == 2){
                   $cobros = CobroFactura::whereRaw("DATE(cob_cobro_facturas.created_at) between '".$fecha_inicial."' AND '".$fecha_final."' AND cob_cobros.estado = 'aplicado' AND cob_cobros.num_remesa = '' AND cob_cobro_facturas.id_ramo = ".$info."")->join("cob_cobros", "cob_cobros.id", "=", "cob_cobro_facturas.cobro_id")->get();
                }else{
                    $cobros = CobroFactura::whereIn('cob_cobro_facturas.cobro_id', $id_cobros)->where(['cob_cobro_facturas.id_ramo' => $info])->join("cob_cobros", "cob_cobros.id", "=", "cob_cobro_facturas.cobro_id")->get();  
                }
                
            }

            $sub_total_comision = 0;
            $sub_total_valorSobre_comision = 0;
            $sub_total_aseguradora = 0;
            $nombre_ramo = '';

            foreach($cobros as $key => $value) {

                if( ($vista == "crear") || ($vista == "editar" && $estado  == "En proceso" && $actualiza == 2) ){
                    $datosRemesas = Remesas_cobros::whereRaw("seg_remesas_cobros.id_cobro = ".$value->cobro_id." AND seg_remesas.estado = 'En proceso' OR seg_remesas_cobros.id_cobro = ".$value->cobro_id." ANd seg_remesas.estado = 'Pagada' OR seg_remesas_cobros.id_cobro = ".$value->cobro_id." AND seg_remesas.estado = 'Por pagar' ")->join("seg_remesas","seg_remesas.id", "=" ,"seg_remesas_cobros.id_remesa")->get();
                }

                if( ($vista == "crear" && count($datosRemesas) == 0) || ($vista == "editar" && $datosRemesas == 0)  ){
                    $factura = $this->FacturaSeguro->GetFacturasRemesasSalientes($value->cobrable_id, $id_asegurado);
                    if($factura != NULL){
                        $datos = 1;
                        $prima_neta = number_format(($value->monto_pagado - $factura['impuestos']), 2, '.', '');
                        array_push($response->idCobros, array('id_cobro' => $value->cobro_id));

                        if($factura['desc_comision'] == 'si'){
                            $valor_comision =   round(($prima_neta * ($factura['comision']/100)),4); //round(($value->monto_pagado * ($factura['comision']/100)),4);
                            $valor_sobreComision = round(($prima_neta * ($factura['porcentaje_sobre_comision']/100)),2);//round(($value->monto_pagado * ($factura['porcentaje_sobre_comision']/100)),2);
                            $total_aseguradora = round(($value->monto_pagado - $valor_comision + $valor_sobreComision),2);
                        }else{
                            $valor_comision = "0.00";
                            $valor_sobreComision = "0.00";
                            $total_aseguradora = round($value->monto_pagado,2);
                        }
                        
                        $comision = $factura['comision'];
                        $sobre_comision = $factura['porcentaje_sobre_comision'];
                        $sub_total_comision = $sub_total_comision + $valor_comision;
                        $sub_total_valorSobre_comision = $sub_total_valorSobre_comision + $valor_sobreComision;
                        $sub_total_aseguradora = $sub_total_aseguradora + $total_aseguradora;
                        $nombre_ramo = $factura['ramo'];
                        $valor_comision = number_format($valor_comision, 2, '.', '');
                        $valor_sobreComision = number_format($valor_sobreComision, 2, '.', '');
                        $total_aseguradora = number_format($total_aseguradora, 2, '.', '');
                        array_push($response->valor_cobro, array('valor_cobro' =>$total_aseguradora));

                        array_push($response->inter, array("check" => '', "id" => $value->id, "codigo" => $value->codigo, "numero_poliza" => $factura['numero'] , 'nombre_ramo' => $factura['ramo'], 'id_ramo' =>$factura['ramo_id'], 'nombre_aseguradora' => $factura['nombre'], 'inicio_vigencia' => date($factura["fecha_desde"]), 'fin_vigencia' => date($factura["fecha_hasta"]), 'prima_total' => '$'.number_format($value->monto_pagado, 2, '.', ''), 'impuesto' => '$'.$factura['impuestos'], 'prima_neta' => '$'.$prima_neta/*$factura['prima_anual']*/ ,'desc_comision' => $comision.'%', 'valor_descuento' => '$'.$valor_comision, 'sobre_comision' => $sobre_comision.'%', 'valor_sobreComision' => '$'.$valor_sobreComision, 'total_aseguradora' => '$'.$total_aseguradora, 'estilos' => 'font-weight: normal' ));
                    }
                }

            }

            if($datos == 1){
                $sub_total_comision = number_format($sub_total_comision, 2, '.', '');
                $sub_total_valorSobre_comision = number_format($sub_total_valorSobre_comision, 2, '.', '');
                $sub_total_aseguradora = number_format($sub_total_aseguradora, 2, '.', '');
                $total_pago = $total_pago + $sub_total_aseguradora;
                array_push($response->inter, array("check" => 'hidden', "id" => '', "codigo" => '', "numero_poliza" => '', 'nombre_ramo' => '', 'id_ramo' => '', 'nombre_aseguradora' => '', 'inicio_vigencia' => '', 'fin_vigencia' => '', 'prima_total' => '', 'impuesto' => '', 'prima_neta' => '' ,'desc_comision' => 'Subtotal '.$nombre_ramo , 'valor_descuento' => '$'.$sub_total_comision, 'sobre_comision' => '', 'valor_sobreComision' => '$'.$sub_total_valorSobre_comision, 'total_aseguradora' => '$'.$sub_total_aseguradora, 'estilos' => 'font-weight: bold; background-color:#efefef;' ));
            }
            $datos = 0;
        }
        $total_pago =  number_format($total_pago, 2, '.', '');
        $response->monto = $total_pago;
        array_push($response->inter, array("check" => 'hidden', "id" => '', "codigo" => '', "numero_poliza" => '', 'nombre_ramo' => '', 'id_ramo' => '', 'nombre_aseguradora' => '', 'inicio_vigencia' => '', 'fin_vigencia' => '', 'prima_total' => '', 'impuesto' => '', 'prima_neta' => '' ,'desc_comision' => 'Total ' , 'valor_descuento' => '', 'sobre_comision' => '', 'valor_sobreComision' => '', 'total_aseguradora' => '$'.$total_pago, 'estilos' => 'font-weight: bold; background-color:#cccccc;'));
    }
    
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
    exit;       
}

public function guardar(){

    $remesa = Util::set_fieldset("remesas");
    $cobros = Util::set_fieldset("id_cobros");
    $valor_cobros = util::set_fieldset("valor_cobro");
    $fecha_desde = $this->input->post("fecha_desde");
    $fecha_hasta = $this->input->post("fecha_hasta");

    /*Capsule::beginTransaction();
    try {*/
        if(!isset($remesa["id_remesa"])){
            $count = Remesa::where('empresa_id',$this->id_empresa)->count();
            $codigo = Util::generar_codigo('RMS'.$this->id_empresa, ($count+1) );
            $datosRemesas['remesa'] = $codigo;
        }
        $datosRemesas['uuid_remesa'] = Capsule::raw("ORDER_UUID(uuid())");
        $datosRemesas['fecha'] = date('Y-m-d');
        $datosRemesas['aseguradora_id'] = $remesa["id_aseguradora"]; 
        $datosRemesas['monto'] = $remesa["monto_remesa"];
        $datosRemesas['recibos_remesados'] = count(array_unique($cobros));
        $datosRemesas['usuario'] = $this->usuario_id;
        $datosRemesas['empresa_id'] = $this->id_empresa;
        $datosRemesas['created_at'] = date('Y-m-d');
        $datosRemesas['updated_at'] = date('Y-m-d');
        $datosRemesas['creado_por'] = $this->usuario_id;
        $datosRemesas['fecha_desde'] = date('Y-m-d', strtotime($fecha_desde));
        $datosRemesas['fecha_hasta'] = date('Y-m-d', strtotime($fecha_hasta));
        $datosRemesas['ramos_id'] = $remesa['ramos'];

        if(isset($remesa["guardar"])){
            $datosRemesas['estado'] = "En Proceso";
        }elseif(isset($remesa["pagar"])){
            $datosRemesas['estado'] = "Por Pagar";
        }
        
        if(!isset($remesa["id_remesa"])){
            $remesas = Remesa::create($datosRemesas);
            $estado_anterior = '';
        }else{
            $remesas = Remesa::where(['id' => $remesa["id_remesa"]])->first();
            $estado_anterior = $remesas->estado;
            $remesas->update($datosRemesas);
        }
        $codigo = $remesas->remesa;
        $this->crear_pago_seguros($remesas,$estado_anterior);

        Remesas_cobros::where(['id_remesa' => $remesas->id])->delete();
        $datosCobros = $cobros;
        $valorCobros = $valor_cobros;
        foreach($datosCobros as $key => $value){
            $remesasCobros['id_remesa'] = $remesas->id;
            $remesasCobros['id_cobro'] = $value;
            $remesasCobros['valor_cobro'] = $valorCobros[$key];
            Remesas_cobros::create($remesasCobros);
        }

    /*}catch (ValidationException $e) {
        log_message('error', $e);
        Capsule::rollback();
    }*/
    if(!empty($remesas)){
        $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Remesa ' . $codigo . '');
    }

    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('remesas/listar'));
}

public function editar($uuid = null){

    if( !$this->auth->has_permission('acceso', 'remesas/ver/(:any)') && !$this->auth->has_permission('acceso', 'remesas/editar/(:any)') ) { 
        // No, tiene permiso, redireccionarlo.
        $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para editar', 'titulo' => 'Remesas ');
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('remesas/listar'));

    }elseif($this->auth->has_permission('acceso', 'remesas/editar/(:any)')) {
        $ver = 0;
        $mensaje = [];
    }else{
        $ver = 1;
        $mensaje = [];
    }

    $this->assets->agregar_js(array(
        'public/assets/js/modules/remesas/plugins.js',
            //'public/assets/js/modules/remesas/crear.vue.js',
        ));

    $data = array();
    $data['vista'] = "editar";

    $Remesas = Remesa::where(['uuid_remesa' => hex2bin(strtolower($uuid))])->first();
    $codigo = $Remesas->remesa;
    $data['ver'] = $ver;

    $data['aseguradora_id'] = $Remesas->aseguradora_id; 
    $ramos_id = explode(",", $Remesas->ramos_id);

    $fecha_desde = date('m/d/Y', strtotime($Remesas->fecha_desde));
    $fecha_hasta = date('m/d/Y', strtotime($Remesas->fecha_hasta));        

    $this->_js();
    $this->_css();

    $data['aseguradoras'] = Aseguradoras::where(['empresa_id' =>$this->id_empresa])->get();
    $clause = array('empresa_id' => $this->id_empresa);
    $data['menu_crear'] = $this->ramoRepository->listar_cuentas($clause);

    $ramosRoles = RolesUsuario::with(array('ramos'))->where(['usuario_id' => $this->usuario_id, 'empresa_id' => $this->id_empresa])->get();
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

    $this->assets->agregar_var_js(array(
        "vista" => 'editar',
        "fecha_desde" => $fecha_desde ,
        "fecha_hasta" => $fecha_hasta,
        "ramos_id" => $Remesas->ramos_id,
        "id_remesa" => $Remesas->id,
        "ver" => $ver,
        "estado" => $Remesas->estado
    ));

    $breadcrumb = array(
        "titulo" => '<i class="fa fa-archive"></i> Remesas Salientes: '.$codigo,
        "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
            1 => array("nombre" => '<b>Remesas Salientes</b>', "activo" => true, "url" => 'remesas/listar'),
            2 => array("nombre" => '<b>ver</b>', "activo" => true)
            ),
        "filtro" => false,
        "menu" => array()
        );

    $breadcrumb["menu"] = array(
        "url" => '',
        "clase" => '',
        "nombre" => "Acción"
    );

    $menuOpciones["#imprimirRemesaBtn"] = "Imprimir";
    $menuOpciones["#eliminarRemesaBtn"] = "Eliminar";
    $breadcrumb["menu"]["opciones"] = $menuOpciones;

    $this->template->agregar_titulo_header('Ver Remesa saliente');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);

}

public function imprimirRemesa($codigo_remesa = null, $id_aseguradora = null , $fecha_inicial = null, $fecha_final = null){

    $fecha_inicial = date('Y-d-m', strtotime($fecha_inicial));  
    $fecha_final = explode("-", $fecha_final);
    $fecha_final = $fecha_final[2]."-".$fecha_final[0]."-".$fecha_final[1];


    $Remesas = Remesa::where(['remesa' => $codigo_remesa])->first();
    $id_ramos = explode(",", $Remesas->ramos_id);
    $datosRemesa = array();
    $total_pago = 0;

    if($Remesas->estado == "Pagada"){
        $estado = $Remesas->remesa;
    }else{
        $estado = '';
    }

    
    foreach ($id_ramos as $key => $value) {

        $cobros = CobroFactura::whereRaw("DATE(cob_cobro_facturas.created_at) between '".$fecha_inicial."' AND '".$fecha_final."' AND cob_cobros.estado = 'aplicado' AND cob_cobros.num_remesa = '".$estado."' AND cob_cobro_facturas.id_ramo = ".$value)->join("cob_cobros", "cob_cobros.id", "=", "cob_cobro_facturas.cobro_id")->get(); 

        $sub_total_comision = 0;
        $sub_total_sobre_comision = 0;
        $sub_total_valorSobre_comision = 0;
        $sub_total_aseguradora = 0;
        $nombre_ramo = '';
        $cont = 0;

        foreach ($cobros as $key => $info) {
            $factura = $this->FacturaSeguro->GetFacturasRemesasSalientes($info->cobrable_id, $id_aseguradora);
            
            if($factura != NULL){

                $cont = 1;
                $prima_neta = number_format(($info->monto_pagado - $factura['impuestos']), 2, '.', '');
                if($factura['desc_comision'] == 'si'){

                    $valor_comision = round(($prima_neta * ($factura['comision']/100)),4);//round(($info->monto_pagado * ($factura['comision']/100)),2);
                    $valor_sobreComision = round(($prima_neta * ($factura['porcentaje_sobre_comision']/100)),2);//round(($info->monto_pagado * ($factura['porcentaje_sobre_comision']/100)),2);
                    $total_aseguradora = round(($info->monto_pagado - $valor_comision + $valor_sobreComision),2);
                    $sub_total_comision = number_format(($sub_total_comision + $valor_comision), 2, '.', '');
                    $sub_total_valorSobre_comision = number_format(($sub_total_valorSobre_comision + $valor_sobreComision), 2, '.', '');

                }else{

                    $valor_comision = "0.00";
                    $valor_sobreComision = "0.00";
                    $total_aseguradora = round($info->monto_pagado,2);
                    $sub_total_comision = "0.00";
                    $sub_total_valorSobre_comision = "0.00";  
                }
                $valor_comision = number_format($valor_comision, 2, '.', '');
                $valor_sobreComision = number_format($valor_sobreComision, 2, '.', '');
                $sub_total_aseguradora = number_format($sub_total_aseguradora + $total_aseguradora, 2, '.', '');
                $total_aseguradora = number_format($total_aseguradora, 2, '.', '');
                $monto_pagado = number_format($info->monto_pagado, 2, '.', '');
                $nombre_ramo = $factura['ramo'];
                array_push($datosRemesa, array("codigo" => $info->codigo, "numero_poliza" => $factura['numero'], 'inicio_vigencia' => date($factura["fecha_desde"]), 'fin_vigencia' => date($factura["fecha_hasta"]), 'prima_total' => '$ '.$monto_pagado, 'valor_descuento' => '$ '.$valor_comision, 'valor_sobreComision' => '$ '.$valor_sobreComision, 'total_aseguradora' => '$ '.$total_aseguradora, 'estilos' => 'font-weight: normal; text-align:center;' ));
            }
        }
        if($cont == 1){
            array_push($datosRemesa, array("codigo" => '', "numero_poliza" => '', 'inicio_vigencia' => '', 'fin_vigencia' => '', 'prima_total' => 'Subtotal '.$nombre_ramo , 'valor_descuento' => '$ '.$sub_total_comision, 'valor_sobreComision' => '$'.$sub_total_valorSobre_comision, 'total_aseguradora' => '$'.$sub_total_aseguradora, 'estilos' => 'font-weight: bold; background-color:#efefef; text-align:center;' ));
            $total_pago = number_format(($total_pago + $sub_total_aseguradora), 2, '.', ''); 
        }
        
    }
    array_push($datosRemesa, array("codigo" => '', "numero_poliza" => '', 'inicio_vigencia' => '', 'fin_vigencia' => '', 'prima_total' => 'Total ' , 'valor_descuento' => '', 'valor_sobreComision' => '', 'total_aseguradora' => '$ '.$total_pago, 'estilos' => 'font-weight: bold; background-color:#cccccc; text-align:center;'));

    $Remesas = Remesa::where(['remesa' => $codigo_remesa])->first();
    $aseguradora = Aseguradoras::where(['id' => $id_aseguradora])->first();
    $clause = array('empresa_id' => $this->id_empresa);
    $GetRamos = $this->ramoRepository->listar_cuentas($clause);
    $nombreRamos = array();
    foreach ($GetRamos as $value) {
        if(in_array($value['id'], $id_ramos)){
            array_push($nombreRamos, array('nombre' => $value['nombre']));
        }
    }

    $nombre = $codigo_remesa;
    $formulario = "formularioRemesa";

    $data = ['datos' => $Remesas, 'aseguradora' => $aseguradora, 'fecha_inicial' => $fecha_inicial, 'fecha_final' => $fecha_final, 'nombreRamos' => $nombreRamos, 'datosRemesa' => $datosRemesa];
    $dompdf = new Dompdf();
    $html = $this->load->view('pdf/' . $formulario, $data, true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream($nombre, array("Attachment" => false));
    exit(0);

}

public function descargarRemesa() {

    if (empty($_POST)) {
        exit();
    }
    $id = $this->input->post('id', true);

    if (empty($id)) {
        return false;
    }
    $csv = array();

    $Remesas = Remesa::where(['id' => $id])->first();

    $fecha_inicial = $Remesas->fecha_desde;  
    $fecha_final = $Remesas->fecha_hasta;
    $id_asegurado = $Remesas->aseguradora_id;
    $id_ramos = explode(",", $Remesas->ramos_id);
    $validacion_datos = 0;
    $total_pago = 0;
    $i = 0;
    foreach($id_ramos as $key => $info){

        if( $Remesas->estado == "En Proceso" || $Remesas->estado == "Por pagar" ){

            $cobros = CobroFactura::whereRaw("DATE(cob_cobro_facturas.created_at) between '".$fecha_inicial."' AND '".$fecha_final."' AND cob_cobros.estado = 'aplicado' AND cob_cobros.num_remesa = '' AND cob_cobro_facturas.id_ramo = ".$info."")->join("cob_cobros", "cob_cobros.id", "=", "cob_cobro_facturas.cobro_id")->get();

        }elseif($Remesas->estado == "Pagada" ){

            $cobros = CobroFactura::whereRaw("cob_cobros.estado = 'aplicado' AND cob_cobros.num_remesa = '".$Remesas->remesa."' AND cob_cobro_facturas.id_ramo = ".$info."")->join("cob_cobros", "cob_cobros.id", "=", "cob_cobro_facturas.cobro_id")->get();
        }

        $sub_total_comision = 0;
        $sub_total_valorSobre_comision = 0;
        $sub_total_aseguradora = 0;
        $nombre_ramo = '';

        foreach ($cobros as $key => $value) {

            $factura = $this->FacturaSeguro->GetFacturasRemesasSalientes($value->cobrable_id, $id_asegurado);              

            if($factura != NULL){

                $prima_neta = number_format(($value->monto_pagado - $factura['impuestos']), 2, '.', '');

                if($factura['desc_comision'] == 'si'){

                    $valor_comision =   round(($prima_neta * ($factura['comision']/100)),4); 
                    $valor_sobreComision = round(($prima_neta * ($factura['porcentaje_sobre_comision']/100)),2);
                    $total_aseguradora = round(($value->monto_pagado - $valor_comision + $valor_sobreComision),2);
                }else{

                    $valor_comision = "0.00";
                    $valor_sobreComision = "0.00";
                    $total_aseguradora = round($value->monto_pagado,2);
                }
                $comision = $factura['comision'];
                $sobre_comision = $factura['porcentaje_sobre_comision'];

                $sub_total_comision = $sub_total_comision + $valor_comision;
                $sub_total_valorSobre_comision = $sub_total_valorSobre_comision + $valor_sobreComision;
                $sub_total_aseguradora = $sub_total_aseguradora + $total_aseguradora;
                $nombre_ramo = $factura['ramo'];

                $valor_comision = number_format($valor_comision, 2, '.', '');
                $valor_sobreComision = number_format($valor_sobreComision, 2, '.', '');
                $total_aseguradora = number_format($total_aseguradora, 2, '.', '');

                $csvdata[$i]['numero_recibo'] = $value->codigo;
                $csvdata[$i]['numero_poliza'] = $factura['numero'];
                $csvdata[$i]['ramo'] = $factura['ramo'];
                $csvdata[$i]['asegurado'] = $factura['nombre'];
                $csvdata[$i]['Inicio_vigencia'] = date($factura["fecha_desde"]);
                $csvdata[$i]['fin_vigencia'] = date($factura["fecha_hasta"]);
                $csvdata[$i]['prima_cobrada'] = '$'.number_format($value->monto_pagado, 2, '.', '');
                $csvdata[$i]['impuesto'] = '$'.$factura['impuestos'];
                $csvdata[$i]['prima_neta'] = '$'.$prima_neta;
                $csvdata[$i]['porcentaje_comision'] = $comision.'%';
                $csvdata[$i]['comision_descontada'] = '$'.$valor_comision;
                $csvdata[$i]['porcentaje_sobre_comision'] = $sobre_comision.'%';
                $csvdata[$i]['sobre_comision_descontada'] = '$'.$valor_sobreComision;
                $csvdata[$i]['pago_aseguradora'] = '$'.$total_aseguradora;
                $i++;
                $validacion_datos = 1;
            }
        }

        if($validacion_datos == 1){

            $sub_total_comision = number_format($sub_total_comision, 2, '.', '');
            $sub_total_valorSobre_comision = number_format($sub_total_valorSobre_comision, 2, '.', '');
            $sub_total_aseguradora = number_format($sub_total_aseguradora, 2, '.', '');

            $csvdata[$i]['numero_recibo'] = '';
            $csvdata[$i]['numero_poliza'] = '';
            $csvdata[$i]['ramo'] = '';
            $csvdata[$i]['asegurado'] = '';
            $csvdata[$i]['Inicio_vigencia'] = '';
            $csvdata[$i]['fin_vigencia'] = '';
            $csvdata[$i]['prima_cobrada'] = '';
            $csvdata[$i]['impuesto'] = '';
            $csvdata[$i]['prima_neta'] = '';
            $csvdata[$i]['porcentaje_comision'] = $nombre_ramo ;
            $csvdata[$i]['comision_descontada'] = '$'.$sub_total_comision;
            $csvdata[$i]['porcentaje_sobre_comision'] = '';
            $csvdata[$i]['sobre_comision_descontada'] = '$'.$sub_total_valorSobre_comision;
            $csvdata[$i]['pago_aseguradora'] = '$'.$sub_total_aseguradora;
            $i++;
            $total_pago = $total_pago + $sub_total_aseguradora;
        }    
        $validacion_datos = 0;
    }

    $total_pago =  number_format($total_pago, 2, '.', '');
    $csvdata[$i]['numero_recibo'] = '';
    $csvdata[$i]['numero_poliza'] = '';
    $csvdata[$i]['ramo'] = '';
    $csvdata[$i]['asegurado'] = '';
    $csvdata[$i]['Inicio_vigencia'] = '';
    $csvdata[$i]['fin_vigencia'] = '';
    $csvdata[$i]['prima_cobrada'] = '';
    $csvdata[$i]['impuesto'] = '';
    $csvdata[$i]['prima_neta'] = '';
    $csvdata[$i]['porcentaje_comision'] = 'Total' ;
    $csvdata[$i]['comision_descontada'] = '';
    $csvdata[$i]['porcentaje_sobre_comision'] = '';
    $csvdata[$i]['sobre_comision_descontada'] = '';
    $csvdata[$i]['pago_aseguradora'] = '$'.$total_pago;
    

    //we create the CSV into memory
    $csv = Writer::createFromFileObject(new SplTempFileObject());
    $headers = [
    'No. Recibo',
    'No. Póliza',
    'Ramo',
    'Aseguradoro',
    'Inicio vigencia',
    'Fin vigencia',
    'Prima cobrada',
    'impuesto',
    'Prima Neta',
    '% Comisión',
    'Comisión descontada',
    '% Sobre comisión',
    'S.Comisión descontada',
    'Pago a aseguradora'
    ];
    $decodingHeaders = array_map("utf8_decode", $headers);
    $csv->insertOne($decodingHeaders);
    $csv->insertAll($csvdata);
    $csv->output("RemesaSalientes-" . date('y-m-d') . ".csv");
    exit();
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
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css',
        'public/assets/css/modules/stylesheets/cobros.css'
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
        'public/assets/js/modules/remesas/routes.js',
                //'public/assets/js/default/grid.js',
        ));
}

public function ajax_cambiar_estado_remesa() {

    $campos = $this->input->post('campo');
    $ids = $campos['ids'];
    $estado_anterior = $campos['campo_anterior'];
    $empresa_id = $this->empresa_id;
    $campo = ['estado'=>$campos['estado'], 'fecha' => date('Y-m-d')];
    
    try {
        if($campos['estado'] == "Anulado"){
            $datosPagos = Pagos::where(['empezable_id' => $ids])->get();
            foreach ($datosPagos as $key => $value) {
               if($value['estado'] != "aplicado" && $value['estado'] != "anulado"){
                    $datosPagos->update(['estado' => 'anulado']);
                    $reclamo = Remesa::where('empresa_id', $empresa_id)->whereIn('id',$ids)->update($campo);
                }else{
                    $reclamo = 0;
                }
            }
            
        }else{
            $reclamo = Remesa::where('empresa_id', $empresa_id)->whereIn('id',$ids)->update($campo);
            $remesas = Remesa::whereIn('id',$ids)->first();
            $this->crear_pago_seguros($remesas,$estado_anterior); 
        }
        
        $msg = $reclamo;

    } catch (\Exception $e) {
        $msg = $e->getMessage() . "\r\n";
    }

    print json_encode($msg);
    exit;
}


public function crear_pago_seguros($datosRemesas = null, $estado_anterior_remesa = null){

    $cuenta_remesa_saliente = CuentaRemesaSaliente::where(['empresa_id' => $this->id_empresa])->first();

    if(isset($datosRemesas) && !empty($datosRemesas)){
        if( ($estado_anterior_remesa == "En Proceso" && ($datosRemesas->estado == "Por Pagar" || $datosRemesas->estado == "Por pagar" ) ) || ($estado_anterior_remesa == '' && $datosRemesas->estado == "Por Pagar" ) ){
            $datosPagos['codigo'] = Pagos::whereEmpresaId($this->id_empresa)->count() + 1;
            $datosPagos['fecha_pago'] = date('d/m/Y');
            $datosPagos['proveedor_id'] = $datosRemesas->aseguradora_id;
            $datosPagos['monto_pagado'] = number_format($datosRemesas->monto,4, '.', '');
            $datosPagos['cuenta_id'] = 0;
            $datosPagos['empresa_id'] = $this->id_empresa;
            $datosPagos['estado'] = 'por_aplicar';
            $datosPagos['formulario'] = 'remesa';
            $datosPagos['depositable_type'] = 'banco';
            $datosPagos['depositable_id'] = $cuenta_remesa_saliente->cuenta_id;
            $datosPagos['empezable_type'] = 'remesas_salientes';
            $datosPagos['empezable_id'] = $datosRemesas->id;

            $pagosSeguros =  Pagos::create($datosPagos);
            $datosPagoMetodo['pago_id'] = $pagosSeguros->id;
            $datosPagoMetodo['tipo_pago'] = 'efectivo';
            $datosPagoMetodo['total_pagado'] = number_format($datosRemesas->monto,4, '.', '');
            $pagosSegurosmetodos = PagosMetodos::create($datosPagoMetodo);

            $cobrosRemesas = Remesas_cobros::where(['id_remesa' => $datosRemesas->id])->get();
            foreach($cobrosRemesas as $value){
                $datosPagables['pago_id'] = $pagosSeguros->id;
                $datosPagables['pagable_id'] = $value['id_cobro'];
                $datosPagables['pagable_type'] = 'Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros';
                $datosPagables['monto_pagado'] = $value['valor_cobro'];
                $datosPagables['empresa_id'] = $this->id_empresa;
                PagosPagables::create($datosPagables);
            }
            
        }elseif( ($estado_anterior_remesa == "Por Pagar" || $estado_anterior_remesa == "Por pagar") && $datosRemesas->estado == "En Proceso"){

            Pagos::where(['empezable_id' => $datosRemesas->id])->update(['estado' => 'anulado']);
        }
    }  
}

public function ajax_eliminar_cobro(){
    $ids_cobros = $this->input->post('ids_cobros');
    try {
        $cobros = Remesas_cobros::whereIn('id_cobro',$ids_cobros)->delete();        
        $msg = $cobros;
    } catch (\Exception $e) {
        $msg = $e->getMessage() . "\r\n";
    }
    echo json_encode($msg);
    exit;
}

}